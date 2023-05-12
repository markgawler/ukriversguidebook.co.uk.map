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
    protected $item;
    protected $script;
    protected $canDo;

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
        //$this->script = $this->get('Script');  // what's this validation?

        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = JHelperContent::getActions('com_ukrgbmap', 'map', $this->item->id);

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors), 500);
        }


        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        //$this->setDocument();
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

        JToolBarHelper::title($isNew ? JText::_('JTOOLBAR_NEW')
            : JText::_('JTOOLBAR_EDIT'), 'map');

        // Build the actions for new and existing records.
        if ($isNew)
        {
            // For new records, check the create permission.
            if ($this->canDo->get('core.create'))
            {
                JToolBarHelper::apply('map.apply', 'JTOOLBAR_APPLY');
                JToolbarHelper::save('map.save', 'JTOOLBAR_SAVE');

            }
            JToolbarHelper::cancel('map.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            if ($this->canDo->get('core.edit'))
            {
                // We can save the new record
                JToolBarHelper::apply('map.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('map.save', 'JTOOLBAR_SAVE');

                // We can save this record, but check the create permission to see
                // if we can return to make a new one.
//                if ($this->canDo->get('core.create'))
//                {
//                    JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png',
//                        'JTOOLBAR_SAVE_AND_NEW', false);
//                }
            }
//            if ($this->canDo->get('core.create'))
//            {
//                JToolBarHelper::custom('helloworld.save2copy', 'save-copy.png', 'save-copy_f2.png',
//                    'JTOOLBAR_SAVE_AS_COPY', false);
//            }
            JToolBarHelper::cancel('map.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
