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
     */
    protected function getOptions()
    {
        $db    = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id,map_type,articleid');
        $query->from('#__ukrgb_maps');
        $db->setQuery((string) $query);
        $messages = $db->loadObjectList();
        $options  = array();

        if ($messages)
        {
            foreach ($messages as $message)
            {
                $options[] = JHtml::_('select.option', $message->id, $message->map_type,$message->articleid);
            }
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
