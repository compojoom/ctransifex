<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellegacy');

class ctransifexModelPackage extends JModelLegacy
{

    public function __construct(array $config = array())
    {
        if (isset($config['project'])) {
            $this->projectId = $config['project']->id;
            $this->project = $config['project'];
        }

        parent::__construct($config);
    }

    public function add($resources, $language)
    {

        // now add the resources
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $translated = 0;
        $untranslated = 0;

        $allResources = $this->countResources();

        foreach($resources as $resource) {
                $translated += $resource->translated_entities;
                $untranslated += $resource->untranslated_entities;
                $completed = (($translated / ($translated + $untranslated)) * 100);
        }
        
        // make front listing and saving of zip values depend on the value minimum_perc
                $query ->select('minimum_perc')
                   ->from('#__ctransifex_projects')
                  ->where('id='.$db->quote($this->projectId));
                      $db->setQuery($query);
                      $result = $db->loadObject();
                $minperc = $result->minimum_perc;
                if ($completed >= $minperc){

        $values = $db->q($this->projectId) .
                ',' . $db->q($language) .
                ',' . $db->q((int)$completed) .
                ',' . $db->q(JFactory::getDate()->toSql());

        $query->insert('#__ctransifex_zips')
            ->columns(
            array(
                $db->qn('project_id'),
                $db->qn('lang_name'),
                $db->qn('completed'),
                $db->qn('created')
            )
        )->values($values);

        $db->setQuery($query);
        $db->execute();
        }
    }

    public function countResources() {
        $db = JFactory::getDbo();
        $query = $db->getQuery('true');

        $query->select('COUNT(id) as count')->from('#__ctransifex_resources')->where('project_id = ' .$db->q($this->projectId));

        $db->setQuery($query);

        return $db->loadObject()->count;
    }
}
