<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');


/**
 * @param  array	A named array
 * @return	array
 */
function CTransifexBuildRoute(&$query)
{
	$segments = array();

	$db = JFactory::getDBO();
	$sql = $db->getQuery(true);
	
	if (isset($query['view']) && $query['view']=='project' && isset($query['id'])) {

		$sql->select('alias')
			->from('#__ctransifex_projects')
			->where("id = ".$db->q($query['id']));
		$db->setQuery($sql);
		if ($alias = $db->loadResult()) {
			$segments[] = $alias;
			unset($query['view']);
			unset($query['id']);
		}
	} else if (isset($query['task']) && $query['task']=='download.download' && isset($query['language'])) {

		$sql->select('p.id, p.alias, z.lang_name')
			->from('#__ctransifex_projects p')
			->join('INNER','#__ctransifex_zips z  ON (p.id = z.project_id)')
			->where("z.id = ".$db->q($query['language']));
		$db->setQuery($sql);
		if ($data = $db->loadObject()) {
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
 * @param	array	A named array
 * @param	array
 */
function CTransifexParseRoute($segments)
{
	$vars = array();

	$count = count($segments);

	$db = JFactory::getDBO();
	$sql = $db->getQuery(true);
	
	if ($count) {
		//We are in a project detail
		$alias = str_replace(':','-',$segments[0]);
		$sql->select('id')
			->from('#__ctransifex_projects')
			->where("alias = ".$db->q($alias));
		$db->setQuery($sql);
		$projectid = $db->loadResult();
		if ($count==1) {
			$vars['view'] = 'project';
			$vars['id'] = $projectid;
		}
		
		if ($count==3 && $segments[1]=='download') {
			$language_code = str_replace(':','-',$segments[2]);
			//Downloading a language
			$sql = $db->getQuery(true);
			$sql->select('id')
				->from('#__ctransifex_zips')
				->where("project_id = ".$db->q($projectid))
				->where("lang_name = ".$db->q($language_code));
			$db->setQuery($sql);
			$languageid = $db->loadResult();
			$vars['task'] = 'download.download';
			$vars['language'] = $languageid;
		}
	}
	
	return $vars;
}
