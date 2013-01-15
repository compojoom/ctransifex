<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');

class ctransifexControllerPackager extends JControllerLegacy
{

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        // we need the project data everywhere in the controller, so let's get it!
        $input = JFactory::getApplication()->input;
        $projectModel = $this->getModel('Project', 'ctransifexModel');
        $this->project = $projectModel->getItem($input->getInt('project-id'));
    }

    /**
     *
     */
    public function package()
    {
        $this->checkSession();

        $input = JFactory::getApplication()->input;
        $lang = $input->getString('language');
        $project = $this->project;
        $jLang = ctransifexHelperTransifex::getJLangCode($lang, parse_ini_string($project->transifex_config, true));

        $folder = JPATH_ROOT . '/media/com_ctransifex/packages/' . $project->transifex_slug . '/' . $jLang;
        $zipFile = $folder . '/' . $jLang . '.' . $project->extension_name . '.zip';

        // make sure that we are always creating a new zip file
        if(JFile::exists($zipFile)) {
            JFile::delete($zipFile);
        }

        if(!$this->generateInstallXML($folder, $jLang, $project)) {
            $response['status'] = 'error';
            echo json_encode($response);
            jexit();
        }

        if($this->zip($folder, $zipFile)) {
            $response['status'] = 'success';
        }

        echo json_encode($response);

        jexit();
    }

    /**
     * Recursively goes through each file in the folder and ads it to the archive.
     * @param $source
     * @param $destination
     * @return bool
     */
    private function zip($source, $destination)
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
        $dummyXml = JFile::read(JPATH_ROOT . '/media/com_ctransifex/packages/install.xml');
        $params = JComponentHelper::getParams('com_ctransifex');
        $adminFiles = '';
        $frontendFiles = '';
        $content = str_replace('@@EXTENSION_NAME@@', $project->extension_name, $dummyXml);
        $content = str_replace('@@CREATION_DATE@@', date('d.m.Y'), $content);
        $content = str_replace('@@AUTHOR@@', $params->get('author'), $content);
        $content = str_replace('@@AUTHOR_EMAIL@@', $params->get('author_email'), $content);
        $content = str_replace('@@AUTHOR_URL@@', $params->get('author_url'), $content);
        $content = str_replace('@@COPYRIGHT@@', str_replace('@@YEAR@@', date('Y'), $params->get('copyright')), $content);
        $content = str_replace('@@DESCRIPTION@@', JText::_('COM_CTRANSIFEX_INSTALL_XML_DESCRIPTION'), $content);
        $content = str_replace('@@THANKS_OPENTRANSLATORS@@', JText::_('COM_CTRANSIFEX_INSTALL_XML_THANKS_OPENTRANSLATORS'), $content);
        $content = str_replace('@@LANGUAGE@@', $jLang, $content);
        $admin = $this->getFiles($folder . '/admin');
        if($admin) {
            $adminFiles = '<files folder="admin" target="administrator/language/'.$jLang.'">'.$admin.'</files>';
        }
        $content = str_replace('@@ADMIN_FILENAMES@@', $adminFiles , $content);
        $frontend = $this->getFiles($folder . '/frontend');
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
    private function getFiles($folder) {
        $files = JFolder::files($folder);
        $xml = array();
        foreach($files as $file) {
            $xml[] = '<filename>'.$file.'</filename>';
        }
        return implode("\n", $xml);
    }

    /**
     * @return mixed - returns true if the token is ok or just stops the application if it is not
     */
    private function checkSession()
    {
        $input = JFactory::getApplication()->input;
        if (JSession::getFormToken() != $input->get('token')) {
            $response['status'] = 'failure';
            $response['message'] = 'Invalid session token. Maybe your session expired???';

            echo json_encode($response);
            jexit();
        }

        return true;
    }
}