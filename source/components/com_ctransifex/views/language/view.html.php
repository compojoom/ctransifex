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
 * Class CtransifexViewProject
 *
 * @since  1.5
 */
class CtransifexViewLanguage extends JViewLegacy
{
	/**
	 * Display function
	 *
	 * @param   string  $tpl  - the template
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$projectModel = JModelLegacy::getInstance('Project', 'ctransifexModel');
		$this->project = $projectModel->getItem(JFactory::getApplication()->input->getInt('project'));
		$this->language = $this->get('Item');
		$this->resources = $this->get('Resources');
		$document = JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' ' . JText::sprintf('COM_CTRANSIFEX_LANGUAGE_PACK', $this->language->lang_name));
		parent::display($tpl);
	}
}
