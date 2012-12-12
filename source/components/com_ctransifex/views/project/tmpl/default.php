<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('stylesheet', 'media/com_ctransifex/css/ctransifex-frontend.css');
?>

<h2>
    <?php echo $this->item->title; ?>
</h2>
<div>
    <?php echo $this->item->description; ?>
</div>

<table class="table">
    <?php foreach ($this->languages as $language) : ?>
		<tr>
			<td>
				<a href="<?php echo JRoute::_('index.php?option=com_ctransifex&task=download.download&language=' . $language->id); ?>"
				   target="_blank">
					<?php if (isset($language->iso_lang_name)) : ?>
					<?php echo $language->iso_lang_name; ?>
					<?php if (isset($language->iso_country_name) && $language->iso_country_name != '') : ?>
						(<?php echo $language->iso_country_name; ?>)
					<?php endif; ?>
					<?php else : ?>
						<?php echo $language->lang_name; ?>
					<?php endif; ?>
				</a>
			</td>
			<td width="40%">
				<div class="progress progress-striped" style="margin-bottom: 0px;">
					<div class="bar" style="width: <?php echo $language->completed; ?>%;"></div>
				</div>
			</td>
			<td>
				<?php echo $language->completed; ?>%
			</td>
			<td>
				<div>
					<?php echo $language->created; ?>
					<?php if($this->item->params['display_contribute_link'] && $language->completed != 100) : ?>
						<a href="http://transifex.com/projects/p/<?php echo $this->item->transifex_slug; ?>/language/<?php echo $language->lang_name; ?>" class="btn" target="_blank">
							<?php echo JText::_('COM_CTRANSIFEX_CONTRIBUTE_NOW'); ?>
						</a>
					<?php endif; ?>
				</div>
			</td>
		</tr>
    <?php endforeach; ?>
</table>