<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_ukrgbmap
 *
 * @copyright   Copyright (C) Mark Gawler. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
/**
 * UKRGB Map
 * @package  com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
class UkrgbmapControllerHelper
{
	public function parseController($app)
	{
		// Require specific controller if requested
		$tasks = explode('.', $app->input->get('task','default'));
		$task = ucfirst(strtolower($tasks[0]));
		$activity = '';
		if (!empty($tasks[1]))
		{
			$activity = ucfirst(strtolower($tasks[1]));
		}
		$controllerName = 'Ukrgbmap' . 'Controller' . $task . $activity;
        if (!class_exists($controllerName))
		{
			error_log("Error Log no such Controller: ".$controllerName);
			return false;
        }
        return new $controllerName;
	}
}