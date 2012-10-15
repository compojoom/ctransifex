<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class ctransifexViewProject extends JViewLegacy {

    public function display() {


        $this->form = $this->get('Form');
        $this->item = $this->get('Item');


        $this->addToolbar();
        parent::display();
    }

    private function addToolbar(){
        $isNew		= ($this->item->id == 0);
        if($isNew) {
            JToolBarHelper::title(JText::_('COM_CTRANSIFEX_PROJECT_NEW'), 'projects');
        } else {
            JToolBarHelper::title(JText::_('COM_CTRANSIFEX_PROJECT_EDIT'), 'projects');
        }

        JToolBarHelper::save('project.save');
        JToolBarHelper::apply('project.apply');
        JToolBarHelper::cancel('project.cancel');
    }
}