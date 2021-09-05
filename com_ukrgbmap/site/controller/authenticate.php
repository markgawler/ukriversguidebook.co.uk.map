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
        $model = new UkrgbmapModelAuthenticate();

        $userId = $app->input->get('userid',Null);
        $accessToken = $model->getAccessToken();

        if (!is_null($userId)){
            echo json_encode(array(
                'username' => $user->username,
                'userId' => $user->id,
                'accessToken' => $accessToken->access_token,
                'expiresIn' => $accessToken->expires_in,
                'issuedAt'=> $accessToken->issued_at));
        } else {
            echo json_encode("Username: " . "unknown");
        }
	}

}
