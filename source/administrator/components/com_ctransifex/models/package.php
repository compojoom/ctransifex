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
 * Class ctransifexModelPackage
 *
 * @since  1
 */
class CtransifexModelPackage extends JModelLegacy
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  - An array of configuration options (name, state, dbo, table_path, ignore_request).
	 */
	public function __construct(array $config = array())
	{
		if (isset($config['project']))
		{
			$this->projectId = $config['project']->id;
			$this->project = $config['project'];
		}

		parent::__construct($config);
	}

	/**
	 * Ads the lang pack to the DB
	 *
	 * @param   string  $language       - the joomla lang
	 * @param   string  $transifexLang  - the transifexLang
	 *
	 * @return void
	 */
	public function add($resources, $language)
	{
		// Now add the resources
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$totalStrings = 0;
		$translatedStrings = 0;

		foreach ($resources as $resource)
		{
			$stats = json_decode($resource->raw_data);

			if ($stats)
			{
				$translatedStrings += (int) $stats->translated_entities;
				$totalStrings += (int) $stats->translated_entities + (int) $stats->untranslated_entities;

			}
		}

		// Prevent division by zero
		if (empty($totalStrings))
		{
			$completed = 0;
		}
		else
		{
			$completed = (int) (($translatedStrings / $totalStrings) * 100);
		}

		$values = $db->q($this->projectId) .
			',' . $db->q($language) .
			',' . $db->q($completed) .
			',' . $db->q(JFactory::getDate()->toSql());

		$query->insert('#__ctransifex_zips')
			->columns(
				array(
					$db->qn('project_id'),
					$db->qn('lang_name'),
					$db->qn('completed'),
					$db->qn('created')
				)
			)->values($values);

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Count the resources
	 *
	 * @return int
	 */
	public function countResources()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery('true');

		$query->select('COUNT(id) as count')->from('#__ctransifex_resources')->where('project_id = ' . $db->q($this->projectId));

		$db->setQuery($query);

		return $db->loadObject()->count;
	}
}
