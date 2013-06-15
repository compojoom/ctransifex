<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerlegacy');

/**
 * Class ctransifexControllerWebhooks
 *
 * @since  1
 */
class CtransifexControllerWebhooks extends JControllerLegacy
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  - config options
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'ctransifexModel');
	}

	/**
	 * This function needs to be simplified a lot... Getting a headache just by looking at it.
	 *
	 * @throws Exception
	 *
	 * @return bool|void
	 */
	public function webhook()
	{
		$config = JComponentHelper::getParams('com_ctransifex');
		$input = JFactory::getApplication()->input;

		if ($input->getString('key') != $config->get('transifex_webhook_key'))
		{
			return false;
		}

		JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
		JLog::add(serialize($input));

		$projectId = $input->getInt('project_id');

		// Get the project
		$projectModel = $this->getModel('Project', 'ctransifexModel');
		$this->project = $project = $projectModel->getItem($projectId);


		$langName = ctransifexHelperTransifex::getJLangCode($input->getString('language'), parse_ini_string($this->project->transifex_config, true));

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')->from('#__ctransifex_languages AS l')
			->leftJoin('#__ctransifex_resources AS r ON l.project_id = r.project_id')
			->where('l.project_id=' . $db->q($projectId))
			->where('r.resource_name=' . $db->q($input->getString('resource')))
			->where('l.lang_name=' . $db->q($langName));

		$db->setQuery($query, 0, 1);
		$language = $db->loadObject();

		// If we have a language just update the row
		if ($language)
		{
			$query->clear();
			$query->update('#__ctransifex_languages')->set('completed=' . $db->q($input->getInt('percent')))
				->where('resource_id=' . $db->q($language->resource_id))
				->where('lang_name=' . $db->q($langName));
			$db->setQuery($query);
			$db->execute();

			// TODO: create zip
		}
		else
		{
			// If we don't have a language we need to create a new row for it
			// first we need the resource id
			$query->clear();
			$query->select('id')->from('#__ctransifex_resources')
				->where('project_id=' . $db->q($projectId));
			$db->setQuery($query);

			$resource = $db->loadObject();

			if (!$resource)
			{
				throw new Exception('Resource doesn\'t exist');
			}

			// Now insert the new language
			$query->clear();
			$query->insert('#__ctransifex_languages')->columns(array('project_id', 'resource_id', 'lang_name', 'completed'))
				->values($db->q($projectId) . ',' . $db->q($resource->id), ',' . $db->q($langName) . ',' . $db->q($input->getString('percent')));
			$db->setQuery($query);
			$db->execute();
		}


		// Remove the zip file
		$query->clear();
		$query->delete('#__ctransifex_zips')->where('project_id=' . $db->q($project->id))
			->where('lang_name=' . $db->q($langName));
		$db->setQuery($query);
		$db->execute();

		JLog::add($query->dump());
		$this->updateLang($langName, $project);


		jexit();
	}

	/**
	 * Updates a language pack
	 *
	 * @param   string  $langName  - the lang name
	 * @param   object  $project   - the project object
	 *
	 * @return bool
	 */
	private function updateLang($langName, $project)
	{
		$model = $this->getModel('Language', 'ctransifexModel', array('project' => $project));
		$resources = $model->getResourcesForLang($langName);
		$input = JFactory::getApplication()->input;

		foreach ($resources as $resource)
		{
			if (!$this->langFile($project, $resource->resource_name, $input->getString('language')))
			{
				JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
				JLog::add('something went wrong when we tried to get the ' . $langName . ' for the ' . $resource->resource_name . ' resource');
			}
		}

		if (ctransifexHelperPackage::package($langName, $project))
		{
			$packageModel = $this->getModel('Package', 'ctransifexModel', array('project' => $project));
			$packageModel->add($resources, $langName);
		}

		return true;
	}

	/**
	 * Get the language files from transifex
	 *
	 * @param   object  $project   - the project object
	 * @param   string  $resource  - the resource name
	 * @param   string  $txLang    - the transifex lang name
	 *
	 * @return bool
	 */
	public function langFile($project, $resource, $txLang)
	{
		$config = parse_ini_string($project->transifex_config, true);

		$path = $project->transifex_slug . '/resource/' . $resource . '/translation/' . $txLang . '/?file';

		$file = ctransifexHelperTransifex::getData($path);

		if (isset($file['info']) && $file['info']['http_code'] == 200)
		{
			if (isset($config[$project->transifex_slug . '.' . $resource]))
			{
				$jlang = ctransifexHelperTransifex::getJLangCode($txLang, $config);

				return ctransifexHelperPackage::saveLangFile($file, $jlang, $project, $resource, $config);
			}
		}

		return false;
	}
}
