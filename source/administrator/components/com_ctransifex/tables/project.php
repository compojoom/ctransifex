<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 24.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

class CtransifexTableProject extends JTable
{
    /**
     * @param string $db
     * @internal param \A $JDatabaseDriver database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__ctransifex_projects', 'id', $db);
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    $pks     An optional array of primary key values to update.
     *                            If not set the instance property value is used.
     * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer  $userId  The user id of the user performing the operation.
     *
     * @return  boolean  True on success; false if $pks is empty.
     *
     * @link    http://docs.joomla.org/JTable/publish
     * @since   11.1
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('state = ' . (int) $state);

        // Build the WHERE clause for the primary keys.
        $query->where($k . ' = ' . implode(' OR ' . $k . ' = ', $pks));

        $this->_db->setQuery($query);
        $this->_db->execute();

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks))
        {
            $this->published = $state;
        }

        $this->setError('');
        return true;
    }

    /**
     * Overloaded check function
     *
     * @return  boolean  True on success, false on failure
     *
     * @see     JTable::check
     * @since   11.1
     */
    public function check()
    {
        if (trim($this->title) == '')
        {
            $this->setError(JText::_('COM_CTRANSIFEX_WARNING_PROVIDE_VALID_PROJECT_TITLE'));
            return false;
        }

        if (trim($this->alias) == '')
        {
            $this->alias = $this->title;
        }

        $this->alias = JApplication::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '')
        {
            $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        return true;
    }
}
