<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class ctransifexHelperPackage
{

    public static function saveLangFile($file, $jLang, $project, $resource, $config) {

        // find out the fileName and his location
        $fileFilter = preg_split('#/|\\\#', $config[$project->transifex_slug.'.'.$resource]['file_filter']);
        $fileName = str_replace('<lang>', $jLang, end($fileFilter));
		$isAdmin = false;
	    $isInstall = false;
        $adminPath = JPATH_ROOT . '/media/com_ctransifex/packages/'.$project->transifex_slug.'/'.$jLang.'/admin/';
        $frontendPath = JPATH_ROOT . '/media/com_ctransifex/packages/'.$project->transifex_slug.'/'.$jLang.'/frontend/';
        $installPath = JPATH_ROOT . '/media/com_ctransifex/packages/'.$project->transifex_slug.'/'.$jLang.'/installation/';
	    $path = $frontendPath.$fileName;
	    $params = new JRegistry($project->params);

	    if($params->get('determine_location', 1)) {
	        if(in_array('admin', $fileFilter) || in_array('administrator', $fileFilter) || in_array('backend', $fileFilter)) {
	            $isAdmin = true;
	        }

		    if(in_array('install', $fileFilter) || in_array('installation', $fileFilter)) {
			    $isInstall = true;
		    }
	    } else {
		    if(strstr($resource, 'admin') || strstr($resource, 'administrator') || strstr($resource, 'backend')) {
			    $isAdmin = true;
		    }

		    if(strstr($resource, 'install') || strstr($resource, 'installation')) {
			    $isInstall = true;
		    }
	    }

	    if($isAdmin) {
		    $path = $adminPath.$fileName;
	    }

	    if($isInstall) {
		    $path = $installPath.$fileName;
	    }


	    if(Jfile::write($path, $file['data'])){
            return true;
        }

        return false;
    }


    /**
     *
     */
    public static function package($jLang, $project)
    {

        $folder = JPATH_ROOT . '/media/com_ctransifex/packages/' . $project->transifex_slug . '/' . $jLang;
        $zipFile = $folder . '/' . $jLang . '.' . $project->extension_name . '.zip';

        // make sure that we are always creating a new zip file
        if(JFile::exists($zipFile)) {
            JFile::delete($zipFile);
        }

        if(!self::generateInstallXML($folder, $jLang, $project)) {
            return false;
        }

        if(self::zip($folder, $zipFile)) {
            // clean up
            if(JFolder::exists($folder.'/admin')) {
                JFolder::delete($folder.'/admin');
            }
            if(JFolder::exists($folder.'/frontend')) {
                JFolder::delete($folder.'/frontend');
            }
            if(JFile::exists($folder.'/install.xml')) {
                JFile::delete($folder.'/install.xml');
            }
            if(JFile::exists($folder.'/'.$project->extension_name.'-'.$jLang.'.xml')) {
                JFile::delete($folder.'/'.$project->extension_name.'-'.$jLang.'.xml');
            }
            return true;
        }

        return false;
    }

    /**
     * Recursively goes through each file in the folder and ads it to the archive.
     * @param $source
     * @param $destination
     * @return bool
     */
    private static function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source));
            foreach ($iterator as $key => $value) {
                if (!is_dir($key)) {
                    if (!$zip->addFile(realpath($key), substr($key, strlen($source)+1))) {
                        return false;
                    }
                }
            }
        }

        return $zip->close();
    }

    /**
     * @param $folder
     * @param $jLang
     * @param $project
     * @return bool
     */
    private function generateInstallXml($folder, $jLang, $project)
    {
        $mediaXML = JPATH_ROOT . '/media/com_ctransifex/packages/install.xml';
        if(file_exists($mediaXML)) {
            $dummyXml = JFile::read($mediaXML);
        } else {
            $dummyXml = JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/assets/install.xmt');
        }

        $params = JComponentHelper::getParams('com_ctransifex');
        $adminFiles = '';
        $frontendFiles = '';
        $content = str_replace('@@EXTENSION_NAME@@', $project->extension_name, $dummyXml);
        $content = str_replace('@@VERSION@@', time(), $content);
        $content = str_replace('@@CREATION_DATE@@', date('d.m.Y'), $content);
        $content = str_replace('@@AUTHOR@@', $params->get('author'), $content);
        $content = str_replace('@@AUTHOR_EMAIL@@', $params->get('author_email'), $content);
        $content = str_replace('@@AUTHOR_URL@@', $params->get('author_url'), $content);
        $content = str_replace('@@COPYRIGHT@@', str_replace('@@YEAR@@', date('Y'), $params->get('copyright')), $content);
        $content = str_replace('@@DESCRIPTION@@', JText::_('COM_CTRANSIFEX_INSTALL_XML_DESCRIPTION'), $content);
        $content = str_replace('@@THANKS_OPENTRANSLATORS@@', JText::_('COM_CTRANSIFEX_INSTALL_XML_THANKS_OPENTRANSLATORS'), $content);
        $content = str_replace('@@LANGUAGE@@', $jLang, $content);
        $admin = self::getFiles($folder . '/admin');
        if($admin) {
            $adminFiles = '<files folder="admin" target="administrator/language/'.$jLang.'">'.$admin.'</files>';
        }
        $content = str_replace('@@ADMIN_FILENAMES@@', $adminFiles , $content);
        $frontend = self::getFiles($folder . '/frontend');
        if($frontend) {
            $frontendFiles = '<files folder="frontend" target="language/'.$jLang.'">'.$frontend.'</files>';
        }
        $content = str_replace('@@FRONTEND_FILENAMES@@', $frontendFiles, $content);
        if(JFile::write($folder . '/'.$project->extension_name.'-'.$jLang.'.xml', $content)) {
            return true;
        }
        return false;
    }

    /**
     * @param $folder
     * @return string
     */
    private static function getFiles($folder) {

        $files = JFolder::files($folder);
        $xml = array();
        foreach($files as $file) {
            $xml[] = '<filename>'.$file.'</filename>';
        }
        return implode("\n", $xml);
    }
}