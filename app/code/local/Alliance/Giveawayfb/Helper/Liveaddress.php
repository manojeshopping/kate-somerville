<?php

/**
 * Class Alliance_Giveawayfb_Helper_Liveaddress
 */
class Alliance_Giveawayfb_Helper_Liveaddress extends Mage_Core_Helper_Abstract
{
    private $auth_id;
    private $auth_token;

    /**
     * Constructor loads the Auth ID and Auth Token of the LiveAddress API from Magento's system configuration.
     *
     * IMPORTANT: This is intended to load RAW values only, not URL-encoded values! If there are URL-encoded values stored in system
     * configuration, it will likely break the code and API requests will return NULL
     */
    public function __construct()
    {
        $this->auth_id = Mage::getStoreConfig('alliance_giveawayfb/api_keys/auth_id');
        $this->auth_token = Mage::getStoreConfig('alliance_giveawayfb/api_keys/auth_token');
    }

    /**
     * Takes a street, city, and state and returns a JSON-encoded array of the LiveAddress API response
     *
     * @param string $street
     * @param string $city
     * @param string $state
     * @return array|mixed
     */
    public function streetAddress($street, $city, $state)
    {
        $street = urlencode($street);
        $city = urlencode($city);
        $state = urlencode($state);
        $auth_id = urlencode($this->auth_id);
        $auth_token = urlencode($this->auth_token);

        $request = "https://api.smartystreets.com/street-address/?street={$street}&city={$city}&state={$state}&auth-id={$auth_id}&auth-token={$auth_token}";
        $response = file_get_contents($request);

        return $response;
    }
}