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
        //$model = new UkrgbmapModelAuthenticate();
        $app = JFactory::getApplication();
        $deletes = $app->input->post->get('delete', array(),'array');
        $updates = $app->input->post->get('update', array(),'array');

        

//        ob_start();
//        var_dump($deletes);
//        var_dump($updates);
//        $result = ob_get_clean();
//        error_log('P: ' .  $result);

        $app->close();
	}
}
