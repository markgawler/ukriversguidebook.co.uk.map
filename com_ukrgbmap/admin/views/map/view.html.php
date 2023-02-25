<?php
/**
 * UKRGB Map
 *
 * @package        com_ukrgbmap Administrator
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * UkrgbMap View
 *
 * @since 3.0.1
 */
class UkrgbMapViewMap extends JViewLegacy
{
    /**
     * View form
     *
     * @var   form
     * @since 3.0.1
     */
    protected $form = null;

    /**
     * Display the UKRGB Map view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  boolean
     * @throws \Exception
     * @since 3.0
     */
    public function display($tpl = null)
    {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }


        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     * @throws \Exception
     * @since   3.0
     */
    protected function addToolBar()
    {
        $input = JFactory::getApplication()->input;

        // Hide Joomla Administrator Main menu
        $input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        if ($isNew)
        {
            $title = JText::_('JTOOLBAR_NEW');
        }
        else
        {
            $title = JText::_('JTOOLBAR_EDIT');
        }

        JToolbarHelper::title($title, 'ukrgbmap');
        JToolbarHelper::save('ukrgbmap.save');
        JToolbarHelper::cancel(
            'ukrgbmap.cancel',
            $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
        );
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     * @since 3.0.1
     */
    protected function setDocument()
    {
        $isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? JText::_('COM_UKRGBMAP_MAP_CREATING') :
            JText::_('COM_UKRGBMAP_MAP_EDITING'));
    }
}
