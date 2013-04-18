<?php
/**
 * @author     Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$user = JFactory::getUser();
$config = JComponentHelper::getParams('com_ctransifex');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

JHtml::_('behavior.framework', true);
Jhtml::_('stylesheet', 'media/com_ctransifex/css/ctransifex-backend.css');
Jhtml::_('script', 'media/com_ctransifex/js/projects.js');

$domready = "window.addEvent('domready', function() {
    new projects({token: '" . JSession::getFormToken() . "', baseUrl:'" . Juri::root() . "'});
});";

$document->addScriptDeclaration($domready);
?>
<div class="compojoom-bootstrap">
	<div>
		<form action="<?php echo JRoute::_('index.php?option=com_ctransifex&view=projects'); ?>"
		      id="adminForm"
		      name="adminForm"
		      method="POST">
			<table class="table table-striped adminlist">
				<thead>
				<tr>
					<th width="2%">
						<input type="checkbox" name="checkall-toggle" value=""
						       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JText::_('COM_CTRANSIFEX_TRANSIFEX_WEBHOOKS'); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<?php
					$canChange = $user->authorise('core.edit.state', 'com_ctransifex.project.' . $item->id);
					?>
					<tr>
						<td>
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<?php echo JHtml::_('jgrid.published', $item->state, $i, 'projects.', $canChange, 'cb'); ?>
							<?php if ($config->get('tx_username') && $config->get('tx_password')) : ?>
								<a href="#" class="btn ctransifex-project-data"
								   data-id="<?php echo $item->id; ?>"
								   title="<?php echo JText::_('COM_CTRANSIFEX_LOAD_TRANSIFEX_PROJECT_DATA'); ?>"
								   data-toggle="ctransifex">
									<i class="icon-loop icon-joomla25"></i>
								</a>
							<?php endif; ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_ctransifex&task=project.edit&id=' . $item->id); ?>"
							   title="<?php echo JText::_('JACTION_EDIT'); ?>">
								<?php echo $item->title; ?>
							</a>
						</td>
						<td class="small hidden-phone">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<td>
							<?php if ($config->get('transifex_webhook_key')) : ?>
								<?php echo JURI::root(); ?>index.php?option=com_ctransifex&key=<?php echo $config->get('transifex_webhook_key'); ?>&task=webhooks.webhook&project_id=<?php echo $item->id; ?>
							<?php else : ?>
								<?php echo JText::_('COM_CTRANSIFEX_NO_GLOBAL_WEBHOOKS_KEY'); ?>
							<?php endif; ?>
						</td>

					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php echo $this->pagination->getListFooter(); ?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>

			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
	<div class="clr"></div>
	<div>
		<div class="fluid-row">
			<strong>
				CTransifex
				<?php echo JComponentHelper::getParams('com_ctransifex')->get('version'); ?></strong>
			<br>
	<span style="font-size: x-small">
		Copyright &copy;2008&ndash;<?php echo date('Y'); ?> Daniel Dimitrov / compojoom.com
	</span>
			<br>


			<strong>
				If you use CTransifex, please post a rating and a review at the
				<a href="http://extensions.joomla.org/extensions/miscellaneous/development/22711"
				   target="_blank">Joomla! Extensions Directory</a>.
			</strong>
			<br>

	<span style="font-size: x-small">
		CTransifex is Free software released under the
		<a href="www.gnu.org/licenses/gpl.html">GNU General Public License,</a>
		version 2 of the license or &ndash;at your option&ndash; any later version
		published by the Free Software Foundation.
	</span>

			<div>
				<div class="row-fluid">
					<strong><?php echo JText::_('COM_CTRANSIFEX_LATEST_NEWS_PROMOTIONS'); ?>:</strong>
				</div>
				<div class="row-fluid">
					<div class="span3">
						<?php echo JText::_('COM_CTRANSIFEX_LIKE_FB'); ?><br/>
						<iframe
							src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fcompojoom&amp;width=292&amp;height=62&amp;show_faces=false&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=false&amp;appId=545781062132616"
							scrolling="no" frameborder="0"
							style="border:none; overflow:hidden; width:292px; height:62px;"
							allowTransparency="true"></iframe>
					</div>
					<div class="span3">
						<?php echo JText::_('COM_CTRANSIFEX_FOLLOW_TWITTER'); ?><br/><br/>
						<a href="https://twitter.com/compojoom" class="twitter-follow-button" data-show-count="false">Follow
							@compojoom</a>
						<script>!function (d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (!d.getElementById(id)) {
									js = d.createElement(s);
									js.id = id;
									js.src = "//platform.twitter.com/widgets.js";
									fjs.parentNode.insertBefore(js, fjs);
								}
							}(document, "script", "twitter-wjs");</script>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="modal" id="ctransifex-project-data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	     aria-hidden="true" style="display:none;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><?php echo JText::_('COM_CTRANSIFEX_FETCH_DATA_FROM_TRANSIFEX_FOR_THIS_PROJECT'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::_('COM_CTRANSIFEX_SIT_BACK_RELAX'); ?></p>

			<form class="ctransifex-project-data-form"
			      action="<?php echo JRoute::_('index.php?option=com_ctransifex&task=transifex.resources&format=raw'); ?>"
			      method="POST">
				<div></div>
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
		<div class="modal-footer">
			<!--        <button class="btn" data-dismiss="modal" aria-hidden="true">-->
			<?php //echo JText::_('COM_CTRANSIFEX_CLOSE'); ?><!--</button>-->
			<button class="btn btn-primary"><?php echo JText::_('COM_CTRANSIFEX_GET_DATA'); ?></button>
		</div>
	</div>

</div>