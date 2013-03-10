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

class ctransifexModelLanguage extends JModelLegacy
{

    public function __construct(array $config = array())
    {
        if (isset($config['project'])) {
            $this->projectId = $config['project']->id;
            $this->project = $config['project'];
        }

        if (isset($config['resource'])) {
            $this->resourceId = $this->getResource($config['resource']);
        }

        parent::__construct($config);
    }

    public function add($languages = array())
    {

        // now add the resources
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $config = parse_ini_string($this->project->transifex_config, true);

        foreach ($languages as $key => $language) {
            $langCode = ctransifexHelperTransifex::getJLangCode($key, $config);
            if ($langCode) {
                $values[] = $db->q($this->projectId) . ','
                    . $db->q($this->resourceId) . ','
                    . $db->q($langCode) . ','
                    . $db->q($language->completed) . ','
                    . $db->q($language->untranslated_entities) . ','
                    . $db->q($language->translated_entities) . ','
		    . $db->q(json_encode($language));
            }
        }

        $query->insert('#__ctransifex_languages')
            ->columns(
            array(
                $db->qn('project_id'),
                $db->qn('resource_id'),
                $db->qn('lang_name'),
                $db->qn('completed'),
                $db->qn('untranslated_entities'),
                $db->qn('translated_entities'),
		$db->qn('raw_data')
            )
        )->values($values);

        $db->setQuery($query);
        $db->execute();
    }


    private function getResource($name)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id')->from('#__ctransifex_resources')->where($db->qn('resource_name') . '=' . $db->q($name))
            ->where($db->qn('project_id') . '=' . $db->q($this->projectId));
        $db->setQuery($query, 0, 1);
        return $db->loadObject()->id;
    }

    public function getResourcesForLang($jlang)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select(
            array(
                $db->qn('l.resource_id'),
                $db->qn('r.resource_name'),
                $db->qn('l.lang_name'),
                $db->qn('l.completed'),
                $db->qn('l.untranslated_entities'),
                $db->qn('l.translated_entities'),
		$db->qn('l.raw_data')
            )
        )
            ->from('#__ctransifex_languages AS l')
            ->leftJoin('#__ctransifex_resources AS r ON l.resource_id = r.id')
            ->where($db->qn('l.lang_name') . '=' . $db->q($jlang))
            ->where($db->qn('l.project_id') . '=' . $db->q($this->projectId));

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
