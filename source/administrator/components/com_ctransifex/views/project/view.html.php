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
	 * Display
	 *
	 * @param   null  $tpl  - the template
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		$this->addToolbar();
		parent::display();
	}

	/**
	 * Add toolbar
	 *
	 * @return void
	 */
	private function addToolbar()
	{
		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			JToolBarHelper::title(JText::_('COM_CTRANSIFEX_PROJECT_NEW'), 'projects');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_CTRANSIFEX_PROJECT_EDIT'), 'projects');
		}

		JToolBarHelper::save('project.save');
		JToolBarHelper::apply('project.apply');
		JToolBarHelper::cancel('project.cancel');
	}
}
