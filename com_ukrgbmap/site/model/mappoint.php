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
    //public string|JDatabaseQuery $query;
    public  $query;
    //public JDatabaseQuery $query;

    public function __construct(Registry $state = null)
    {
        $this->db = JFactory::getDbo();
        parent::__construct($state);
    }

    public function getByMapId($mapId)
    {
        $this->query = $this->db->getQuery(true);
        $this->query->where('mapid = ' . $this->db->Quote($mapId));
        return $this->doQuery();
    }

    public function getByMapType($mapType)
    {
        $this->query = $this->db->getQuery(true);
        $this->query->where('type = ' . $this->db->Quote($mapType));
        return $this->doQuery();
    }

    /**
     * Get everything in the radius
     * @param $centreLat
     * @param $centreLng
     * @param $radius
     * @return array|mixed|null
     * @since 3.0.5
     */
    public function getByRadius($centreLat, $centreLng, $radius)
    {
        // select points within a 'n' km radius of the point
        $this->query = $this->db->getQuery(true);
        $this->query->where('ST_Distance_Sphere(point, GeomFromText(' .
            $this->db->quote('POINT(' . $centreLng . ' ' . $centreLat . ')') . ')) < ' . $radius * 1000);
        return $this->doQuery();
    }

    /**
     * Get everything for the map in the radius, and access points for other maps
     * @param $centreLat
     * @param $centreLng
     * @param $radius
     * @param $mapId
     * @return array|mixed|null
     * @since 3.0.5
     */
    public function getAccessPointsByRadius($centreLat, $centreLng, $radius, $mapId)
    {
        // select points within a 'n' km radius of the point
        // Only select point of type 0, 2, 3 & 4 (legacy, putin, takeout and access point)
        $this->query = $this->db->getQuery(true);
        $this->query->where('ST_Distance_Sphere(point, GeomFromText(' .
            $this->db->quote('POINT(' . $centreLng . ' ' . $centreLat . ')') . ')) < ' . $radius * 1000 .
            ' AND  ( type IN (0, 2, 3, 4) OR mapid = ' . $this->db->Quote($mapId) . ')');
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
        $mapId = null;
        $mapPat = "/{map}/i";
        $hasMap = preg_match($mapPat, $text);
        $updateMap = false;

        if ($hasMap) {
            // there is a map tag in the article
            // Check for a map for this article, create a dummy map if one does not exist.
            $mapModel = new UkrgbmapModelMap();
            $mapId = $mapModel->getMapIdForArticle($articleId);
            if (!$mapId) {
                // Create a map of type 1 (Auto Generated), type 0 must not be used (legacy auto generated)
                $mapId = $mapModel->addMap(1, $articleId);
                $updateMap = true; // its new so auto generate it!
            } else {
                // Only automatically update map that are auto generated. i.e. not manually modified.
                $updateMap = ($mapModel->getMapType($mapId) < 2);
            }
        }
        if ($mapId && $updateMap) {
            // A non-null map ID was returned so update the map and points
            $gridPat = "/([STNOH][A-HJ-Z]\s?[0-9]{3,5}\s?[0-9]{3,5})/";
            preg_match_all($gridPat, $text, $gridMatches);

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
            foreach ($gridMatches[0] as $gr) {
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

    /**
     * Add Marker point to the DB
     *
     * @param object $points
     * @param int $mapId
     * @since v3.0.5
     **/
    public function addMapPointsFromMap($points, $mapId )
    {
        $mapModel = new UkrgbmapModelMap ;

        // find the articleId from the MapId
        $article = $mapModel->getArticleIdFotMap($mapId) ;
        if ($article) {
            foreach ($points as $pt){
                $p = (object)["x"=> $pt["X"], "y"=> $pt["Y"]];
                $this->addMapPoint($p,$mapId,$pt["type"], $pt["description"], $article);
            }
        } else {
            error_log('No Article associated with Map');
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
        $this->query = $this->db->getQuery(true);

        // Insert columns.
        $columns = array('mapid', 'point', 'type', 'description', 'riverguide');

        // Insert values.
        $values = array(
            $this->db->quote($mapId),
            'GeomFromText(' . $this->db->quote('POINT(' . $point->x . ' ' . $point->y . ')') . ')',
            $this->db->quote($type),
            $this->db->quote($description),
            $this->db->quote($guideID));

        // Prepare the insert query.
        $this->query->insert($this->db->quoteName('#__ukrgb_map_point'))
            ->columns($this->db->quoteName($columns))
            ->values(implode(',', $values))
        ;
        // Reset the query using our newly populated query object.
        $this->db->setQuery($this->query);

        try
        {
            $this->db->execute();
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
     * @return void
     **/
    public function deleteMapPointsForArticle($articleId)
    {
        $this->query = $this->db->getQuery(true);
        $this->query = $this->db->getQuery(true)
            ->delete($this->db->quoteName('#__ukrgb_map_point'))
            ->where('riverguide = ' . $this->db->Quote($articleId))
        ;
        $this->db->setQuery($this->query);

        try
        {
            $this->db->execute();
        }
        catch (Exception $e)
        {
            error_log($e);
        }
    }

    /**
     * Delete Map Points by the specified IDs in Array
     *
     * @param array $ids () $ids  The ids to be deleted
     * @param int $map Map id to constrain deletes to.
     *
     * @since v2.0
     */
    public function deleteMapPointsById($ids, $map = 0)
    {
        $this->query = $this->db->getQuery(true);
        $values = implode(',', $ids);
        if ($values !== "")
        {
            $constraint = '';
            if ($map !== 0) {
                $constraint = ' AND ' . $this->db->quoteName('mapid') .' = ' . $this->db->quote($map);
            }
            $this->query->delete($this->db->quoteName('#__ukrgb_map_point'));
            $this->query->where($this->db->quoteName('id') . ' IN (' . implode(',', ArrayHelper::toInteger($ids))  . ')'. $constraint);
            $this->db->setQuery($this->query);

            try
            {
                $this->db->execute();
            }
            catch (Exception $e)
            {
                error_log($e);
            }
        }
    }

    /**
     * @param array $ids an array of MapPoint IDs to validate that they belong to the specified Map
     * @param int $map the owning Map ID
     * @return boolean
     * @since 3.0.5
     */
    public function validateMapPoints($ids,$map)
    {
        $this->query = $this->db->getQuery(true);
        $pointCount = count($ids);
        $values = implode(',', $ids);
        if ($values != "")
        {
            $this->query->select('COUNT(*)');
            $this->query->where($this->db->quoteName('id') . ' IN (' . implode(',', ArrayHelper::toInteger($ids)) .
            ') AND ' . $this->db->quoteName('mapid') .' = ' . $this->db->quote($map));
            $this->query->from('#__ukrgb_map_point');
            $this->db->setQuery($this->query);

            try
            {
                $count = $this->db->loadResult();
                return $count == $pointCount;
            }
            catch (Exception $e)
            {
                error_log($e);
            }
        }
        return false;
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
        $this->query = $this->db->getQuery(true);

        $fields = array(
            $this->db->quoteName('riverguide') . ' = ' . $this->db->quote($point["riverguide"]),
            $this->db->quoteName('point') . ' = ' . 'GeomFromText(' . $this->db->quote('POINT(' . $point["X"] . ' ' . $point["Y"] . ')') . ')',
            $this->db->quoteName('type') . ' = ' . $this->db->quote($point["type"]),
            $this->db->quoteName('description') . ' = ' . $this->db->quote($point["description"]),
        );

        $conditions = array(
            $this->db->quoteName('id') . ' = ' . $this->db->quote($point["id"])
        );

        $this->query->update($this->db->quoteName('#__ukrgb_map_point'))->set($fields)->where($conditions);
        $this->db->setQuery($this->query);

        return $this->db->execute();
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

