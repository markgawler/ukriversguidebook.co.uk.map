<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_ukrgbmap
 *
 * @copyright   Copyright (C) 2005 - 2014 Mark Gawler, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Ukrgb  Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_ukrgbmap
 *
 */
class UkrgbmapController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 */
	protected $default_view = 'eventmanager';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean			$cachable	If true, the view output will be cached
	 * @param   array  $urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.

	 */
	public function display($cachable = false, $urlparams = false)
	{
		

		parent::display();

		return $this;
	}
}
