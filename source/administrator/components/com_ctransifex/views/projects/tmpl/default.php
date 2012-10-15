<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

JHtml::_('behavior.framework');
Jhtml::_('script', 'media/com_ctransifex/js/projects.js');

$domready = "window.addEvent('domready', function() {
    new projects({token: '".JSession::getFormToken()."'});
});";

$document->addScriptDeclaration($domready);
?>

<form action="<?php echo JText::_('index.php?option=com_ctransifex&view=projects'); ?>"
      id="adminForm"
      name="adminForm"
        method="POST">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>
                <th>
                    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                </th>
                <th>
                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th>
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
    <tbody>
        <?php foreach($this->items as $i => $item) : ?>
            <?php
                $canChange  = $user->authorise('core.edit.state', 'com_ctransifex.project.'.$item->id);
            ?>
            <tr>
                <td>
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'projects.', $canChange, 'cb'); ?>
                    <a href="#" class="btn ctransifex-project-data"
                       data-id="<?php echo $item->id; ?>"
                       title="<?php echo JText::_('COM_CTRANSIFEX_LOAD_TRANSIFEX_PROJECT_DATA'); ?>"
                       data-toggle="ctransifex">
                        <i class="icon-loop"></i>
                    </a>
                </td>
                <td>
                    <a href="<?php echo JRoute::_('index.php?option=com_ctransifex&task=project.edit&id=' . $item->id);?>" title="<?php echo JText::_('JACTION_EDIT');?>">
                        <?php echo $item->title; ?>
                    </a>
                </td>
                <td class="small hidden-phone">
                    <?php echo $this->escape($item->access_level); ?>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
    <?php echo $this->pagination->getListFooter(); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

    <?php echo JHtml::_('form.token'); ?>
</form>

<div class="modal" id="ctransifex-project-data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo JText::_('COM_CTRANSIFEX_FETCH_DATA_FROM_TRANSIFEX_FOR_THIS_PROJECT'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo JText::_('COM_CTRANSIFEX_SIT_BACK_RELAX'); ?></p>
        <form class="ctransifex-project-data-form" action="<?php echo JRoute::_('index.php?option=com_ctransifex&task=transifex.resources&format=raw'); ?>" method="POST">
            <div></div>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_CTRANSIFEX_CLOSE'); ?></button>
        <button class="btn btn-primary"><?php echo JText::_('COM_CTRANSIFEX_GET_DATA'); ?></button>
    </div>
</div>