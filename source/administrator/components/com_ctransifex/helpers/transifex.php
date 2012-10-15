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

    /**
     * @param $path
     * @return mixed
     */
    public static function getData($path)
    {
        $url = self::$apiUrl . $path;
        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "compojoom:tur1ngopt1on");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Gets the transifex langCode and transforms it to the joomla language code
     * @param $transifexLang
     * @param $config
     * @return mixed
     */
    public static function getJLangCode($transifexLang, $config){
        $langMap = explode(',', $config['main']['lang_map']);
        foreach($langMap as $map) {
            $langCodes = explode(':', $map);
            $langs[trim($langCodes[0])] = trim($langCodes[1]);
        }

        return $langs[$transifexLang];
    }
}