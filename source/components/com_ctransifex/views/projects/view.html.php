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
        $document           = JFactory::getDocument();
        $document->setTitle($document->getTitle() .' '. JText::_('COM_CTRANSIFEX_TRANSLATION_PROJECTS'));

        parent::display();
    }
}