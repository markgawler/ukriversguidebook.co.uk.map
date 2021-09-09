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
        $app = JFactory::getApplication();

        echo json_encode(array(
            'userId' => $user->id,
            'accessToken' => $accessToken->accessToken,
            'expiresIn' => $accessToken->expiresIn));
        $app->close(); // TODO: fix json view
	}

}
