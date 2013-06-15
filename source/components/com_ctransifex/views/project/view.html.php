<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class ctransifexViewProject
 *
 * @since  1
 */
class CtransifexViewProject extends JViewLegacy
{
	/**
	 * Display.
	 *
	 * @param   null  $tpl  - the template
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$this->item = $this->get('Item');
		$this->languages = $this->get('Languages');
		$document = JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' ' . JText::sprintf('COM_CTRANSIFEX_TRANSLATIONS_FOR_PROJECT', $this->item->title));
		parent::display();
	}
}
