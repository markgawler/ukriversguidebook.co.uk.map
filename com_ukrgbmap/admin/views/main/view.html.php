<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * UKRGB View
*/
class UkrgbmapViewmain extends JViewLegacy
{
	protected $form;
	
	/**
	 * Ukrgb view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$items = $this->get('Items');
		$this->form	= $this->get('Form');
		
		// Check for errors.
		if (!empty($this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;

		// Set the toolbar
		$this->addToolBar();
		
		// Display the template
		parent::display($tpl);
	}
	
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		$user = JFactory::getUser();
		
		JToolBarHelper::title('UK Rivers Guidebook - Maps');
		if ($user->authorise('core.admin', 'com_ukrgbmap'))
		{
			JToolbarHelper::preferences('com_ukrgbmap');
				
		}
		JToolbarHelper::divider();
		
	}
}