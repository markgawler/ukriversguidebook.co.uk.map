<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of UkrgbMap component.
 *
 * This class is called by Joomla!'s installer
 *
 * * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @since 3.0.6
 *
 */
class com_ukrgbmapInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param \stdClass $parent - Parent object calling this method.
     *
     * @return void
     * @since 3.0.6
     *
     */
    public function install($parent)
    {
        $parent->getParent()->setRedirectURL('index.php?option=com_ukrgbmap');
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param \stdClass $parent - Parent object calling this method.
     *
     * @return void
     * @since 3.0.6
     */
    public function uninstall($parent)
    {
    }

    /**
     * This method is called after a component is updated.
     * The script is to update any legacy maps which do not have an asset_id to have an asset
     * Once this is done the ACLs on the maps will work
     *
     * @param \stdClass $parent - Parent object calling object.
     *
     * @return void
     * @since 3.0.6
     */
    public function update($parent)
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('id'));
        $query->from('#__ukrgb_maps');
        $query->where($db->quoteName('asset_id') .' = 0');

        $db->setQuery($query);
        try {
            $result = $db->loadObjectList();
        } catch (Exception $e) {
            // catch any database errors.
            error_log($e);
        }

        if (!empty($result))
        {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ukrgbmap/tables');
            $table = JTable::getInstance('Map', 'UkrgbMapTable');
            foreach ($result as $row) {
                // Perform a dummy update on the table to force the allocation of an asset_id to the map.
                $props = array('id' => $row->id);
                $result = $table->save($props);
            }
        }

    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param string    $type    - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param \stdClass $parent  - Parent object calling object.
     *
     * @return void
     * @since 3.0.6
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Runs right after any installation action is performed on the component.
     *
     * @param string    $type    - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param \stdClass $parent  - Parent object calling object.
     *
     * @return void
     * @since 3.0.6
     */
    function postflight($type, $parent)
    {
    }
}