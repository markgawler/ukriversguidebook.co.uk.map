<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

// Load classes
JLoader::registerPrefix('Ukrgbmap', JPATH_COMPONENT);

// Application
$app = JFactory::getApplication();
$controllerHelper = new UkrgbmapControllerHelper;
$controller = $controllerHelper->parseController($app);
$controller->prefix = 'UkrgbMap';

// Perform the Request task
$controller->execute();



