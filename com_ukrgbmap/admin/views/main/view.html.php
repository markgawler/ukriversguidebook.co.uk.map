<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
//jimport('joomla.application.component.view');

/**
 * UkrgbMap View
 *
 * @since 1.0
*/
class UkrgbMapViewMain extends JViewLegacy
{
	//protected $form;
	
	/**
	 * Ukrgb view display method
     *
     * @param string $tpl The name of the template
	 * @return void
     *
     * @since 1.0
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$this->items = $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        //$this->form	= $this->get('Form');

        // Check for errors.
        $errors = $this->get('Errors');
        if ($errors != null )
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

		// Set the toolbar
		$this->addToolBar();
        //JHtml::_('behavior.framework'); // Adding this line fix the error ReferenceError: Joomla is not defined

        // Display the template
		parent::display($tpl);
	}
	
	/**
	 * Setting the toolbar
     * @since 1.0
	 */
	protected function addToolBar()
	{
		$user = JFactory::getUser();
		
		JToolBarHelper::title('UK Rivers Guidebook - Maps');
		if ($user->authorise('core.admin', 'com_ukrgbmap'))
		{
            JToolbarHelper::addNew('ukrgbmap.add');
            JToolbarHelper::editList('ukrgbmap.edit');
            JToolbarHelper::deleteList('', 'ukrgbmap.delete');
			JToolbarHelper::preferences('com_ukrgbmap');
				
		}
		JToolbarHelper::divider();
		
	}
}