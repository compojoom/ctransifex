<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * @property mixed update
 */
class com_ctransifexInstallerScript extends CompojoomInstaller
{
	/*
	 * The release value to be displayed and checked against throughout this file.
	 */
	public $release = '3.0';
	public $minimum_joomla_release = '2.5.6';
	public $extension = 'com_ctransifex';
	private $type = '';

	/**
	 * method to run before install
	 * @param $type
	 * @param $parent
	 */
	public function preflight($type, $parent) {
		$this->update = $this->getParam('version');
	}
	/**
	 * method to run after an install/update/discover method
	 *
	 * @param $type
	 * @param $parent
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		$this->loadLanguage();

		if($type == 'update') {
			switch ($this->update) {
				case '1.0':
					ctransifexInstallerDatabase::updateTo1_0();
					break;
			}
		}

		echo $this->displayInfoInstallation();
	}

}

class ctransifexInstallerDatabase {
	public static function updateTo1_0() {
		$db = JFactory::getDbo();
		$db->setQuery(
			'ALTER TABLE `#__ctransifex_projects`
			ADD `params` LONGTEXT NOT NULL;');
		$db->execute();

		$db->setQuery(
			'ALTER TABLE `#__ctransifex_languages`
			ADD `raw_data` LONGTEXT NOT NULL;');
		$db->execute();
	}
}

class CompojoomInstaller
{
	public function loadLanguage()
	{
		$extension = $this->extension;
		$jlang =& JFactory::getLanguage();
		$path = $this->parent->getParent()->getPath('source') . '/administrator';
		$jlang->load($extension, $path, 'en-GB', true);
		$jlang->load($extension, $path, $jlang->getDefault(), true);
		$jlang->load($extension, $path, null, true);
		$jlang->load($extension . '.sys', $path, 'en-GB', true);
		$jlang->load($extension . '.sys', $path, $jlang->getDefault(), true);
		$jlang->load($extension . '.sys', $path, null, true);
	}

	public function installModules($modulesToInstall)
	{
		$src = $this->parent->getParent()->getPath('source');
		$status = array();
		// Modules installation
		if (count($modulesToInstall)) {
			foreach ($modulesToInstall as $folder => $modules) {
				if (count($modules)) {
					foreach ($modules as $module => $modulePreferences) {
						// Install the module
						if (empty($folder)) {
							$folder = 'site';
						}
						$path = "$src/modules/$module";
						if ($folder == 'admin') {
							$path = "$src/administrator/modules/$module";
						}
						if (!is_dir($path)) {
							continue;
						}
						$db = JFactory::getDbo();
						// Was the module alrady installed?
						$sql = 'SELECT COUNT(*) FROM #__modules WHERE `module`=' . $db->Quote($module);
						$db->setQuery($sql);
						$count = $db->loadResult();
						$installer = new JInstaller;
						$result = $installer->install($path);
						$status[] = array('name' => $module, 'client' => $folder, 'result' => $result);
						// Modify where it's published and its published state
						if (!$count) {
							list($modulePosition, $modulePublished) = $modulePreferences;
							$sql = "UPDATE #__modules SET position=" . $db->Quote($modulePosition);
							if ($modulePublished) $sql .= ', published=1';
							$sql .= ', params = ' . $db->quote($installer->getParams());
							$sql .= ' WHERE `module`=' . $db->Quote($module);
							$db->setQuery($sql);
							$db->execute();

//	                        get module id
							$db->setQuery('SELECT id FROM #__modules WHERE module = ' . $db->quote($module));
							$moduleId = $db->loadObject()->id;

							// insert the module on all pages, otherwise we can't use it
							$query = 'INSERT INTO #__modules_menu(moduleid, menuid) VALUES (' . $db->quote($moduleId) . ' ,0 );';
							$db->setQuery($query);

							$db->execute();
						}
					}
				}
			}
		}
		return $status;
	}

	public function uninstallModules($modulesToUninstall)
	{
		$status = array();
		if (count($modulesToUninstall)) {
			$db = JFactory::getDbo();
			foreach ($modulesToUninstall as $folder => $modules) {
				if (count($modules)) {

					foreach ($modules as $module => $modulePreferences) {
						// Find the module ID
						$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = '
							. $db->Quote($module) . ' AND `type` = "module"');

						$id = $db->loadResult();
						// Uninstall the module
						$installer = new JInstaller;
						$result = $installer->uninstall('module', $id, 1);
						$status[] = array('name' => $module, 'client' => $folder, 'result' => $result);
					}
				}
			}
		}
		return $status;
	}

	public function installPlugins($plugins)
	{
		$src = $this->parent->getParent()->getPath('source');

		$db = JFactory::getDbo();
		$status = array();

		foreach ($plugins as $plugin => $published) {
			$parts = explode('_', $plugin);
			$pluginType = $parts[1];
			$pluginName = $parts[2];

			$path = $src . "/plugins/$pluginType/$pluginName";

			$query = "SELECT COUNT(*) FROM  #__extensions WHERE element=" . $db->Quote($pluginName) . " AND folder=" . $db->Quote($pluginType);

			$db->setQuery($query);
			$count = $db->loadResult();

			$installer = new JInstaller;
			$result = $installer->install($path);
			$status[] = array('name' => $plugin, 'group' => $pluginType, 'result' => $result);

			if ($published && !$count) {
				$query = "UPDATE #__extensions SET enabled=1 WHERE element=" . $db->Quote($pluginName) . " AND folder=" . $db->Quote($pluginType);
				$db->setQuery($query);
				$db->execute();
			}
		}

		return $status;
	}

	public function uninstallPlugins($plugins)
	{
		$db = JFactory::getDbo();
		$status = array();

		foreach ($plugins as $plugin => $published) {
			$parts = explode('_', $plugin);
			$pluginType = $parts[1];
			$pluginName = $parts[2];
			$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = ' . $db->Quote($pluginName) . ' AND `folder` = ' . $db->Quote($pluginType));

			$id = $db->loadResult();

			if ($id) {
				$installer = new JInstaller;
				$result = $installer->uninstall('plugin', $id, 1);
				$status[] = array('name' => $plugin, 'group' => $pluginType, 'result' => $result);
			}
		}

		return $status;
	}

	/*
		  * get a variable from the manifest file (actually, from the manifest cache).
		  */
	public function getParam($name)
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = ' . $db->quote($this->extension));
		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}

	public function addCss() {
		$css = '<style type="text/css">
					.compojoom-info {
						background-color: #D9EDF7;
					    border-color: #BCE8F1;
					    color: #3A87AD;
					    border-radius: 4px 4px 4px 4px;
					    padding: 8px 35px 8px 14px;
					    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
					    margin-bottom: 18px;
					}

				</style>
				';
		return $css;
	}

	public function displayInfoInstallation() {
		$html[] = $this->addCSS();
		$html[] = '<div class="compojoom-info alert alert-info">'
			. JText::_('COM_CTRANSIFEX_INSTALLATION_SUCCESS') . '</div>';

		$html[] .= '<p>'.JText::_('COM_CTRANSIFEX_LATEST_NEWS_PROMOTIONS').':</p>';
		$html[] .= '<table><tr><td>'. JText::_('COM_CTRANSIFEX_LIKE_FB').': </td><td><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fcompojoom&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=true&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=119257468194823" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true"></iframe></td></tr>
							<tr><td>'.JText::_('COM_CTRANSIFEX_FOLLOW_TWITTER').': </td><td><a href="https://twitter.com/compojoom" class="twitter-follow-button" data-show-count="false">Follow @compojoom</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td></tr></table>';


		return implode('', $html);
	}

	public function renderModuleInfoInstall($modules) {
		$rows = 0;

		$html = array();
		if (count($modules)) {
			$html[] = '<table>';
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) .'_MODULE') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) .'_CLIENT') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) .'_STATUS') . '</th>';
			$html[] = '</tr>';
			foreach ($modules as $module) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $module['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($module['client']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color:' . (($module['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($module['result']) ? JText::_(strtoupper($this->extension) .'_MODULE_INSTALLED') : JText::_(strtoupper($this->extension) .'_MODULE_NOT_INSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}
			$html[] = '</table>';
		}


		return implode('', $html);
	}

	public function renderModuleInfoUninstall($modules)
	{
		$rows = 0;
		$html = array();
		if (count($modules)) {
			$html[] = '<table>';
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_MODULE') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_CLIENT') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_STATUS') . '</th>';
			$html[] = '</tr>';
			foreach ($modules as $module) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $module['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($module['client']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color:' . (($module['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($module['result']) ? JText::_(strtoupper($this->extension) . '_MODULE_UNINSTALLED') : JText::_(strtoupper($this->extension) . '_MODULE_COULD_NOT_UNINSTALL');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}
			$html[] = '</table>';
		}

		return implode('', $html);
	}

	public function renderPluginInfoInstall($plugins)
	{
		$rows = 0;
		$html[] = '<table>';
		if (count($plugins)) {
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_PLUGIN') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_GROUP') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_STATUS') . '</th>';
			$html[] = '</tr>';
			foreach ($plugins as $plugin) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $plugin['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($plugin['group']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color: ' . (($plugin['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($plugin['result']) ? JText::_(strtoupper($this->extension) . '_PLUGIN_INSTALLED') : JText::_(strtoupper($this->extension) . 'PLUGIN_NOT_INSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}
		}
		$html[] = '</table>';

		return implode('', $html);
	}

	public function renderPluginInfoUninstall($plugins)
	{
		$rows = 0;
		$html = array();
		if (count($plugins)) {
			$html[] = '<table>';
			$html[] = '<tbody>';
			$html[] = '<tr>';
			$html[] = '<th>Plugin</th>';
			$html[] = '<th>Group</th>';
			$html[] = '<th></th>';
			$html[] = '</tr>';
			foreach ($plugins as $plugin) {
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $plugin['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($plugin['group']) . '</td>';
				$html[] = '<td>';
				$html[] = '	<span style="color:' . (($plugin['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($plugin['result']) ? JText::_(strtoupper($this->extension) . '_PLUGIN_UNINSTALLED') : JText::_(strtoupper($this->extension) . '_PLUGIN_NOT_UNINSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = ' </tr> ';
			}
			$html[] = '</tbody > ';
			$html[] = '</table > ';
		}

		return implode('', $html);
	}

	/**
	 * method to run before an install/update/discover method
	 *
	 * @param $type
	 * @param $parent
	 * @return void
	 */
	public function preflight($type, $parent)
	{
		$jversion = new JVersion();

		// Extract the version number from the manifest file
		$this->release = $parent->get("manifest")->version;

		// Find mimimum required joomla version from the manifest file
		$this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;

		if (version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt')) {
			Jerror::raiseWarning(null, 'Cannot install ' . $this->extension . ' in a Joomla release prior to '
				. $this->minimum_joomla_release);
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update') {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if (!strstr($this->release, 'git_')) {
				if (version_compare($this->release, $oldRelease, 'lt')) {
					Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
					return false;
				}
			}
		}

	}

	/**
	 * method to update the component
	 *
	 * @param $parent
	 * @return void
	 */
	public function update($parent)
	{
		$this->parent = $parent;
		// Delete old install.xml
		jimport('joomla.filesystem.file');
		$file	= JPATH_ADMINISTRATOR.'/components/com_ctransifex/assets/install.xml';
		if(JFile::exists($file))
		{
			JFile::delete($file);
		}
	}

	/**
	 * method to install the component
	 *
	 * @param $parent
	 * @return void
	 */
	public function install($parent)
	{
		$this->parent = $parent;

	}

}