<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class ctransifexViewProjects extends JViewLegacy {

    public function display() {
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        $this->addToolbar();
        parent::display();
    }

    private function addToolbar(){
        JToolBarHelper::title(JText::_('COM_CTRANSIFEX_PROJECTS'), 'projects');
        JToolBarHelper::addNew('project.add');
        JToolBarHelper::editList('project.edit');
        JToolBarHelper::deleteList('project.delete');
    }
}