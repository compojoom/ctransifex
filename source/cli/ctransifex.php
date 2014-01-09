<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       09.01.14
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// We are a valid entry point.
const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * Class CtransifexCli
 *
 * @since  1.5
 */
class CtransifexCli extends JApplicationCli
{
	/**
	 * Main entry point for executing the cronjob
	 *
	 * @return void
	 */
	public function execute()
	{
		$addResource = array();

		// Fool the system into thinking we are running as JSite with ctransifex as the active component
		$_SERVER['HTTP_HOST'] = 'domain.com';
		JFactory::getApplication('site');

		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_BASE . '/administrator/components/com_ctransifex');

		JLoader::discover('ctransifexTable', JPATH_COMPONENT_ADMINISTRATOR . '/tables');
		JLoader::discover('ctransifexHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'ctransifexModel');

		$projectsModel = JModelLegacy::getInstance('Projects', 'ctransifexModel');
		$projectsModel->setState('filter.state', 1);
		$projectModel = JModelLegacy::getInstance('Project', 'ctransifexModel');

		// Get all projects in the DB
		$projects = $projectsModel->getItems();

		foreach ($projects as $value)
		{
			$project = $projectModel->getItem($value->id);

			// Get all resources for the project
			$resources = $this->handleResources($project);

			// If something goes wrong with the resources show the error and exit
			if (!$resources)
			{
				print $this->error;

				return false;
			}

			$packsToCreate = array();

			// Go through every resource and find out which lang packs we have to create
			foreach ($resources as $resource)
			{
				$languages = $this->handleLanguages($project, $resource);

				if (!is_array($languages))
				{
					print $this->error;

					return false;
				}

				$packsToCreate = array_merge($packsToCreate, $languages);
			}

			// Create the lang packs
			foreach ($packsToCreate as $key => $value)
			{
				$this->handlePackages($key, $project);
			}

			$this->out("Project: " . $project->title . ". We've generated " . count($packsToCreate) . ' packs.');
		}

		$this->out('Cron job complete');
	}

	/**
	 * Gets a lang file from transifex
	 *
	 * @param   string  $resource  - the resource name
	 * @param   string  $lang      - the transifex lang name
	 * @param   object  $project   - the project info
	 *
	 * @return bool
	 */
	public function langFile($resource, $lang, $project)
	{
		$config = parse_ini_string($project->transifex_config, true);

		$path = $project->transifex_slug . '/resource/' . $resource . '/translation/' . $lang . '/?file';

		$file = ctransifexHelperTransifex::getData($path);

		if (isset($file['info']) && $file['info']['http_code'] == 200)
		{
			if (isset($config[$project->transifex_slug . '.' . $resource]))
			{
				$jlang = ctransifexHelperTransifex::getLangCode($lang, $config);

				return ctransifexHelperPackage::saveLangFile($file, $jlang, $project, $resource, $config);
			}
		}

		return false;
	}

	/**
	 * Get all resources from transifex. Match them with our local tx .config and add to the db if necessary
	 *
	 * @param   object  $project  - the project object
	 *
	 * @return array|bool
	 */
	public function handleResources($project)
	{
		$addResource = array();
		$response['data'] = array();
		$resources = ctransifexHelperTransifex::getData($project->transifex_slug . '/resources/');

		if (isset($resources['info']) && $resources['info']['http_code'] != 200)
		{
			$this->error = 'Transifex response: ' . $resources['data'];

			return false;
		}
		else
		{
			$resources = json_decode($resources['data']);

			// Calculate the response data
			foreach ($resources as $resource)
			{
				// Only add the resource to the response data if it's defined in the Transifex config
				$config = parse_ini_string($project->transifex_config, true);

				if (isset($config[$project->transifex_slug . '.' . $resource->name]))
				{
					$response['data'][] = $resource->slug;
				}
			}

			// Get only the resouce names
			$dbResourceNames = array_keys($this->getResourcesForProject($project));


			foreach ($response['data'] as $responseData)
			{
				if (!in_array($responseData, $dbResourceNames))
				{
					$addResource[] = $responseData;
				}
			}

			if (count($addResource))
			{
				$model = JModelLegacy::getInstance('Resource', 'ctransifexModel', array('project_id' => $project->id));
				$model->add($addResource, false);
			}
		}

		return $response['data'];
	}

	/**
	 * Get all resources for a given project
	 *
	 * @param   object  $project  - the project object
	 *
	 * @return mixed
	 */
	public function getResourcesForProject($project)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get all resources for the project from the db
		$query->select('id,resource_name')->from('#__ctransifex_resources')
			->where('project_id=' . $db->q($project->id));
		$db->setQuery($query);

		return $db->loadObjectList('resource_name');
	}

	/**
	 * Get all language stats for a given resource name
	 * On the base of the percentage complete - we add the language to the db or update it
	 *
	 * @param   object  $project   - project object
	 * @param   string  $resource  - resource name
	 *
	 * @return array|bool
	 */
	public function handleLanguages($project, $resource)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery();

		$dbResources = $this->getResourcesForProject($project);

		$projectConfig = parse_ini_string($project->transifex_config, true);
		$needsUpdate = array();
		$needsInsert = array();
		$langsInDb = array();
		$namesMap = array();
		$path = $project->transifex_slug . '/resource/' . $resource . '/stats/';
		$txData = (ctransifexHelperTransifex::getData($path));

		if (isset($txData['info']) && $txData['info']['http_code'] == 200)
		{
			$stats = get_object_vars(json_decode($txData['data']));
			$names = array_keys($stats);

			// Lang codes
			foreach ($names as $nvalue)
			{
				$jLang = ctransifexHelperTransifex::getLangCode($nvalue, $projectConfig);

				if ($jLang)
				{
					$namesMap[$nvalue] = $db->q($jLang);
				}
			}

			// Get the available languages for the given resource id and lang name
			$query->clear();
			$query->select('*')->from('#__ctransifex_languages')
				->where('project_id=' . $db->q($project->id))
				->where('lang_name IN (' . implode(',', $namesMap) . ')')
				->where('resource_id = ' . $db->q($dbResources[$resource]->id));
			$db->setQuery($query);

			$dbLangs = $db->loadObjectList();

			// Create an array with the lang names from the db
			foreach ($dbLangs as $valueDbLangs)
			{
				$langsInDb[] = $valueDbLangs->lang_name;
			}

			foreach ($namesMap as $namesMapKey => $namesMapValue)
			{
				$stat = $stats[$namesMapKey];
				$jLang = str_replace("'", '', $namesMapValue);

				foreach ($dbLangs as $valueDbLangs)
				{
					if ($jLang == $valueDbLangs->lang_name)
					{
						if (str_replace('%', '', $stat->completed) != $valueDbLangs->completed)
						{
							$needsUpdate[$namesMapKey] = $stat;
							unset($stats[$namesMapKey]);
						}
					}
				}

				if (!in_array($jLang, $langsInDb))
				{
					$needsInsert[$namesMapKey] = $stat;
				}
			}

			$model = JModelLegacy::getInstance('language', 'ctransifexModel', array('project' => $project, 'resource' => $resource));


			if (count($needsInsert))
			{
				$model->add($needsInsert);
			}

			if ($needsUpdate)
			{
				$model->add($needsUpdate, true);
			}

			return array_merge($needsInsert, $needsUpdate);
		}
		else
		{
			$this->error = 'Handle languages Transifex error: ' . $txData;
		}

		return false;
	}

	/**
	 * Create the zip packages
	 *
	 * @param   string  $lang     - the language to generate zip for
	 * @param   object  $project  - the project object
	 *
	 * @return void
	 */
	public function handlePackages($lang, $project)
	{
		$projectConfig = parse_ini_string($project->transifex_config, true);
		$jLang = ctransifexHelperTransifex::getLangCode($lang, $projectConfig);

		if ($jLang)
		{
			$model = JModelLegacy::getInstance('Language', 'ctransifexModel', array('project' => $project));
			$resourcesForLang = $model->getResourcesForLang($jLang);

			foreach ($resourcesForLang as $resourceLang)
			{
				$langInfo = $model->getLangInfo($jLang, $resourceLang->resource_name);

				$minPerc = 0;

				if (isset($projectConfig['main']['minimum_perc']))
				{
					$minPerc = $projectConfig['main']['minimum_perc'];
				}
				elseif (isset($projectConfig[$project->transifex_slug . '.' . $resourceLang->resource_name]['minimum_perc']))
				{
					// Set min perc for resource
					$minPerc = $projectConfig[$project->transifex_slug . '.' . $resourceLang->resource_name]['minimum_perc'];
				}

				// Download the file only if necessary
				if ($minPerc == 0 || $minPerc <= $langInfo->completed)
				{
					if (!$this->langFile($resourceLang->resource_name, $lang, $project))
					{
						JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
						JLog::add('something went wrong when we tried to get the ' . $jLang . ' for the ' . $resourceLang->resource_name . ' resource');
					}
				}
			}

			if (ctransifexHelperPackage::package($jLang, $project))
			{
				$packageModel = JModelLegacy::getInstance('Package', 'ctransifexModel', array('project' => $project));
				$packageModel->add($resourcesForLang, $jLang);
				$response['message'] = 'We have created a zip package for ' . $jLang;
				$response['status'] = 'success';
			}
			else
			{
				JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
				JLog::add('we couldn\t package ' . $jLang);
			}
		}
	}
}

JApplicationCli::getInstance('CtransifexCli')->execute();
