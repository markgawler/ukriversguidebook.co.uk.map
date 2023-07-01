<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');


class UkrgbmapControllerMappoint extends JControllerBase
{
    public function execute()
    {
        $app = JFactory::getApplication();
        $model = new UkrgbmapModelMappoint;
        $mapId = $app->input->get('mapid', null);
        $mapType = $app->input->get('type', null);
        $radius = $app->input->get('radius', null);
        $centreLat = $app->input->get('lat', null);
        $centreLng = $app->input->get('lng', null);

        if (!is_null($mapType))
        {
            $points = $model->getByMapType($mapType);
            echo json_encode($points);
        }
        else if (!is_null($radius))
        {
            if ($mapId != null){
                // Get everything for the map in the radius, and access points for other maps
                $points = $model->getAccessPointsByRadius($centreLat, $centreLng, $radius, $mapId);
            } else {
                // Get everything in a radius
                $points = $model->getByRadius($centreLat, $centreLng, $radius);
            }
            echo json_encode($points);
        }
        JFactory::getApplication()->close();
    }
}
