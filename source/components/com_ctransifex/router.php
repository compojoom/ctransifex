<?php
/**
 * Thanks to https://github.com/daycounts for his original pull request for this router file
 *
 * @package    Ctransifex
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       15.06.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Builds the route
 *
 * @param   array  &$query  A named array
 *
 * @return    array
 */
function cTransifexBuildRoute(&$query)
{
	$segments = array();

	$db = JFactory::getDBO();
	$sql = $db->getQuery(true);

	if (isset($query['view']) && $query['view'] == 'project' && isset($query['id']))
	{
		$sql->select('alias')
			->from('#__ctransifex_projects')
			->where("id = " . $db->q($query['id']));
		$db->setQuery($sql);

		if ($alias = $db->loadResult())
		{
			$segments[] = $alias;
			unset($query['view']);
			unset($query['id']);
		}
	}
	elseif (isset($query['view']) && $query['view'] == 'language' && isset($query['zip']) && isset($query['project']))
	{
		$sql->select('z.lang_name, p.alias')
			->from('#__ctransifex_zips as z')
			->leftJoin('#__ctransifex_projects AS p ON z.project_id = p.id')
			->where("z.id = " . $db->q($query['zip']));
		$db->setQuery($sql);

		if ($project = $db->loadObject())
		{
			$segments[] = $project->alias;
			$segments[] = $project->lang_name;
			unset($query['view']);
			unset($query['zip']);
			unset($query['project']);
		}
	}
	elseif (isset($query['task']) && $query['task'] == 'download.download' && isset($query['language']))
	{
		$sql->select('p.id, p.alias, z.lang_name')
			->from('#__ctransifex_projects p')
			->join('INNER', '#__ctransifex_zips z  ON (p.id = z.project_id)')
			->where("z.id = " . $db->q($query['language']));
		$db->setQuery($sql);

		if ($data = $db->loadObject())
		{
			$segments[] = $data->alias;
			$segments[] = 'download';
			$segments[] = $data->lang_name;
			unset($query['task']);
			unset($query['language']);
		}
	}

	return $segments;
}

/**
 * Parses route
 *
 * @param   array  $segments  A named array
 *
 * @return    array
 */
function cTransifexParseRoute($segments)
{
	$vars = array();

	$count = count($segments);

	$db = JFactory::getDBO();
	$sql = $db->getQuery(true);

	if ($count)
	{
		if ($count == 1)
		{
			// We are in a project detail
			$alias = str_replace(':', '-', $segments[0]);
			$sql->select('id')
				->from('#__ctransifex_projects')
				->where("alias = " . $db->q($alias));
			$db->setQuery($sql);
			$projectid = $db->loadResult();

			$vars['view'] = 'project';
			$vars['id'] = $projectid;
		}

		if ($count == 2)
		{
			$alias = str_replace(':', '-', $segments[0]);
			$language_code = str_replace(':', '-', $segments[1]);

			$vars['view'] = 'language';
			$sql->select('z.id as zipid, p.id as pid')
				->from('#__ctransifex_zips as z')
				->leftJoin('#__ctransifex_projects AS p ON z.project_id = p.id')
				->where("z.lang_name = " . $db->q($language_code))
				->where('p.alias =' . $db->q($alias));
			$db->setQuery($sql);
			$project = $db->loadObject();

			$vars['zip'] = $project->zipid;
			$vars['project'] = $project->pid;
		}

		if ($count == 3 && $segments[1] == 'download')
		{
			$language_code = str_replace(':', '-', $segments[2]);
			$alias = str_replace(':', '-', $segments[0]);

			// Downloading a language
			$sql = $db->getQuery(true);
			$sql->select('z.id as id')
				->from('#__ctransifex_zips AS z')
				->leftJoin('#__ctransifex_projects AS p on p.id = z.project_id')
				->where("p.alias = " . $db->q($alias))
				->where("lang_name = " . $db->q($language_code));
			$db->setQuery($sql);
			$languageid = $db->loadResult();
			$vars['task'] = 'download.download';
			$vars['language'] = $languageid;
		}
	}

	return $vars;
}
