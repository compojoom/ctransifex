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
				<a href="<?php echo JRoute::_('index.php?option=com_ctransifex&view=language&zip=' . $language->id.'&project='.$this->item->id); ?>">
					<?php if (isset($language->iso_lang_name)) : ?>
					<?php echo ucfirst($language->iso_lang_name); ?>
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
		</tr>
    <?php endforeach; ?>
</table>

<?php CTransifexHelperUtils::footer(); ?>
