<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

/**
 * Class ctransifexModelProject
 *
 * @since  1
 */
class CtransifexModelProject extends JModelAdmin
{
	/**
	 * Gets the languages from the db
	 *
	 * @return mixed
	 */
	public function getLanguages()
	{
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$id = $input->getInt('id', 0);
		$query = $db->getQuery(true);
		$params = JComponentHelper::getParams('com_ctransifex');

		$query->select('*')->from('#__ctransifex_zips')
			->where($db->qn('project_id') . '=' . $db->q($id));

		// Include languages that are 0% complete?
		if (!$params->get('show_0', 1))
		{
			$query->where('completed > 0');
		}

		$db->setQuery($query);

		$languages = $this->prepare($db->loadObjectList());

		return $languages;
	}

	/**
	 * Prepares the languages for output
	 *
	 * @param   array  $languages  - the array with the languages
	 *
	 * @return array
	 */
	private function prepare($languages)
	{
		if (is_array($languages) && count($languages))
		{
			foreach ($languages as $key => $language)
			{
				$iso = explode('-', $language->lang_name);

				if (is_array($iso) && count($iso))
				{
					if (strlen($iso[0]) == 2)
					{
						$languages[$key]->iso_lang_name = ctransifexHelperLanguage::code2ToName($iso[0]);
					}
					elseif (strlen($iso[0]) == 3)
					{
						$languages[$key]->iso_lang_name = ctransifexHelperLanguage::code3ToName($iso[0]);
					}

					if ((isset($iso[1]) && strlen($iso[1]) == 2))
					{
						$languages[$key]->iso_country_name = ctransifexHelperLanguage::code2ToCountry($iso[1]);
					}

					// Prepare values for array_multisort
					$sort['iso_lang_name'][$key] = $languages[$key]->iso_lang_name;
					$sort['completed'][$key] = $languages[$key]->completed;
				}
			}

			// Sort first on completed status and then on the real iso_lang_name
			array_multisort($sort['completed'], SORT_DESC, $sort['iso_lang_name'], SORT_ASC, $languages);
		}


		return $languages;
	}


	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    mixed    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_ctransifex.project', 'project', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed    The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_ctransifex.edit.project.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    the type
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @internal param \The $type table type to instantiate
	 * @return    JTable    A database object
	 */
	public function getTable($type = 'Project', $prefix = 'CtransifexTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}
