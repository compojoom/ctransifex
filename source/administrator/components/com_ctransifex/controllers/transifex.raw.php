<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');

/**
 * Class ctransifexControllerTransifex
 *
 * @since  1
 */
class CtransifexControllerTransifex extends JControllerLegacy
{
	/**
	 * The constructor
	 *
	 * @param   array  $config  - the controller config
	 */
	public function __construct(array $config = array())
	{
		parent::__construct($config);

		// We need the project data everywhere in the controller, so let's get it!
		$input = JFactory::getApplication()->input;
		$projectModel = $this->getModel('Project', 'ctransifexModel');
		$this->project = $projectModel->getItem($input->getInt('project-id'));
	}

	/**
	 * Gets the project resources
	 *
	 * @return void
	 */
	public function resources()
	{
		$this->checkSession();

		$project = $this->project;

		$resources = ctransifexHelperTransifex::getData($project->transifex_slug . '/resources/');

		if (isset($resources['info']) && $resources['info']['http_code'] != 200)
		{
			$response['message'] = $resources['data'];
			$response['status'] = 'failure';

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

			// If we have resources add them to the db
			if (is_array($response['data']))
			{
				$model = $this->getModel('Resource', 'ctransifexModel', array('project_id' => $project->id));

				$model->add($response['data']);
			}

			$response['status'] = 'success';
		}

		echo json_encode($response);
		jexit();
	}

	/**
	 * Get language stats per resource
	 *
	 * @return void
	 */
	public function languageStats()
	{
		$this->checkSession();

		$input = JFactory::getApplication()->input;
		$resource = $input->get('resource');
		$project = $this->project;

		$path = $project->transifex_slug . '/resource/' . $resource . '/stats/';
		$txData = (ctransifexHelperTransifex::getData($path));

		if (isset($txData['info']) && $txData['info']['http_code'] == 200)
		{
			$stats = get_object_vars(json_decode($txData['data']));

			if (is_array($stats))
			{
				$response['status'] = 'success';
				$response['data'] = array_keys($stats);

				$model = $this->getModel('language', 'ctransifexModel', array('project' => $project, 'resource' => $resource));

				$model->add($stats);
			}
		}
		else
		{
			$response['status'] = 'failure';
			$response['data'] = $txData['data'];
		}

		echo json_encode($response);
		jexit();
	}

	/**
	 * Downloads translations for all resources for a language and zips them
	 *
	 * @return void
	 */
	public function langpack()
	{
		$this->checkSession();
		$minPerc = 0;
		$input = JFactory::getApplication()->input;
		$project = $this->project;
		$config = parse_ini_string($project->transifex_config, true);

		// Take the minPerc variable from the config
		if (isset($config['main']['minimum_perc']))
		{
			$minPerc = $config['main']['minimum_perc'];
		}

		$lang = $input->getString('language');
		$jLang = ctransifexHelperTransifex::getLangCode($lang, parse_ini_string($this->project->transifex_config, true));

		if ($jLang)
		{
			$model = $this->getModel('Language', 'ctransifexModel', array('project' => $project));
			$resources = $model->getResourcesForLang($jLang);

			foreach ($resources as $resource)
			{
				$langInfo = $model->getLangInfo($jLang, $resource->resource_name);

				// Check if we have a minPerc for this resource (if not use the main Perc)
				if (isset($config[$project->transifex_slug . '.' . $resource->resource_name]['minimum_perc']))
				{
					$minPerc = $config[$project->transifex_slug . '.' . $resource->resource_name]['minimum_perc'];
				}

				// Download the file only if necessary
				if ($minPerc == 0 || $minPerc <= $langInfo->completed)
				{
					if (!$this->langFile($resource->resource_name, $lang, $model))
					{
						JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
						JLog::add('something went wrong when we tried to get the ' . $jLang . ' for the ' . $resource->resource_name . ' resource');
					}
				}
			}

			if (ctransifexHelperPackage::package($jLang, $project))
			{
				$packageModel = $this->getModel('Package', 'ctransifexModel', array('project' => $project));
				$packageModel->add($resources, $jLang);
				$response['message'] = 'We have created a zip package for ' . $jLang;
				$response['status'] = 'success';
			}
			else
			{
				JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
				JLog::add('we couldn\t package ' . $jLang);
			}

			echo json_encode($response);
		}


		jexit();
	}

	/**
	 * Get the language files from transifex
	 *
	 * @param   string  $resource  - the resource name
	 * @param   string  $lang      - the lang name
	 *
	 * @return bool
	 */
	public function langFile($resource, $lang)
	{
		$this->checkSession();

		$project = $this->project;

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
	 * Checks the user session
	 *
	 * @return bool
	 */
	private function checkSession()
	{
		$input = JFactory::getApplication()->input;

		if (JSession::getFormToken() != $input->get('token'))
		{
			$response['status'] = 'failure';
			$response['message'] = 'Invalid session token. Maybe your session expired???';

			echo json_encode($response);
			jexit();
		}

		return true;
	}
}