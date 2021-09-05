<?php

defined('_JEXEC') or die;

class UkrgbmapModelAuthenticate extends JModelItem
{
    /*
     * Get an access token for the OS Map API
     */
    public function getAccessToken()
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
        return json_decode($result);
    }
}