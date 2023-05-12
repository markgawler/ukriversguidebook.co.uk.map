<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @since 3.0
 */
class UkrgbMapControllerMap extends JControllerForm
{
    /**
     * Implement to allowAdd or not
     *
     * Not used at this time (but you can look at how other components use it....)
     * Overwrites: JControllerForm::allowAdd
     *
     * @param array $data
     * @return bool
     * @since 3.0.2
     */
    protected function allowAdd($data = array())
    {
        return parent::allowAdd($data);
    }
    /**
     * Overwrites: JControllerForm::allowEdit
     *
     * @param array $data
     * @param string $key
     * @return bool
     * @since 3.0.2
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
        if( !empty( $id ) )
        {
            return JFactory::getUser()->authorise( "core.edit", "com_ukrgbmap.map." . $id );
        }
    }
}