<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class ctransifexHelperTransifex
{
    private static $apiUrl = 'https://www.transifex.com/api/2/project/';
    private static $languages = array();
    /**
     * @param $path
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
        curl_setopt($ch, CURLOPT_USERPWD, $config->get('tx_username').":".$config->get('tx_password'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // get the data
        $data = curl_exec($ch);
        // get info about the request
        $info = curl_getinfo($ch);

        // close the request
        curl_close($ch);

        return array('data' => $data, 'info' => $info);
    }

    /**
     * Gets the transifex langCode and transforms it to the joomla language code
     * @param $transifexLang
     * @param $projectConfig
     * @internal param $config
     * @return mixed
     */
    public static function getJLangCode($transifexLang, $projectConfig){


        $languages = self::getLangmap($projectConfig);

        if(isset($languages[$transifexLang])) {
            return $languages[$transifexLang];
        } else {
            JLog::addLogger(array('text_file' => 'com_ctransifex.error.php'));
            JLog::add('there is no lang map entry for ' . $transifexLang);

            return false;
        }

        return $langs[$transifexLang];
    }

    private function getLangmap($projectConfig) {

        if(!count(self::$languages)) {
            $componentConfig = JComponentHelper::getParams('com_ctransifex');
            $langMap = explode(',', $componentConfig->get('tx_lang_map'));
            foreach($langMap as $map) {
                $langCodes = explode(':', $map);
                $languages[trim($langCodes[0])] = trim($langCodes[1]);
            }

            $langMap = explode(',', $projectConfig['main']['lang_map']);
            foreach($langMap as $map) {
                $langCodes = explode(':', $map);
                $languages[trim($langCodes[0])] = trim($langCodes[1]);
            }

            self::$languages = $languages;
        }

        return self::$languages;
    }
}