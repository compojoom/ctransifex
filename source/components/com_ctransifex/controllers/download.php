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
 * Class ctransifexControllerDownload
 *
 * @since  1
 */
class CtransifexControllerDownload extends JControllerLegacy
{
	/**
	 * The download function
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function download()
	{
		$input = JFactory::getApplication()->input;
		$language = $input->getInt('language', 0);

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('z.*, p.transifex_slug, p.extension_name')
			->from($db->qn('#__ctransifex_zips') . ' AS z')
			->leftJoin($db->qn('#__ctransifex_projects') . ' AS p ON ' . $db->qn('p.id') . '=' . $db->qn('z.project_id'))
			->where($db->qn('z.id') . ' = ' . $db->q($language));

		$db->setQuery($query, 0, 1);
		$result = $db->loadObject();

		if ($result)
		{
			$filepath = JPATH_ROOT . '/media/com_ctransifex/packages/' . $result->transifex_slug . '/'
				. $result->lang_name . '/' . $result->lang_name . '.' . $result->extension_name . '.zip';

			// Set example variables
			$filename = $result->lang_name . '.' . $result->extension_name . '.zip';

			// Http headers for zip downloads
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($filepath));
			ob_end_flush();
			readfile($filepath);
			JFactory::getApplication()->close();
		}
		else
		{
			throw new Exception('Something went wrong');
		}
	}
}
