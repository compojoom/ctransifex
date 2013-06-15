<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelitem');

/**
 * Class CtransifexModelLanguage
 *
 * @since  1.5
 */
class CtransifexModelLanguage extends JModelItem
{
	/**
	 * Populate State
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('zip');
		$this->setState('zip.id', $pk);
		$this->setState('project.id', $app->input->getInt('project'));
	}

	/**
	 * Gets the Language item
	 *
	 * @return object
	 */
	public function getItem()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')->from('#__ctransifex_zips')
			->where('id=' . $db->q($this->getState('zip.id')));

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get language resources for lang
	 *
	 * @return mixed
	 */
	public function getResources()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('l.*, r.resource_name')->from('#__ctransifex_languages AS l')
			->leftJoin('#__ctransifex_zips AS z ON z.lang_name = l.lang_name')
			->leftJoin('#__ctransifex_resources as r ON r.id = l.resource_id')
			->where('z.id=' . $db->q($this->getState('zip.id')))
			->where('l.project_id = ' . $db->q($this->getState('project.id')));

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
