<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * UkrgbMapList Model
 *
 * @since  3.0.1
 */
class UkrgbMapModelMaps extends JModelList
{
    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     * @since 3.0
     */
    protected function getListQuery()
    {
        // Initialize variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Create the base select statement.
        $query->select(array('#__ukrgb_maps.id','#__ukrgb_maps.map_type','#__content.title'))
            ->from($db->quoteName('#__ukrgb_maps'))
            ->join('INNER',$db->quoteName('#__content') . ' ON ' . $db->quoteName('#__ukrgb_maps.articleid') . ' = ' . $db->quoteName('#__content.id'));

        // Filter: like / search
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $like = $db->quote('%' . $search . '%');
            $query->where('#__content.title LIKE ' . $like);
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', '#__content.title');
        $orderDirn 	= $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}