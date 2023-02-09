<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;


class UkrgbmapControllerSaveMapPoints extends JControllerBase
{
    /*

    */
    public function execute()
	{
        $user = Factory::getUser();
        $model = new UkrgbmapModelMappoint;

        $app = JFactory::getApplication();
        $deletes = $app->input->post->get('delete', array(),'array');
        $updates = $app->input->post->get('update', array(),'array');

        $model->deleteMapPointsById($deletes);
        $model->updateMapPoints($updates);


        $app->close();
	}
}
