<?php
/**
 * UKRGB Map
 * @package  plg_ukrgbmap
 *
 * @copyright  (C) 2023 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if (!class_exists('UkrgbmapModelMap')) {
     require_once JPATH_SITE . '/components/com_ukrgbmap/model/map.php';
     require_once JPATH_SITE . '/components/com_ukrgbmap/model/mappoint.php';
}
class plgContentUkrgbMap extends JPlugin {
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param string $context The context of the content being passed to the plugin.
	 * @param object $article The article object.  Note $article->text is also available
	 * @param object $params  The article params
	 * @param int    $page    The 'page' number
     * @since   1.0
	 */
	public function onContentPrepare(string $context, $article, &$params,  $page = 0)
	{		
		$index_js = "";
		$index_css = "";
		if (isset($article->id) and $context == 'com_content.article')
		{
			$model = new UkrgbmapModelMap;
			$mapId = $model->getMapIdForArticle($article->id);
			
			if (isset($mapId)){
                $url = JURI::base() . 'index.php?option=com_ukrgbmap'; //&tmpl=raw&format=json';
                $mapData = $model->getMapParameters($mapId);
                $aid = $mapData['aid'];
				$mapData = base64_encode(json_encode($mapData));

				// Insert river-app js
				// The important bit is the '<script type="module" crossorigin' which is why I haven't used $document->addScript
                JFactory::getDocument()->addScriptDeclaration(
                    '</script><script type="module" crossorigin src="components/com_ukrgbmap/view/map/assets/' .
                    $index_js . '.js"></script><script>');
                
				// Load resources
				JHtml::_('stylesheet','components/com_ukrgbmap/view/map/assets/' . $index_css . '.css');

                // Replace the {map} annotation in the guide with the map plugin html
                $token = 'token="' . JSession::getFormToken() . '"'; // Add a token
                $bounds = 'bounds="' . $mapData . '"';
                $callback = 'callback="' . $url . '"';
                $guideId = 'guideid="' . $aid . '"';
                /** @noinspection HtmlUnknownAttribute */
                $mapDiv = sprintf("<div id=\"app\" mode=\"plugin\" %s %s %s %s %s></div>", $guideId, $aid, $callback, $bounds, $token);
				$pattern = "/{map}/i";
				$article->text = preg_replace($pattern, $mapDiv, $article->text);
			}
		}
	}
	
	/**
     * Hook to onContentAfterSave which triggers the generation of Map data from the article
     *
     * @param string  $context
     * @param object  $article
     * @param boolean $isNew
     * @since 1.0
     */
    public function onContentAfterSave(string $context, $article, bool $isNew)
	{
		if ($context == 'com_content.form')
		{	
			$model = new UkrgbmapModelMappoint;
			$model->updateMapPointsFromArticle($article->introtext, $article->id, $article->title);
		}				
	}

    public function onContentBeforeDelete($context, $data) {
        return true;
    }
}
