<?php
/**
 * UKRGB Map
 * @package  com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

// Site - Map Model

require_once JPATH_SITE . '/libraries/ukrgbgeo/proj4php/proj4php.php';

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class UkrgbmapModelMappoint extends JModelBase
{
    public $db;
    public $query;

    public function __construct(Registry $state = null)
    {
        $this->db = JFactory::getDbo();
        $this->query = $this->db->getQuery(true);
        parent::__construct($state);
    }

    public function getByMapId($mapId)
    {
        $this->query->where('mapid = ' . $this->db->Quote($mapId));
        return $this->doQuery();
    }

    public function getByMapType($mapType)
    {
        $this->query->where('type = ' . $this->db->Quote($mapType));
        return $this->doQuery();
    }

    public function getByRadius($centreLat, $centreLng, $radius)
    {
        // select points within a 'n' km radius of the point
        $this->query->where('ST_Distance_Sphere(point, GeomFromText(' .
            $this->db->quote('POINT(' . $centreLng . ' ' . $centreLat . ')') . ')) < ' . $radius * 1000);
        return $this->doQuery();
    }

    private function doQuery()
    {
        $this->query->select(array(
            $this->db->quoteName('id'),
            $this->db->quoteName('mapid'),
            $this->db->quoteName('riverguide'),
            'X(' . $this->db->quoteName('point') . ') AS X',
            'Y(' . $this->db->quoteName('point') . ') AS Y',
            $this->db->quoteName('type'),
            $this->db->quoteName('description')));
        $this->query->from('#__ukrgb_map_point');

        //error_log($query);
        $this->db->setQuery($this->query);

        try
        {
            $result = $this->db->loadObjectList();
        }
        catch (Exception $e)
        {
            // catch any database errors.
            error_log($e);
            $result = null;
        }
        return $result;
    }

    public function updateMapPointsFromArticle($text, $articleId, $description)
    {
        //error_log("updateMapPoints");
        /**
         * text =  article text
         * id = article id
         **/
        $pat = "/([STNOH][A-HJ-Z]\s?[0-9]{3,5}\s?[0-9]{3,5})/";
        $res = preg_match_all($pat, $text, $matches);
        if ($res > 0 && $res)
        {
            // Check for a map for this article, create a dummy map if one does not exist.
            $mapModel = new UkrgbmapModelMap();
            $mapId = $mapModel->getMapIdForArticle($articleId);
            if ($mapId == null)
            {
                // Create a map of type 1 (Auto Generated), type 0 must not be used (legacy auto generated)
                $mapId = $mapModel->addMap(1, $articleId);
            }
            if ($mapId) {
                // A non-null map ID was returned so create the map and points
                $proj4 = new Proj4php();
                $projWGS84 = new Proj4phpProj('EPSG:4326', $proj4);    # LatLon with WGS84 datum
                $projOSGB36 = new Proj4phpProj('EPSG:27700', $proj4);# UK Ordnance Survey, 1936 datum (OSGB36)
                // remove existing points
                $this->deleteMapPointsForArticle($articleId);
                $north = 0;
                $south = 1300000;
                $east = 0;
                $west = 800000;
                $grSet = array(); // Array used as a set to ensure the GR is processed only once.
                foreach ($matches[0] as $gr) {
                    $gr = str_replace(' ', '', $gr);

                    // Don't process the grid ref. if it is repeated in the guide.
                    if (!in_array($gr, $grSet)) {
                        $grSet[] = $gr;
                        $en = $this->OSGridtoNE($gr);
                        $pointSrc = new proj4phpPoint($en['x'], $en['y']);
                        $pointDest = $proj4->transform($projOSGB36, $projWGS84, $pointSrc);
                        $this->addMapPoint($pointDest, $mapId, 0, $description, $articleId);

                        // Calculate the extent of the map in    OSGB Nothings and eastings.
                        $north = max($north, $en['y']);
                        $south = min($south, $en['y']);
                        $east = max($east, $en['x']);
                        $west = min($west, $en['x']);
                    }
                }
                // Convert Map extent to WGS84
                $swSrc = new proj4phpPoint($west - 250, $south - 250);
                $neSrc = new proj4phpPoint($east + 250, $north + 250);
                $swDest = $proj4->transform($projOSGB36, $projWGS84, $swSrc);
                $neDest = $proj4->transform($projOSGB36, $projWGS84, $neSrc);

                // Add the map or Update the boundaries.

                $mapModel->updateMap(1, $swDest, $neDest, $mapId);
            }
        }
    }

    /**
     * Add Marker point to the DB
     *
     * @param object $point
     * @param int $mapId
     * @param int $type
     * @param string $description
     * @since v1.0
     **/
    public function addMapPoint($point, $mapId, $type, $description, $guideID)
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Insert columns.
        $columns = array('mapid', 'point', 'type', 'description', 'riverguide');

        // Insert values.
        $values = array(
            $db->quote($mapId),
            'GeomFromText(' . $db->quote('POINT(' . $point->x . ' ' . $point->y . ')') . ')',
            $db->quote($type),
            $db->quote($description),
            $db->quote($guideID));

        // Prepare the insert query.
        $query->insert($db->quoteName('#__ukrgb_map_point'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values))
        ;
        // Reset the query using our newly populated query object.

        $db->setQuery($query);

        try
        {
            $result = $db->query();
        }
        catch (Exception $e)
        {
            error_log($e);
        }
    }

    /** Delete all the Map Points for the specified Article
     *
     * @param $articleId
     * @since v1.0
     **/
    public function deleteMapPointsForArticle($articleId)
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__ukrgb_map_point'))
            ->where('riverguide = ' . $db->Quote($articleId))
        ;
        $db->setQuery($query);

        try
        {
            $result = $db->query(); // $db->execute(); for Joomla 3.0.
        }
        catch (Exception $e)
        {
            error_log($e);
        }
    }

    /**
     * Delete a Map Points for the specified IDs
     *
     * @param array $ids () $ids  The ids to be deleted
     *
     * @since v2.0
     */
    public function deleteMapPointsById($ids)
    {
        $values = implode(',', $ids);
        if ($values !== "")
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->delete($db->quoteName('#__ukrgb_map_point'));
            $query->where($db->quoteName('id') . ' IN (' . implode(',', ArrayHelper::toInteger($ids)) . ')');
            $db->setQuery($query);

            try
            {
                $result = $db->query(); // $db->execute(); for Joomla 3.0.
            }
            catch (Exception $e)
            {
                error_log($e);
            }
        }
    }

    public function updateMapPoints($points)
    {
        foreach($points as $point) {
           $this->updateMapPointById($point);
        }
    }

    /** Update a Map Point
     *
     * @param $point object
     * @since v2.0
     **/
    public function updateMapPointById($point)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $fields = array(
            $db->quoteName('riverguide') . ' = ' . $db->quote($point["riverguide"]),
            $db->quoteName('point') . ' = ' . 'GeomFromText(' . $db->quote('POINT(' . $point["X"] . ' ' . $point["Y"] . ')') . ')',
            $db->quoteName('type') . ' = ' . $db->quote($point["type"]),
            $db->quoteName('description') . ' = ' . $db->quote($point["description"]),
        );

        $conditions = array(
            $db->quoteName('id') . ' = ' . $db->quote($point["id"])
        );

        $query->update($db->quoteName('#__ukrgb_map_point'))->set($fields)->where($conditions);
        $db->setQuery($query);

        return $db->execute();
    }

    /**
     * Convert an Ordinance Survey zone (two-letter code) to distances from the reference point.
     *
     * @param $ossquare
     * @return float[]|int[]
     * @since 1.0
     */
    private function OSGridZonetoNE($ossquare)
    {
        // find the 500km square
        $lookup = array(
            'S' => array(0, 0),
            'T' => array(1, 0),
            'N' => array(0, 1),
            'O' => array(1, 1),
            'H' => array(0, 2),
        );

        $key = substr($ossquare, 0, 1);
        $offset = $lookup[$key];
        $easting = $offset[0] * 500;
        $northing = $offset[1] * 500;

        // find the 100km offset & add
        /** @noinspection SpellCheckingInspection */
        $grid = "VWXYZQRSTULMNOPFGHJKABCDE";
        $key = substr($ossquare, 1, 1);
        $posn = strpos($grid, $key);
        $easting += ($posn % 5) * 100;
        $northing += (int) ($posn / 5) * 100;
        return (array('x' => $easting * 1000, 'y' => $northing * 1000));

    }

    /**
     * OS GridRef to Northings and Eastings
     *
     * @param $osgrStr
     * @return array x - eastings, y - northings
     * @since 1.0
     */
    private function OSGridtoNE($osgrStr)
    {
        $osgrStr = str_replace(' ', '', $osgrStr);
        $zone = substr($osgrStr, 0, 2); // Leading letters
        $coords = substr($osgrStr, 2); // Numeric portion

        // reject odd number of digits
        //assert (len (coords) % 2 == 0),"'%s' must be an even number of digits" % coords

        // Calculate the size and resolution of numeric portion of the GR
        $rez = strlen($coords) / 2;
        $osgb_easting = substr($coords, 0, $rez);
        $osgb_northing = substr($coords, $rez);

        // what is each digit (in metres)
        $rez_unit = pow(10, 5 - $rez);
        $relEasting = $osgb_easting * $rez_unit;
        $relNorthing = $osgb_northing * $rez_unit;

        $en = $this->OSGridZonetoNE($zone);
        $zoneEasting = $en['x'];
        $zoneNorthing = $en['y'];

        return array(
            'x' => $zoneEasting + $relEasting,
            'y' => $zoneNorthing + $relNorthing);
    }

}

