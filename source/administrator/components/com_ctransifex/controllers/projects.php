<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

class ctransifexControllerProjects extends JControllerAdmin {

    /**
     * Proxy for getModel.
     *
     * @param    string    $name    The name of the model.
     * @param    string    $prefix    The prefix for the PHP class name.
     *
     * @param array $config
     * @return    JModel
     * @since    1.6
     */
    public function getModel($name = 'Project', $prefix = 'CTransifexModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

}