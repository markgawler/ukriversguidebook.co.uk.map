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
		$app_js = "";
		$chunk_vendors_js = "";
		$app_css = "";
		$chunk_vendors_css = "";
		if (isset($article->id) and $context == 'com_content.article')
		{
			$model = new UkrgbmapModelMap;
			$mapid = $model->getMapIdforArticle($article->id);
			
			if (isset($mapid)){

				$mapData = json_encode(array(
						'url' => JURI::base() . 'index.php?option=com_ukrgbmap&tmpl=raw&format=json',
						'mapdata' => $model->getMapParameters($mapid)));

				/** @var JDocumentHtml $document */
				$document = JFactory::getDocument();

				// Map parameters passed ina global variable
                $document->addScriptDeclaration('window.mapParams = ' .$mapData.';');

                // Preload resources
                $document->addHeadLink('components/com_ukrgbmap/view/map/js/app.' . $app_js . '.js', 'preload', 'rel', array('as' => 'script'));
                $document->addHeadLink('components/com_ukrgbmap/view/map/js/chunk-vendors.' . $chunk_vendors_js . '.js', 'preload', 'rel', array('as' => 'script'));
                $document->addHeadLink('components/com_ukrgbmap/view/map/css/app.' . $app_css . '.css', 'preload', 'rel', array('as' => 'style'));
                $document->addHeadLink('components/com_ukrgbmap/view/map/css/chunk-vendors.' . $chunk_vendors_css . '.css', 'preload', 'rel', array('as' => 'style'));

				// Load resources
				JHtml::_('stylesheet','components/com_ukrgbmap/view/map/css/app.' . $app_css . '.css');
				JHtml::_('stylesheet','components/com_ukrgbmap/view/map/css/chunk-vendors.' . $chunk_vendors_css . '.css');

                /** @noinspection HtmlUnknownTarget */
                $mapDiv = '<div id="app"></div><script src="components/com_ukrgbmap/view/map/js/chunk-vendors.' . $chunk_vendors_js . '.js"></script><script src="components/com_ukrgbmap/view/map/js/app.' . $app_js . '.js"></script>';
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
