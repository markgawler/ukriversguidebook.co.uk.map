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

/**
 * UKRGB Map component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   3.0.1
 */
abstract class UkrgbMapHelper extends JHelperContent
{
    /**
     * Configure the Linkbar.
     *
     * @return Bool
     * @since 3.0.1
     */

    public static function addSubmenu($submenu)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_UKRGBMAP_SUBMENU_MESSAGES'),
            'index.php?option=com_ukrgbmap',
            $submenu == 'maps'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_UKRGBMAP_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&view=categories&extension=com_ukrgbmap',
            $submenu == 'categories'
        );

        // Set some global property
        $document = JFactory::getDocument();
        //$document->addStyleDeclaration('.icon-48-helloworld ' .
        //    '{background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
        if ($submenu == 'categories')
        {
            $document->setTitle(JText::_('COM_UKRGBMAP_ADMINISTRATION_CATEGORIES'));
        }
    }
}
