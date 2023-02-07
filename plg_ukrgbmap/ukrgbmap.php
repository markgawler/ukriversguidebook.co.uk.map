<?php
/**
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2012 Mark Gawler. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
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
		$index_js = "";
		$index_css = "";
		if (isset($article->id) and $context == 'com_content.article')
		{
			$model = new UkrgbmapModelMap;
			$mapid = $model->getMapIdForArticle($article->id);
			
			if (isset($mapid)){
			    //$userId = Factory::getUser()->id;
                $url = JURI::base() . 'index.php?option=com_ukrgbmap'; //&tmpl=raw&format=json';
                $mapData = $model->getMapParameters($mapid);
                $aid = $mapData['aid']; //TODO is $aid always equal to $article->id
				$mapData = base64_encode(json_encode($mapData));

				/** @var JDocumentHtml $document */
				$document = JFactory::getDocument();

				// Insert river-app js
				// The important bit is the '<script type="module" crossorigin' which is why I haven't used $document->addScript
                $document->addScriptDeclaration(
                    '</script><script type="module" crossorigin src="components/com_ukrgbmap/view/map/assets/' . $index_js . '.js"></script><script>');
                
				// Load resources
				JHtml::_('stylesheet','components/com_ukrgbmap/view/map/assets/' . $index_css . '.css');

                // Replace the {map} anotation in the guide with the map plugin html
				$mapDiv = '<div id="app" mode="plugin" guideid="' . $aid . '" callback="' . $url . '"bounds="' . $mapData . '"></div>';
				$pattern = '/{map}/i'; 
				$article->text = preg_replace($pattern, $mapDiv, $article->text);
			}
		}
	}
	
	public function onContentAfterSave($context, $article, $isNew)
	{
		if ($context == 'com_content.form')
		{	
			$model = new UkrgbmapModelMappoint;
			$model->updateMapPoints($article->introtext, $article->id, $article->title);
		}				
	}
}
