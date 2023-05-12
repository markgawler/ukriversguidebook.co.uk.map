<?php
/**
 * UKRGB Map
 *
 * @package        com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;


class UkrgbmapControllerSaveMapPoints extends JControllerBase
{
    /*

    */
    public function execute()
    {
        $model = new UkrgbmapModelMappoint;
        $app = JFactory::getApplication();
        if (JSession::checkToken())
        {
            $user = Factory::getUser();

            $mapId = $app->input->post->get('mapId', 0, 'int');
            $auth = $user->authorise('core.edit', 'com_ukrgbmap.map.' . $mapId);
            if ($auth && $mapId !== 0) {
                $deletes = $app->input->post->get('delete', array(), 'array');
                $updates = $app->input->post->get('update', array(), 'array');
                $model->deleteMapPointsById($deletes);
                $model->updateMapPoints($updates);
            } else {
                header("HTTP/1.0 403 Forbidden");
            }
        }
        else
        {
            header("HTTP/1.0 400 Bad Request");
        }
        $app->close();
    }
}
