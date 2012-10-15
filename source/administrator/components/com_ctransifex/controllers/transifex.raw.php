<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class ctransifexControllerTransifex extends JControllerLegacy {

    public function __construct(array $config = array()) {
        parent::__construct($config);

        // we need the project data everywhere in the controller, so let's get it!
        $input = JFactory::getApplication()->input;
        $projectModel = $this->getModel('Project', 'ctransifexModel');
        $this->project = $projectModel->getItem($input->getInt('project-id'));
    }

    public function resources() {
        $this->checkSession();

        $project = $this->project;

        $resources = json_decode(ctransifexHelperTransifex::getData($project->transifex_slug.'/resources/'));

        foreach($resources as $resource) {
            $response['data'][] = $resource->slug;
        }

        $response['status'] = 'success';

        echo json_encode($response);
        jexit();
    }

    /**
     * Get language stats per resource
     */
    public function languageStats() {
        $this->checkSession();

        $input = JFactory::getApplication()->input;
        $resource = $input->get('resource');
        $project = $this->project;

        $path = $project->transifex_slug.'/resource/'.$resource.'/stats/';

        $stats = get_object_vars(json_decode(ctransifexHelperTransifex::getData($path)));

        if(is_array($stats)) {
            $response['status'] = 'success';
            $response['data'] = array_keys($stats);
        }

        echo json_encode($response);
        jexit();
    }

    /**
     * Get the language files from transifex
     */
    public function languageFiles() {
        $this->checkSession();

        $input = JFactory::getApplication()->input;
        $resource = $input->getString('resource');
        $project = $this->project;
        $lang = $input->getString('language');

        $config = parse_ini_string($project->transifex_config, true);

        $path = $project->transifex_slug . '/resource/'.$resource.'/translation/'.$lang.'/?file';

        $file = ctransifexHelperTransifex::getData($path);

        if($file) {
            if(isset($config[$project->transifex_slug.'.'.$resource])) {
                // get the languageMap from the config and determine the joomla language code
                $langMap = explode(',', $config['main']['lang_map']);
                foreach($langMap as $map) {
                    $langCodes = explode(':', $map);
                    $langs[trim($langCodes[0])] = trim($langCodes[1]);
                }
                $jlang = $langs[$lang];

                // find out the fileName and his location
                $fileFilter = preg_split('#/|\\\#', $config[$project->transifex_slug.'.'.$resource]['file_filter']);
                $fileName = str_replace('<lang>', $jlang, end($fileFilter));

                $adminPath = JPATH_ROOT . '/media/com_ctransifex/packages/compojoom-hotspots/'.$jlang.'/admin/';
                $frontendPath = JPATH_ROOT . '/media/com_ctransifex/packages/compojoom-hotspots/'.$jlang.'/frontend/';

                if(in_array('admin', $fileFilter) || in_array('administrator', $fileFilter) || in_array('backup', $fileFilter)) {
                    $path = $adminPath.$fileName;
                } else {
                    $path = $frontendPath.$fileName;
                }

                if(Jfile::write($path, $file)){
                    $response['status'] = 'success';
                }

            } else {
                $response['status'] = 'failure';
                $response['message'] = 'Your transifex config is missing information for this resource. We cannot save the file';

            }
        }

        echo json_encode($response);
        jexit();
    }

    /**
     * @return mixed - returns true if the token is ok or just stops the application if it is not
     */
    private function checkSession() {
        $input = JFactory::getApplication()->input;
        if(JSession::getFormToken() != $input->get('token')) {
            $response['status'] = 'failure';
            $response['message'] = 'Invalid session token. Maybe your session expired???';

            echo json_encode($response);
            jexit();
        }

        return true;
    }
}