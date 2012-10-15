<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

// thank you for this black magic Nickolas :)
// Magic: merge the default translation with the current translation
$jlang = JFactory::getLanguage();
$jlang->load('com_ctransifex', JPATH_SITE, 'en-GB', true);
$jlang->load('com_ctransifex', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_ctransifex', JPATH_SITE, null, true);
$jlang->load('com_ctransifex', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_ctransifex', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_ctransifex', JPATH_ADMINISTRATOR, null, true);

JLoader::discover('ctransifexHelper', JPATH_COMPONENT . '/helpers');

$input = JFactory::getApplication()->input;
if($input->getCmd('view','') == 'liveupdate') {
    JToolBarHelper::preferences( 'com_hotspots' );
    LiveUpdate::handleRequest();
    return;
}

// in J3.0 the toolbar is not loaded automatically, so let us load it ourselves.
require_once(JPATH_COMPONENT_ADMINISTRATOR. '/toolbar.php');

$controller = JControllerLegacy::getInstance('CTransifex');
$controller->execute($input->getCmd('task'));
$controller->redirect();