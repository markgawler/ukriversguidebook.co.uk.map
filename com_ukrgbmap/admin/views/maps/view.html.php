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
class UkrgbMapViewMaps extends JViewLegacy
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
        // Get application
        $app = JFactory::getApplication();
        $context = "ukrgbmap.list.admin.map";
		// Get data from the model
		$this->items = $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->filter_order 	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'title', 'cmd');
        $this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');

        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = JHelperContent::getActions('com_ukrgbmap');

        // Check for errors.
        $errors = $this->get('Errors');
        if ($errors != null )
        {
            throw new Exception(implode("\n", $errors), 500);
        }

        // Set the submenu
        UkrgbMapHelper::addSubmenu('maps');

        // Set the toolbar
		$this->addToolBar();

        // Display the template
		parent::display($tpl);

        // Set the document
        $this->setDocument();
	}
	
	/**
	 * Setting the toolbar
     * @since 3.0.1
	 */
	protected function addToolBar()
	{
		$user = JFactory::getUser();
        $title = JText::_('COM_UKRGBMAP_HEADING');
		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}
        JToolBarHelper::title($title, 'ukrgbmap');
        //JToolBarHelper::title('UK Rivers Guidebook - Maps');
        if ($this->canDo->get('core.create'))
        {
            JToolbarHelper::addNew('map.add');
        }
        if ($this->canDo->get('core.edit'))
        {
            JToolbarHelper::editList(',map.edit');
        }
        if ($this->canDo->get('core.delete'))
        {
            JToolbarHelper::deleteList('', 'maps.delete');
        }
        //if ($user->authorise('core.admin', 'com_ukrgbmap'))
        if ($this->canDo->get('core.admin'))
        {
            JToolBarHelper::divider();
            JToolbarHelper::preferences('com_ukrgbmap');
		}
		JToolbarHelper::divider();
		
	}
    /**
     * Method to set up the document properties
     *
     * @return void
     * @since 3.0.1
     */
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_UKRGBMAP_ADMINISTRATION'));
    }
}