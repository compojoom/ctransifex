<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class ctransifexModelResource extends JModelLegacy {

    public function __construct(array $config = array()) {
        if(isset($config['project_id'])) {
            $this->projectId = $config['project_id'];
        }

        parent::__construct($config);
    }

    public function add($resources = array()) {
        // remove all resources for that project
        $this->cleanDb();

        // now add the resources
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        foreach($resources as $resource) {
            $values[] = $db->q($this->projectId) . ',' . $db->q($resource);
        }

        $query->insert('#__ctransifex_resources')->columns(array($db->qn('project_id'), $db->qn('resource_name')))
            ->values($values);

        $db->setQuery($query);
        $db->execute();
    }

    private function cleanDb() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        // remove the resources
        $query->delete('#__ctransifex_resources')->where($db->qn('project_id') . '=' . $db->q($this->projectId));
        $db->setQuery($query);
        $db->execute();

        $query->clear();

        // remove all languages for the current project
        $query->delete('#__ctransifex_languages')
            ->where($db->qn('project_id') . '=' . $db->q($this->projectId));
        $db->setQuery($query);
        $db->execute();

        // remove all zips for the current project
        $query->clear();

        $query->delete('#__ctransifex_zips')
            ->where($db->qn('project_id') . '=' . $db->q($this->projectId));
        $db->setQuery($query);
        $db->execute();
    }

}