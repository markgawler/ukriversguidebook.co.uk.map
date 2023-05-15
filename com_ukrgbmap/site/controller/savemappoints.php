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
        $mapId = $app->input->post->get('mapId', 0, 'int');

        if (JSession::checkToken() && $mapId != 0)
        {
            $user = Factory::getUser();
            $auth = $user->authorise('core.edit', 'com_ukrgbmap.map.' . $mapId);
            if ($auth) {
                $deletes = $app->input->post->get('delete', array(), 'array');
                $updates = $app->input->post->get('update', array(), 'array');
                if ($deletes) {
                    // Check that the points being deleted belong to the map.
                    if ($model->validateMapPoints($deletes, $mapId)) {
                        $model->deleteMapPointsById($deletes);
                    } else {
                        header("HTTP/1.0 400 Bad Request");
                    }
                }
                if ($updates) {
                    // build an array of point IDs, also check that all the points are for this Map
                    $ids = array();
                    $valid = true;
                    foreach ($updates as $pt) {
                        $ids[] = (int)$pt["id"];
                        if ((int)$pt["mapid"] !== $mapId) {
                            $valid = false;
                        }
                    }
                    if ($valid && $model->validateMapPoints($ids, $mapId) ) {
                        $model->updateMapPoints($updates);
                    } else {
                        header("HTTP/1.0 400 Bad Request");
                    }
                }
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
