<?php
/**
 * @package    Ctransifex
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       15.06.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('stylesheet', 'media/com_ctransifex/css/ctransifex-frontend.css');

$language = $this->language;
$langParts = explode('-', $language->lang_name);
$langName = ctransifexHelperLanguage::code2ToName($langParts[0]);
$langCountry = ctransifexHelperLanguage::code2ToCountry($langParts[1]);
?>
<h2><?php echo $this->project->title; ?> - <?php echo ucfirst($langName) ?> (<?php echo ucfirst($langCountry); ?>)</h2>
<div class="row-fluid ctransifex-margin">
	<p>
		<?php echo JText::sprintf('COM_CTRANSIFEX_LANGUAGE_COMPLETE_AT', $language->completed); ?>
	</p>

	<div class="progress progress-striped" style="margin-bottom: 0px;">
		<div class="bar" style="width: <?php echo $language->completed; ?>%;"></div>
	</div>
</div>
<?php if (isset($this->project->params['contributor_info'])) : ?>
	<div class="row-fluid">
		<div class="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo $this->project->params['contributor_info']; ?>
		</div>
	</div>
<?php endif; ?>
<div class="row-fluid">
	<h3>
		<?php echo JText::_('COM_CTRANSIFEX_CONTAINS_FOLLOWING_RESOURCES'); ?>
	</h3>
	<table class="table">
		<?php foreach ($this->resources as $resource) : ?>
			<tr>
				<td>
					<?php echo $resource->resource_name; ?>
				</td>
				<td width="40%">
					<div class="progress progress-striped" style="margin-bottom: 0px;">
						<div class="bar" style="width: <?php echo $resource->completed; ?>%;"></div>
					</div>
				</td>
				<td>
					<div>
						<?php if ($this->project->params['display_contribute_link']) : ?>
							<a href="http://transifex.com/projects/p/<?php echo $this->project->transifex_slug; ?>/language/<?php echo $language->lang_name; ?>"
							   class="btn" target="_blank">
								<?php echo JText::_('COM_CTRANSIFEX_CONTRIBUTE_NOW'); ?>
							</a>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
<div class="row-fluid">
	<div class="form-actions">
		<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_ctransifex&task=download.download&language=' . $language->id); ?>">
			<?php echo JText::_('COM_CTRANSIFEX_DOWNLOAD_NOW'); ?>
		</a>
		<?php if ($this->project->params['display_contribute_link']) : ?>
			<a href="http://transifex.com/projects/p/<?php echo $this->project->transifex_slug; ?>/language/<?php echo $language->lang_name; ?>"
			   class="btn" target="_blank">
				<?php echo JText::_('COM_CTRANSIFEX_HELP_IMPROVE_THIS_LANGUAGE_PACK'); ?>
			</a>
		<?php endif; ?>
	</div>
</div>