<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Live Update File Storage Class
 * Allows to store the update data to files on disk. Its configuration options are:
 * path			string	The absolute path to the directory where the update data will be stored as INI files
 *
 */
class LiveUpdateStorageFile extends LiveUpdateStorage
{
	private static $filename = null;
	private static $extname = null;

	public function load($config)
	{
		JLoader::import('joomla.registry.registry');
		JLoader::import('joomla.filesystem.file');

		$path = $config['path'];
		$extname = $config['extensionName'];
		$filename = "$path/$extname.updates.php";

		// Kill old files
		$filenameKill = "$path/$extname.updates.ini";
		if (JFile::exists($filenameKill))
		{
			JFile::delete($filenameKill);
		}

		self::$filename = $filename;
		self::$extname = $extname;

		self::$registry = new JRegistry('update');

		if(JFile::exists(self::$filename)) {
			$options = array(
				'class'	=> 'LiveUpdate' . ucwords($extname) . 'Cache'
			);
			self::$registry->loadFile(self::$filename, 'PHP', $options);
		}
	}

	public function save()
	{
		JLoader::import('joomla.registry.registry');
		JLoader::import('joomla.filesystem.file');

		$options = array(
			'class'	=> 'LiveUpdate' . ucwords(self::$extname) . 'Cache'
		);
		$data = self::$registry->toString('PHP', $options);
		JFile::write(self::$filename, $data);
	}
}