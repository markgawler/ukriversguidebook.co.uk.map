<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
//JHtml::_('behavior.tabstate');


if (!JFactory::getUser()->authorise('core.manage', 'com_ukrgbmap'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller	= JControllerLegacy::getInstance('UkrgbMap');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
