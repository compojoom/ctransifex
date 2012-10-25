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

?>
<div>
    <?php foreach ($this->items as $i => $item) : ?>
    <h2>
        <a href="<?php echo JRoute::_('index.php?option=com_ctransifex&view=project&id=' . $item->id);?>">
            <?php echo $item->title; ?>
        </a>
    </h2>
        <div>
            <?php echo $item->description; ?>
        </div>
    <?php endforeach; ?>
</div>