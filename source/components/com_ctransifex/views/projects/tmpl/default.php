<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');


JHtml::_('stylesheet', 'media/com_ctransifex/css/ctransifex-frontend.css');

?>

<?php foreach ($this->items as $i => $item) : ?>
    <?php $url = JRoute::_('index.php?option=com_ctransifex&view=project&id=' . $item->id); ?>
    <section id="<?php echo $item->transifex_slug; ?>">
        <h2>
            <a href="<?php echo $url;?>">
                <?php echo $item->title; ?>
            </a>
        </h2>
        <div>
            <?php echo $item->description; ?>
        </div>
        <a class="btn" href="<?php echo $url; ?>">
            <?php echo JText::_('COM_CTRANSIFEX_VIEW_AVAILABLE_TRANSLATIONS'); ?>
        </a>
        <hr />
    </section>
<?php endforeach; ?>

<?php CTransifexHelperUtils::footer(); ?>
