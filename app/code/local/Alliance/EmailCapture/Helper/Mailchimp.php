<?php

/**
 * Class Alliance_EmailCapture_Helper_Mailchimp
 */
class Alliance_EmailCapture_Helper_Mailchimp extends Mage_Core_Helper_Abstract
{
    private $api_key;
    private $api_endpoint = 'https://<dc>.api.mailchimp.com/2.0';
    private $verify_ssl = false;

    /**
     * Create a new instance
     */
    function __construct()
    {
        $this->api_key = Mage::getStoreConfig('emailcapture/mailchimp/api_key');
        list(, $datacentre) = explode('-', $this->api_key);
        $this->api_endpoint = str_replace('<dc>', $datacentre, $this->api_endpoint);
    }

    /**
     * Fetches the list ID from system configuration
     *
     * @return mixed
     */
    public function getListId()
    {
        return Mage::getStoreConfig('emailcapture/mailchimp/list_id');
    }

    /**
     * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
     *
     * @param $method
     * @param array $args
     * @param int $timeout
     * @return bool|mixed
     */
    public function call($method, $args = array(), $timeout = 10)
    {
        return $this->makeRequest($method, $args, $timeout);
    }

    /**
     * Performs the underlying HTTP request.
     *
     * @param $method
     * @param array $args
     * @param int $timeout
     * @return bool|mixed
     */
    private function makeRequest($method, $args = array(), $timeout = 10)
    {
        $args['apikey'] = $this->api_key;

        $url = $this->api_endpoint . '/' . $method . '.json';

        if (function_exists('curl_init') && function_exists('curl_setopt')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $json_data = json_encode($args);
            $result = file_get_contents($url, null, stream_context_create(array(
                'http' => array(
                    'protocol_version' => 1.1,
                    'user_agent' => 'PHP-MCAPI/2.0',
                    'method' => 'POST',
                    'header' => "Content-type: application/json\r\n" .
                        "Connection: close\r\n" .
                        "Content-length: " . strlen($json_data) . "\r\n",
                    'content' => $json_data,
                ),
            )));
        }

        return $result ? json_decode($result, true) : false;
    }
}