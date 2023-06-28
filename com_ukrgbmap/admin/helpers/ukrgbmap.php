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
     * Configure the Link bar.
     *
     * @return void
     * @since 3.0.1
     */

    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_UKRGBMAP_SUBMENU_MESSAGES'),
            'index.php?option=com_ukrgbmap',
            $vName == 'maps'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_UKRGBMAP_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&view=categories&extension=com_ukrgbmap',
            $vName == 'categories'
        );

        // Set some global property
        $document = JFactory::getDocument();
        if ($vName == 'categories')
        {
            $document->setTitle(JText::_('COM_UKRGBMAP_ADMINISTRATION_CATEGORIES'));
        }
    }
}
