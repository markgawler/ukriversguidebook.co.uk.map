<?php
/**
 * UKRGB Map
 *
 * @package        com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * HTML View class for the HelloWorld Component
 *
 * @since  1.0
 */
class UkrgbMapViewDefaultHtml extends JViewLegacy
{
	function diaplay($tpl = null)
	{

        // Assign data to the view
        $this->msg = $this->get('Msg');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }

        // Display the view
        parent::display($tpl);
	}
}