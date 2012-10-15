<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$language = JFactory::getLanguage();
$language->load('com_ctransifex.sys', JPATH_ADMINISTRATOR, null, true);

$view	= JFactory::getApplication()->input->getCmd('view');

$subMenus = array (
    'projects' => 'COM_CTRANSIFEX_PROJECTS',
    'liveupdate' => 'COM_HOTSPOTS_LIVEUPDATE'
);

foreach($subMenus as $key => $name) {
    $active	= ( $view == $key );

    JSubMenuHelper::addEntry( JText::_($name) , 'index.php?option=com_ctransifex&view=' . $key , $active );
}