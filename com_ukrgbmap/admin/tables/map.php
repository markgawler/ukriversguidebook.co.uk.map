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

    public function store($updateNulls = false): bool
    {
        // Ensure JTable does not attempt to update the Point columns if the map bounds have not been set.
        if ($this->sw_corner == "" || $this->ne_corner == "") {
            $this->sw_corner = null;
            $this->ne_corner = null;
        }
        return parent::store($updateNulls);
    }

    /**
     * Overloaded bind function
     *
     * @param       $src named array
     * @return      null|string     null is operation was satisfactory, otherwise returns an error
     * @see JTable:bind
     * @since 3.0.2
     */
    public function bind($src, $ignore = '')
    {
        if (isset($src['params']) && is_array($src['params']))
        {
            // Convert the params field to a string.
            $parameter = new JRegistry;
            $parameter->loadArray($src['params']);
            $src['params'] = (string)$parameter;
        }

        // Bind the rules.
        if (isset($src['rules']) && is_array($src['rules']))
        {
            $rules = new JAccessRules($src['rules']);
            $this->setRules($rules);
        }

        return parent::bind($src, $ignore);
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @return	string
     * @since	3.0.2
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_ukrgbmap.map.'.(int) $this->$k;
    }
    /**
     * Method to return the title to use for the asset table.
     *
     * @return	string
     * @since	3.0.2
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }
    /**
     * Method to get the asset-parent-id of the item
     *
     * @return	int
     * @since 3.0.2
     */
    protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
    {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();

        // Find the parent-asset
        if (($this->catid)&& !empty($this->catid))
        {
            // The item has a category as asset-parent
            $assetParent->loadByName('com_ukrgbmap.category.' . (int) $this->catid);
        }
        else
        {
            // The item has the component as asset-parent
            $assetParent->loadByName('com_ukrgbmap');
        }

        // Return the found asset-parent-id
        if ($assetParent->id)
        {
            $assetParentId=$assetParent->id;
        }
        return $assetParentId;
    }
}