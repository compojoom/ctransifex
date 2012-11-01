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
        $this->item = $this->get('Item');
        $this->languages = $this->get('Languages');
        $document = JFactory::getDocument();
        $document->setTitle($document->getTitle() . ' ' . JText::sprintf('COM_CTRANSIFEX_TRANSLATIONS_FOR_PROJECT',$this->item->title));
        parent::display();
    }
}