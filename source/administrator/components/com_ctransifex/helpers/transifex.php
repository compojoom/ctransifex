<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class ctransifexHelperTransifex
 *
 * @since  1
 */
class CtransifexHelperTransifex
{
	private static $apiUrl = 'https://www.transifex.com/api/2/project/';

	private static $languages = array();

	/**
	 * Gets data from transifex
	 *
	 * @param   string  $path  - the url
	 *
	 * @return array
	 */
	public static function getData($path)
	{
		$config = JComponentHelper::getParams('com_ctransifex');
		$url = self::$apiUrl . $path;
		$ch = curl_init();
		$info = '';
		$timeout = 120;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $config->get('tx_username') . ":" . $config->get('tx_password'));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, 400);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// Get the data
		$data = curl_exec($ch);

		// Get info about the request
		$info = curl_getinfo($ch);

		// Close the request
		curl_close($ch);

		return array('data' => $data, 'info' => $info);
	}

	/**
	 * Gets the langCode and transforms it to the format we need
	 * en-GB : en_GB or en_GB : en-GB
	 *
	 * @param   string   $lang           - the lang
	 * @param   object   $projectConfig  - the config
	 * @param   boolean  $transifex      - true = joomla, false = transifex
	 *
	 * @return mixed
	 */
	public static function getLangCode($lang, $projectConfig, $transifex = false)
	{
		$languages = self::getLangmap($projectConfig);

		if ($transifex)
		{
			$languages = array_flip($languages);
		}

		if (isset($languages[$lang]))
		{
			return $languages[$lang];
		}
		else
		{
			JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
			JLog::add('there is no lang map entry for ' . $lang);
		}

		return false;
	}

	/**
	 * Gets the language map
	 *
	 * @param   object  $projectConfig  - the project config
	 *
	 * @return array
	 */
	private static function getLangmap($projectConfig)
	{
		if (!count(self::$languages))
		{
			$componentConfig = JComponentHelper::getParams('com_ctransifex');
			$langMap = explode(',', $componentConfig->get('tx_lang_map'));

			foreach ($langMap as $map)
			{
				$langCodes = explode(':', $map);
				$languages[trim($langCodes[0])] = trim($langCodes[1]);
			}

			if (isset($projectConfig['main']['lang_map']))
			{
				$langMap = explode(',', $projectConfig['main']['lang_map']);

				foreach ($langMap as $map)
				{
					$langCodes = explode(':', $map);
					$languages[trim($langCodes[0])] = trim($langCodes[1]);
				}
			}

			self::$languages = $languages;
		}

		return self::$languages;
	}
}
