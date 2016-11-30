<?php
/**
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2012 Mark Gawler. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . '/components/com_ukrgbmap/model/map.php';
require_once JPATH_SITE . '/components/com_ukrgbmap/model/mappoint.php';

class plgContentUkrgbMap extends JPlugin {
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{		

		if (isset($article->id) and $context == 'com_content.article')
		{
			$model = new UkrgbmapModelMap;
			$mapid = $model->getMapIdforArticle($article->id);
			
			if (isset($mapid)){
				
				JHtml::_('behavior.framework');
				JHtml::_('script', 'https://openlayers.org/api/OpenLayers.js');
				JHtml::_('script', 'libraries/ukrgbgeo/proj4js/proj4js-compressed.js');
				JHtml::_('script', 'components/com_ukrgbmap/view/map/js/OpenSpace.js');
				JHtml::_('script', 'components/com_ukrgbmap/view/map/js/map-openlayers.js');
				JHtml::_('stylesheet','components/com_ukrgbmap/view/map/css/map.css');
				
				$mapDiv = '<div id="map" class="ukrgbmap"></div>
				<form class="ukrgbmapgr form-inline">
				<label>OS Grid Ref: </label><input class="input-small uneditable-input" type="text" id="GridRef" readonly>
				<label>WGS84 Lat: </label><input class="input-mini uneditable-input" type="text" id="Lat"  readonly>
				<label>Lng: </label><input class="input-mini uneditable-input" type="text" id="Lng" readonly>
				</form>';
	
				$mapData = json_encode(array(
						'url' => JURI::base() . 'index.php?option=com_ukrgbmap&tmpl=raw&format=json',
						'mapdata' => $model->getMapParameters($mapid)));
				
				$document = JFactory::getDocument();
				$document->addScriptDeclaration('var params = ' .$mapData.';');
				
				$pattern = '/{map}/i'; 
				$article->text = preg_replace($pattern, $mapDiv, $article->text);
			}
		}
	}
	
	public function onContentAfterSave($context, $article, $isNew)
	{

		//if ($context == 'com_content.article')
		
		if ($context == 'com_content.form')
		{	
			$model = new UkrgbmapModelMappoint;
			$model->updateMapPoints($article->introtext, $article->id, $article->title);
		}				
	}
}
