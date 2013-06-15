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
 * Class ctransifexViewProjects
 *
 * @since  1
 */
class ctransifexViewProjects extends JViewLegacy
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

		$this->addToolbar();
		parent::display();
	}

	/**
	 * Ads toolbar to the backend
	 *
	 * @return void
	 */
	private function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_CTRANSIFEX_PROJECTS'), 'projects');
		JToolBarHelper::addNew('project.add');
		JToolBarHelper::editList('project.edit');
		JToolBarHelper::deleteList('project.delete');
		JToolbarHelper::preferences('com_ctransifex');
	}
}
