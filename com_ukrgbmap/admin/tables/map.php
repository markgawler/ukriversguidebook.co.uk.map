<?php

/**
 * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

/**
 * UKRGB Map Table class
 *
 * @since  3.0.1
 */
class UkrgbMapTableMap extends JTable
{
    /**
     * Constructor
     *
     * @param JDatabaseDriver  &$db A database connector object
     * @since 3.0.1
     */
    function __construct(&$db)
    {
        parent::__construct('#__ukrgb_maps', 'id', $db);
    }

    public function store($updateNulls = false)
    {
        // Ensure JTable does not attempt to update the Point columns if the map bounds have not been set.
        if ($this->sw_corner == "" || $this->ne_corner == "") {
            $this->sw_corner = null;
            $this->ne_corner = null;
        }
        return parent::store($updateNulls);
    }
}