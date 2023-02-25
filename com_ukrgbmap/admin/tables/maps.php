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
class UkrgbMapTableMaps extends JTable
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
}