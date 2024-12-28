<?php
/**
 * UKRGB Map
 * @package  plg_ukrgbtest
 *
 * @copyright  (C) 2024 Mark Gawler. <https://github.com/markgawler>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace UKRGB\Plugin\Content\Ukrgbtest\Extension;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class Ukrgbtest extends CMSPlugin implements SubscriberInterface
{
    /**
     * Load the language file on instantiation
     *
     * @var    boolean
     * @since  1.0
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     * @since 1.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare' => 'insertMap',
        ];
    }

    /** @noinspection PhpUnused */
    public function insertMap(Event $event): bool
    {
        if (!$this->getApplication()->isClient('site')) {
            return false;
        }

        [$context, $article, $params, $page] = array_values($event->getArguments());
        
        if ($context !== "com_content.article" && $context !== "com_content.featured") return false;

        /** @var WebAssetManager $wa */
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $wa = $this->getApplication()->getDocument()->getWebAssetManager();
        $wr = $wa->getRegistry();
        $wr->addRegistryFile('/media/com_ukrgbmap/joomla.asset.json');

        $wa->useScript('com_ukrgbmap/mapjs');
        $wa->useStyle('com_ukrgbmap/mapcss');
        $mapDiv = "<div id=\"app\"></div>";
        $pattern = "/{map}/i";
        $article->text = preg_replace($pattern, $mapDiv, $article->text);
        return true;
    }
}
