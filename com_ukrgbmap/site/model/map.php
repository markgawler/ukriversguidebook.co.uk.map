<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * UKRGB Map
 * @package  com_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @since 3.0.2
 */

// Site - Map Model

class UkrgbmapModelMap extends JModelBase
{
	/**
	 * Get the Map parameters
	 * @return array - with the map parameters
	 * (w_lng, s_lat, E_lng n_lat and map_type)
	 * Map Type:
	 * 0 - legacy auto generated
     * 1 - Auto generated
     * 2 - Manual
	 *
     * @since 1.0
	 */
	public function getMapParameters($mapid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('X(sw_corner)', 'Y(sw_corner)', 'X(ne_corner)', 'Y(ne_corner)' ,'map_type', 'articleid'));
		$query->from('#__ukrgb_maps');
		$query->where('id = ' . $db->Quote($mapid));
		$db->setQuery($query);

		$result = $db->loadRow();

		return array("w_lng" => $result[0],
				"s_lat" => $result[1],
				"e_lng" => $result[2],
				"n_lat" => $result[3],
				"map_type" => $result[4],
				"articleid" => $result[5]);
	}

    /**
     * Get the Map Type for Map
     * @return int -  the map type
     * Map Type:
     * 0 - legacy auto generated
     * 1 - Auto generated
     * 2 - Manual
     *
     * @since 3.0.5
     */
    public function getMapType($mapid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('map_type'));
        $query->from('#__ukrgb_maps');
        $query->where('id = ' . $db->Quote($mapid));
        $db->setQuery($query);

        $result = $db->loadResult();

        return (int) $result;
    }
    /**
     * Get the Map ID for the article
     * @param int $articleId
     * @return mixed - Map ID or null if not found
     * @since 1.0
     */
	public function getMapIdForArticle($articleId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id'));
		$query->from('#__ukrgb_maps');
		$query->where($db->quoteName('articleid') .' = '. $db->Quote($articleId));

		$db->setQuery($query);
		try {
			$result = $db->loadObject();
		} catch (Exception $e) {
			// catch any database errors.
			error_log($e);
			$result = null;
		}

		if ($result == null){
			return null; //No Map
		}
		return $result->id;
	}

    /**
     * Get the Article ID for the Map
     * @param int $mapId
     * @return mixed - Map ID or null if not found
     * @since 3.0.5
     */
    public function getArticleIdFotMap($mapId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('articleid'));
        $query->from('#__ukrgb_maps');
        $query->where($db->quoteName('id') .' = '. $db->Quote($mapId));

        $db->setQuery($query);
        try {
            $result = $db->loadObject();
        } catch (Exception $e) {
            // catch any database errors.
            error_log($e);
            $result = null;
        }

        if ($result == null){
            return null; //No Article
        }
        return $result->articleid;
    }

    /**
     * Add a new Map to the database
     *
     * @param integer $type (0 = legacy, 1 = Auto generated, 2 = manual)
     * @param integer $articleId the content article the map is linked to
     * @return mixed
     * @throws Exception
     *
     * @since 3.0.4
     */
	public function addMap($type, $articleId)
	{
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ukrgbmap/tables');
        $table = JTable::getInstance('Map', 'UkrgbMapTable');

        // Don't allow creation of Map type 0 as this is a legacy format which uses article/ guide id in the map points.
        if ($type == 0 ) {
            throw new Exception(JText::_('COM_UKRGBMAP_INVALID_MAP_TYPE'), 500);
        }
		$db = JFactory::getDbo();
        $props = array(
            'map_type' => $type,
            //'articleid' =>  $db->quote($articleId));
            'articleid' =>  $articleId);
        $result = $table->save($props);
        if ( $result && $table->id > 0) {
            return $table->id;
        }
        return false;
	}

    /**
     * @param object $sw
     * @param object $ne
     * @param int $mapId
     * @param int| null $type
     * @return void
     * @throws Exception
     * @since 3.0.4
     */
	public function updateMap($sw ,$ne , $mapId, $type)
	{
		/*
		 * Update A Map
		*/
        // Don't allow creation of Map type 0 as this is a legacy format which uses article/ guide id in the map points.
        if ($type === 0 ) {
            throw new Exception(JText::_('COM_UKRGBMAP_INVALID_MAP_TYPE'), 500);
        }

		//TODO: Do we need to check the map exists?
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
			
		// Insert values.
		$fields = array(
				$db->quoteName('sw_corner').' = '.'GeomFromText('.$db->quote('POINT('.$sw->x.' '.$sw->y.')').')',
				$db->quoteName('ne_corner').' = '.'GeomFromText('.$db->quote('POINT('.$ne->x.' '.$ne->y.')').')');
        if ($type > 0){
            $fields[] = $db->quoteName('map_type').' = '.$db->quote($type);
        }

		// Prepare the insert query.
		$query->update($db->quoteName('#__ukrgb_maps'))->set($fields)->where('id = '.$mapId);
		$db->setQuery($query);
		try {
			$db->query();
		} catch (Exception $e) {
			error_log($e);
		}
	}

    /**
     * @param int $type
     * @param int $mapId
     * @return true
     * @since 3.0.5
     */
    public function updateMapType($type, $mapId){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Insert values.
        $fields = array(
            $db->quoteName('map_type').' = '.$db->quote($type),
        );

        // Prepare the insert query.
        $query->update($db->quoteName('#__ukrgb_maps'))->set($fields)->where('id = '.$mapId);
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            error_log($e);
        }
        return true;
    }

    /**
     * @param integer  $mapId
     * @return array
     * @since 3.0.5
     */
    public function calculateMapBounds($points) {
        $maxLng = -180;
        $minLng = 180;
        $maxLat = -180;
        $minLat = 180;
        foreach ($points as $pt) {
            //if ($pt->x > $maxLng) { $maxLng = $pt->x; }
            $maxLng = ($pt->X > $maxLng) ? $pt->X : $maxLng;
            $minLng = ($pt->X < $minLng) ? $pt->X : $minLng;

            $maxLat = ($pt->Y > $maxLat) ? $pt->Y : $maxLat;
            $minLat = ($pt->Y < $minLat) ? $pt->Y : $minLat;
        }
        $ne = (object)["x"=> $maxLng, "y"=> $maxLat];
        $sw = (object)["x"=> $minLng, "y"=> $minLat];

        return ["ne" => $ne,"sw" => $sw];
    }
}

