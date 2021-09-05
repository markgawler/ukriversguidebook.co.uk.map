<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;


class UkrgbmapControllerAuthenticate extends JControllerBase
{
	public function execute()
	{
        $user = Factory::getUser();
        $model = new UkrgbmapModelAuthenticate();
        $accessToken = $model->getAccessToken();

        echo json_encode(array(
            'userId' => $user->id,
            'accessToken' => $accessToken->access_token,
            'expiresIn' => $accessToken->expires_in,
            'issuedAt'=> $accessToken->issued_at));
	}

}
