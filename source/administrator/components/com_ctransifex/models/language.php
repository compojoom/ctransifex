<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellegacy');

/**
 * Class ctransifexModelLanguage
 *
 * @version 1
 */
class ctransifexModelLanguage extends JModelLegacy
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  - array with config options
	 */
	public function __construct(array $config = array())
	{
		if (isset($config['project']))
		{
			$this->projectId = $config['project']->id;
			$this->project = $config['project'];
		}

		if (isset($config['resource']))
		{
			$this->resourceId = $this->getResource($config['resource']);
		}

		parent::__construct($config);
	}

	/**
	 * Add languages to DB
	 *
	 * @param   array  $languages  - the languages info
	 *
	 * @return void
	 */
	public function add($languages = array())
	{
		// Now add the resources
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$config = parse_ini_string($this->project->transifex_config, true);

		foreach ($languages as $key => $language)
		{
			$langCode = ctransifexHelperTransifex::getJLangCode($key, $config);

			if ($langCode)
			{
				$values[] = $db->q($this->projectId) . ','
					. $db->q($this->resourceId) . ','
					. $db->q($langCode) . ','
					. $db->q($language->completed) . ','
					. $db->q(json_encode($language));
			}
		}

		$query->insert('#__ctransifex_languages')
			->columns(
				array(
					$db->qn('project_id'),
					$db->qn('resource_id'),
					$db->qn('lang_name'),
					$db->qn('completed'),
					$db->qn('raw_data')
				)
			)->values($values);

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Gets resource information
	 *
	 * @param   string  $name  - the resource name
	 *
	 * @return mixed
	 */
	private function getResource($name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id')->from('#__ctransifex_resources')->where($db->qn('resource_name') . '=' . $db->q($name))
			->where($db->qn('project_id') . '=' . $db->q($this->projectId));
		$db->setQuery($query, 0, 1);

		return $db->loadObject()->id;
	}

	/**
	 * Gets language info for a resource
	 *
	 * @param   string  $jlang  - the joomla lang
	 *
	 * @return mixed
	 */
	public function getResourcesForLang($jlang)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
			array(
				$db->qn('l.resource_id'),
				$db->qn('r.resource_name'),
				$db->qn('l.lang_name'),
				$db->qn('l.completed'),
				$db->qn('l.raw_data')
			)
		)
			->from('#__ctransifex_languages AS l')
			->leftJoin('#__ctransifex_resources AS r ON l.resource_id = r.id')
			->where($db->qn('l.lang_name') . '=' . $db->q($jlang))
			->where($db->qn('l.project_id') . '=' . $db->q($this->projectId));

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * @param   string  $jLang         - the joomla lang
	 * @param   string  $resourceName  - the resource name
	 *
	 * @return mixed
	 */
	public function getLangInfo($jLang, $resourceName)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')->from('#__ctransifex_languages as l')
			->leftJoin('#__ctransifex_resources as r ON r.id = l.resource_id')
			->where($db->qn('r.resource_name') . '=' . $db->q($resourceName))
			->where($db->qn('l.project_id') . '=' . $db->q($this->projectId))
			->where($db->qn('l.lang_name') . '=' . $db->q($jLang));

		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}
}
