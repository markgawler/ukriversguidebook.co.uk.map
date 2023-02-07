<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_ukrgbmap
 *
 * @copyright   Copyright (C) Mark Gawler All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Load classes
JLoader::registerPrefix('Ukrgbmap', JPATH_COMPONENT);

// Application
$app = JFactory::getApplication();
$controllerHelper = new UkrgbmapControllerHelper;
$controller = $controllerHelper->parseController($app);
$controller->prefix = 'Ukrgbmap';

// Perform the Request task
$controller->execute();



