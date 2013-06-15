<?php
/**
 * @author     Daniel Dimitrov - compojoom.com
 * @date       : 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class ctransifexViewProjects
 *
 * @since  1
 */
class CtransifexViewProjects extends JViewLegacy
{
	/**
	 * Display
	 *
	 * @param   string  $tpl  the layout
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$document = JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' ' . JText::_('COM_CTRANSIFEX_TRANSLATION_PROJECTS'));

		parent::display();
	}
}
