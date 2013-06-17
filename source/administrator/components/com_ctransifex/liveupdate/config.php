<?php
/**
 * @package    LiveUpdate
 * @copyright  Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license    GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 *
 * @since  1
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName = 'com_ctransifex';
	var $_extensionTitle = 'CTransifex';
	var $_updateURL = 'https://compojoom.com/index.php?option=com_ars&view=update&format=ini&id=15';
	var $_requiresAuthorization = false;
	var $_versionStrategy = 'different';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_extensionTitle = 'CTransifex ' . (CTRANSIFEX_PRO == 1 ? 'Professional' : 'Core');
		$this->_requiresAuthorization = (CTRANSIFEX_PRO == 1);
		$this->_currentVersion = CTRANSIFEX_VERSION;
		$this->_currentReleaseDate = CTRANSIFEX_DATE;

		if (CTRANSIFEX_PRO)
		{
			$this->_updateURL = 'https://compojoom.com/index.php?option=com_ars&view=update&format=ini&id=17';
		}
		else
		{
			$this->_updateURL = 'https://compojoom.com/index.php?option=com_ars&view=update&format=ini&id=15';
		}

		// Populate downloadID as liveupdate cannot find the download id in the unknown for it scope
		$this->_downloadID = JComponentHelper::getParams('com_ctransifex')->get('downloadid');

		parent::__construct();
	}
}
