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

/**
 * Class CTransifexHelpersUtils
 *
 * @since  1.5
 */
class CTransifexHelperUtils
{
	/**
	 * Outputs a footer
	 *
	 * @return void
	 */
	public static function footer()
	{
		$config = JComponentHelper::getParams('com_ctransifex');
		$compojoom = $config->get('compojoom_footer', 1);
		$opentranslators = $config->get('opentranslators_footer', 1);
		$transifex = $config->get('transifex', 1);

		if ($compojoom || $opentranslators || $transifex)
		{
			echo '<div class="row-fluid muted small"><p class="center text-center">';

			if ($compojoom)
			{
				echo JText::sprintf('COM_CTRANSIFEX_POWERED_BY', 'https://compojoom.com');
			}

			if ($opentranslators)
			{
				echo ' ' . JText::sprintf('COM_CTRANSIFEX_POWERED_BY_OPENTRANSLATORS', 'http://opentranslators.org');
			}

			if ($transifex)
			{
				if ($opentranslators)
				{
					echo ' ' . JText::_('COM_CTRANSIFEX_ON');
				}
				else
				{
					echo ' ' . JText::_('COM_CTRANSIFEX_USING');
				}

				echo ' <a href="https://transifex.com" target="_blank">Transifex</a>.';
			}

			echo '</p></div>';
		}

	}
}
