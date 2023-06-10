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
    /**
     * Controller for handling updates from Map Web App
     * @throws  \Exception
     * @since 3.0
     **/
    public function execute()
    {
        $mapPointModel = new UkrgbmapModelMappoint;
        $app = Factory::getApplication();
        $mapId = $app->input->post->get('mapId', 0, 'int');

        if (JSession::checkToken() && $mapId != 0)
        {
            $user = Factory::getUser();
            $auth = $user->authorise('core.edit', 'com_ukrgbmap.map.' . $mapId);
            if ($auth) {
                $deletesPts = $app->input->post->get('delete', array(), 'array');
                $updatesPts = $app->input->post->get('update', array(), 'array');
                $newPts = $app->input->post->get('new', array(), 'array');

                if ($deletesPts) {
                    // Check that the points being deleted belong to the map.
                    if ($mapPointModel->validateMapPoints($deletesPts, $mapId)) {
                        $mapPointModel->deleteMapPointsById($deletesPts, $mapId);
                    } else {
                        header("HTTP/1.0 400 Bad Request");
                    }
                }
                if ($updatesPts) {
                    // build an array of point IDs, also check that all the points are for this Map
                    $ids = array();
                    $valid = true;
                    foreach ($updatesPts as $pt) {
                        $ids[] = (int)$pt["id"];
                        if ((int)$pt["mapid"] !== $mapId) {
                            $valid = false;
                        }
                    }
                    if ($valid && $mapPointModel->validateMapPoints($ids, $mapId) ) {
                        $mapPointModel->updateMapPoints($updatesPts);
                    } else {
                        header("HTTP/1.0 400 Bad Request");
                    }
                }
                if ($newPts) {
                    $mapPointModel->addMapPointsFromMap($newPts,$mapId);
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
