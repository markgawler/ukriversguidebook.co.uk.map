<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

// Site - Map Model

require_once JPATH_SITE . '/libraries/ukrgbgeo/proj4php/proj4php.php';

class UkrgbmapModelMappoint extends JModelBase
{
    public $db;
    public $query;

    public function __construct(\Joomla\Registry\Registry $state = null)
    {
        $this->db = JFactory::getDbo();
        $this->query = $this->db->getQuery(true);
        parent::__construct($state);
    }

    public function getByGuideId($guideId)
	{
        $this->query->where('riverguide = '. $this->db->Quote($guideId));
        return $this->doQuery();
	}

	public function getByMapType($mapType)
	{
		$this->query->where('type = '. $this->db->Quote($mapType));
        return $this->doQuery();
	}

    public function getByRadius($centreLat, $centreLng, $radius)
    {
        // select points within a 'n' km radius of the point
        $this->query->where('ST_Distance_Sphere(point, GeomFromText('.
            $this->db->quote('POINT('.$centreLng.' '.$centreLat.')').')) < ' . $radius * 1000);
        return $this->doQuery();
    }

    private function doQuery()
    {
        $this->query->select(array(
            $this->db->quoteName('id'),
            $this->db->quoteName('riverguide'),
            'X('.$this->db->quoteName('point').') AS X',
            'Y('.$this->db->quoteName('point').') AS Y',
            $this->db->quoteName('type'),
            $this->db->quoteName('description')));
        $this->query->from('#__ukrgb_map_point');

        //error_log($query);
        $this->db->setQuery($this->query);

        try {
            $result = $this->db->loadObjectList();
        } catch (Exception $e) {
            // catch any database errors.
            error_log($e);
            $result = null;
        }
        return $result;
    }

	public function updateMapPoints($text,$articleId,$description)
	{
		//error_log("updateMapPoints");
		/**
		text =  article text
		id = article id
		**/
		$pat = 	"/([STNOH][A-HJ-Z]\s?[0-9]{3,5}\s?[0-9]{3,5})/";
		$res = preg_match_all ( $pat , $text, $matches);
		if ($res >0 && $res != False){

			$proj4 = new Proj4php();
			$projWGS84 = new Proj4phpProj('EPSG:4326',$proj4);	# LatLon with WGS84 datum
			$projOSGB36 = new Proj4phpProj('EPSG:27700',$proj4);# UK Ordnance Survey, 1936 datum (OSGB36)
			// remove existing points
			$this->deleteMapPointsForArticle($articleId);
			$north = 0;
			$south = 1300000;
			$east = 0;
			$west = 800000;
			$grSet = array(); // Array used as a set to ensure the GR is processed only once.
			foreach ($matches[0] as $gr){
				$gr = str_replace(' ', '', $gr);
					
				// Don't process the grid ref. if it is repeated in the guide.
				if (!in_array($gr,$grSet)){
					$grSet[] = $gr;
					$en = $this->OSGridtoNE($gr);
					$pointSrc = new proj4phpPoint($en['x'],$en['y']);
					$pointDest = $proj4->transform($projOSGB36,$projWGS84,$pointSrc);
					$this->addMapPoint($pointDest, $articleId, 0 ,$description);

					// Calculate the extent of the map in OSGB Nothings and eastings.
					$north = max($north,$en['y']);
					$south = min($south,$en['y']);
					$east = max($east,$en['x']);
					$west = min($west,$en['x']);
				}
			}
			// Convert Map extent to WGS84
			$swSrc = new proj4phpPoint($west-250,$south-250);
			$neSrc = new proj4phpPoint($east+250,$north+250);
			$swDest = $proj4->transform($projOSGB36,$projWGS84,$swSrc);
			$neDest = $proj4->transform($projOSGB36,$projWGS84,$neSrc);

			// Add the map or Update the boundaries.
			$mapmodel = new UkrgbmapModelMap();
			if ($mapmodel->getMapIdForArticle($articleId) == null)
			{
				$mapmodel->addMap(0,$swDest,$neDest,$articleId);
			} else {
				$mapmodel->updateMap(0,$swDest,$neDest,$articleId);
			}
		}
	}

	/* 
	* Add Marker point to the DB
	*/
	public function addMapPoint ($point,$riverguide,$type,$description){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
			
		// Insert columns.
		$columns = array('riverguide', 'point', 'type', 'description');

		// Insert values.
		$values = array(
				$db->quote($riverguide),
				'GeomFromText('.$db->quote('POINT('.$point->x.' '.$point->y.')').')',
				$db->quote($type),
				$db->quote($description));

		// Prepare the insert query.
		$query->insert($db->quoteName('#__ukrgb_map_point'))
		->columns($db->quoteName($columns))
		->values(implode(',', $values));
		// Reset the query using our newly populated query object.

		$db->setQuery($query);

		try {
			$result = $db->query();
		} catch (Exception $e) {
			error_log($e);
		}
	}

	public function deleteMapPointsForArticle ($articleId){
		/* Delete all the Map Points for the specified Article
		 *
		*/
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__ukrgb_map_point'));
		$query->where('riverguide = '. $db->Quote($articleId));
		$db->setQuery($query);

		try {
			$result = $db->query(); // $db->execute(); for Joomla 3.0.
		} catch (Exception $e) {
			error_log($e);
		}
	}


	private function OSGridZonetoNE ($ossquare)
	{
		/*
		 Convert an Ordinance Survey zone (2 letter code) to distances from the reference point.
		*/

		// find the 500km square
		$lookup = array(
				'S' => array(0,0),
				'T' => array(1,0),
				'N' => array(0,1),
				'O' => array(1,1),
				'H' => array(0,2)
		);

		$key = substr($ossquare,0,1);
		$offset = $lookup[$key];
		$easting = $offset[0] * 500;
		$northing = $offset[1] * 500;
			
		// find the 100km offset & add
		$grid = "VWXYZQRSTULMNOPFGHJKABCDE";
		$key = substr($ossquare,1,1);
		$posn = strpos($grid,$key);
		$easting += ($posn % 5) * 100;
		$northing += (int)($posn / 5) * 100;
		return (array('x' => $easting * 1000, 'y' => $northing * 1000));

	}

	private function OSGridtoNE($osgrStr)
	{
		$osgrStr = str_replace(' ', '', $osgrStr);
		$zone = substr($osgrStr,0,2); // Leading letters
		$coords = substr($osgrStr,2); // Numeric portion

		// reject odd number of digits
		//assert (len (coords) % 2 == 0),"'%s' must be an even number of digits" % coords

		// Calculate the size and resolution of numeric portion of the GR
		$rez = strlen($coords) / 2;
		$osgb_easting = substr($coords,0,$rez);
		$osgb_northing = substr($coords,$rez);

		// what is each digit (in metres)
		$rez_unit = pow(10,5-$rez);
		$relEasting =   $osgb_easting * $rez_unit;
		$relNorthing =  $osgb_northing * $rez_unit;

		$en =  $this->OSGridZonetoNE($zone);
		$zoneEasting = $en['x'];
		$zoneNorthing = $en['y'];

		return array(
				'x'=>$zoneEasting + $relEasting,
				'y'=>$zoneNorthing + $relNorthing);
	}

}

