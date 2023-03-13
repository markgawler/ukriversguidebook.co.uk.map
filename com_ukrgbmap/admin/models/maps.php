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
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   3.0.1
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id',
                'map_type',
                'title'
            );
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout'))
        {
            $this->context .= '.' . $layout;
        }

        // Adjust the context to support forced languages.
        $forcedLanguage = $app->input->get('forcedLanguage', '', 'CMD');
        if ($forcedLanguage)
        {
            $this->context .= '.' . $forcedLanguage;
        }

        parent::populateState($ordering, $direction);

        // If there's a forced language then define that filter for the query where clause
        if (!empty($forcedLanguage))
        {
            $this->setState('filter.language', $forcedLanguage);
        }
    }

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
        // a - __content table
        // m - __ukrgb_maps table
        // c - __categories table
        $query->select(array('m.id as id, m.map_type as map_type','a.title'))
            ->from($db->quoteName('#__ukrgb_maps', 'm'))
            ->join('LEFT',$db->quoteName('#__content', 'a') . ' ON ' . $db->quoteName('m.articleid') . ' = ' . $db->quoteName('a.id'));

        // Join over the categories.
        $query->select($db->quoteName('c.title', 'category_title'))
            ->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = m.catid');

        // Filter: like / search
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $like = $db->quote('%' . $search . '%');
            $query->where('a.title LIKE ' . $like);
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'a.title');
        $orderDirn 	= $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}