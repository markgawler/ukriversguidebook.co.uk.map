<?php

defined('_JEXEC') or die;

class UkrgbmapModelAuthenticate extends JModelItem
{
    /*
     * Get an access token for the OS Map API
     */
    public function getAccessToken()
    {
        $getFromApi = function ()
        {
            $app = JFactory::getApplication();
            $params = $app->getParams();
            $apiKey = $params->get('api_key');
            $apiSecret = $params->get('api_secret');

            $ch = curl_init('https://api.os.uk/oauth2/token/v1');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD,$apiKey . ':' .$apiSecret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');

            $result = curl_exec($ch);
            // var_dump(json_decode($result)->access_token);
            curl_close($ch);
            // TODO: Error handling
            // Result {"access_token":"{accessToken}","expires_in":"{expiryPeriod}","issued_at":"{issuedTimestamp}","token_type":"BearerToken"}
            $t = json_decode($result);

            $res = new \stdClass;
            $res->accessToken = $t->access_token;
            $res->expiresAt = (int) microtime(TRUE) + (int) $t->expires_in;

            return $res;
        };

        /** @var JCacheControllerCallback $cache */
        $cache = JFactory::getCache('com_ukrgbmap', 'callback');
        $cacheId = 'com_ukrgbmap-' . 'access_token';
        /** @var JCache $cache */
        $cache->setCaching(true);
        $expiresIn = 0;
        $result = false;
        while ($expiresIn < 30) {
            $result = $cache->get($getFromApi, array(), $cacheId);
            $expiresIn = $result->expiresAt - (int)microtime(TRUE);
            if ($expiresIn < 30) {
                $cache->remove($cacheId, 'com_ukrgbmap');
            }
        }
        $result->expiresIn = $expiresIn;
        return $result;
    }
}