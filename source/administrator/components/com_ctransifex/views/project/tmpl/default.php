<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        if (task == 'project.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
        <?php echo $this->form->getField('description')->save(); ?>
            Joomla.submitform(task, document.getElementById('adminForm'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_ctransifex&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row-fluid">

        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <div class="row-fluid">
                    <div class="span6">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('title'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('title'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('transifex_slug'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('transifex_slug'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('alias'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('alias'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('extension_name'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('extension_name'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo $this->form->getLabel('description'); ?>
                <?php echo $this->form->getInput('description'); ?>

                <?php echo $this->form->getLabel('transifex_config'); ?>
                <?php echo $this->form->getInput('transifex_config'); ?>
            </fieldset>
        </div>
        <div class="span2">
            <?php echo $this->form->getLabel('state'); ?>
            <?php echo $this->form->getInput('state'); ?>
            <?php echo $this->form->getLabel('access'); ?>
            <?php echo $this->form->getInput('access'); ?>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>