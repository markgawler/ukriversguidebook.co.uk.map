<?php

/**
 * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class UkrgbMapController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
     * @since 1.0
	 */
    protected $default_view = 'main';
    protected $model_prefix = "UkrgbMapModel";

	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cacheable If true, the view output will be cached
	 * @param   array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
     * @since 1.0
	 */
//	public function display($cacheable = false, $urlparams = false): JController
//    {
//		parent::display();
//		return $this;
//	}
}
