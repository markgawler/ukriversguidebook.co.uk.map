<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
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
