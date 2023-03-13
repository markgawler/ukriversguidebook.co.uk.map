<?php
/**
 * UKRGB Map
 *
 * @package        com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

/**
 * UKRGB Map Form Field class for the UkrgbMap component
 *
 * @since  3.0.1
 */
class JFormFieldMap extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var         string
     * @since 3.0.1
     */
    protected $type = 'Map';

    /**
     * Method to get a list of options for a list input.
     *
     * @return  array  An array of JHtml options.
     * @since 3.0.1
     */


    protected function getOptions()
    {
        $db    = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('#__ukrgb_maps.id as id,map_type,articleid,#__categories.title as category,catid');
        $query->from('#__ukrgb_maps');
        $query->leftJoin('#__categories on catid=#__categories.id');
        $db->setQuery((string) $query);
        $maps = $db->loadObjectList();
        $options  = array();

        if ($maps)
        {
            foreach ($maps as $map)
            {
                $options[] = JHtml::_('select.option', $map->id, $map->map_type,$map->articleid);
            }
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
