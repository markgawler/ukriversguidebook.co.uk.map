<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');


// Set some global property
$document = JFactory::getDocument();
//$document->addStyleDeclaration('.icon-helloworld {background-image: url(../media/com_helloworld/images/Tux-16x16.png);}');

if (!JFactory::getUser()->authorise('core.manage', 'com_ukrgbmap'))
{
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}


// Require helper file
JLoader::register('UkrgbMapHelper', JPATH_COMPONENT . '/helpers/ukrgbmap.php');

$controller	= JControllerLegacy::getInstance('UkrgbMap');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
