<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;


class UkrgbmapControllerAuthenticate extends JControllerBase
{
	public function execute()
	{
		// Get the document object.
        $app = Factory::getApplication();
        $user = Factory::getUser();

        $userId = $app->input->get('userid',Null);

        if (!is_null($userId)){
            echo json_encode(array(
                'username' => $user->username,
                'userid' => $user->id));
        } else {
            echo json_encode("Username: " . "unknown");
        }
	}
}
?>