<?php

/**
 * Class Alliance_FiveHundredFriends_Helper_Api
 *
 * Used for all 500 Friends API requests
 */
class Alliance_FiveHundredFriends_Helper_Api extends Mage_Core_Helper_Abstract
{
    /**
     * Dispatches a pre-formatted request to the 500 Friends API and decodes the JSON response into an array
     *
     * @param $request
     * @return array
     */
    protected function _dispatch($request)
    {
        return json_decode($this->_curlGetContents($request), TRUE);
    }

    /**
     * Takes a set of request parameters, a secret key, and a uuid, and returns the correct sig hash for that set of request parameters
     *
     * @param array $request_parameters
     * @param $secret_key
     * @param $uuid
     * @return string
     */
    protected function _sigHash(array $request_parameters, $secret_key, $uuid)
    {
        $request_parameters['uuid'] = $uuid;
        ksort($request_parameters);
        $sig = $secret_key;
        foreach ($request_parameters as $key => $value) {
            $sig .= $key . $value;
        }
        return md5($sig);
    }

    /**
     * Like a url_fopen file_get_contents, but uses cURL for cross-server compatibility (especially with shared hosting)
     *
     * @param $url
     * @return mixed
     */
    protected function _curlGetContents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to an "enroll" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function enroll(array $request_parameters)
    {
        $request = 'https://loyalty.500friends.com/api/enroll.json?uuid='.Mage::helper('alliance_fivehundredfriends')->getUserId();

        foreach ($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }

    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to a "record" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function record(array $request_parameters)
    {
        $request = 'https://loyalty.500friends.com/api/record.json?uuid='.Mage::helper('alliance_fivehundredfriends')->getUserId();

        foreach ($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }
	
    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to an "reject" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function reject($request_parameters)
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://api.500friends.com/data/event/reject?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();
		
		$sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }
		
		$response = $this->_dispatch($request);
		return $response;
    }
	
	
	/**
	* Dispatches a "tiers" the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
	*
	* @return array
	*/
	public function tiers()
	{
		$uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
		$request = 'https://api.500friends.com/data/tiers?uuid='.$uuid;
		$secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();
		
		$request_parameters = array();
		$sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
		$request_parameters['sig'] = $sig;
		
		foreach($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }
		
		$response = $this->_dispatch($request);
		return $response;
	}
	
    /**
     * Dispatches the request to 500 Friends API consulting by 'data/rewards', and returns the response in the form of a JSON-decoded array
     *
     * @return array
     */
    public function getRewards()
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://loyalty.500friends.com/data/rewards?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();
		
		$request_parameters = array();
        $sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }
	
    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to an "reward_redeem" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function rewardRedemption($request_parameters)
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://loyalty.500friends.com/api/reward_redeem.json?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();
		
		$sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }
		
		$response = $this->_dispatch($request);
		return $response;
    }
	
	
    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to an "auth_token" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function customerAuthToken(array $request_parameters)
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://loyalty.500friends.com/data/customer/auth_token?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();

        $sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach ($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }

    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to a "customer/enroll" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function customerShow(array $request_parameters)
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://loyalty.500friends.com/data/customer/show?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();

        $sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach ($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }
	
    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to a "customer/rewards" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function customerRewards(array $request_parameters)
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://loyalty.500friends.com/data/customer/rewards?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();

        $sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach ($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }

    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to a "customer/update_email" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function updateEmail(array $request_parameters)
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://loyalty.500friends.com/data/customer/update_email?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();

        $sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach ($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }
	
    /**
     * Takes request parameters in the form of a multi-dimensional array, formats them as GET variables, attaches them to a "customer/update_customer_info" request,
     * dispatches the request to 500 Friends API, and returns the response in the form of a JSON-decoded array
     *
     * @param array $request_parameters
     * @return array
     */
    public function updateCustomerInfo(array $request_parameters)
    {
        $uuid = Mage::helper('alliance_fivehundredfriends')->getUserId();
        $request = 'https://loyalty.500friends.com/data/customer/update_customer_info?uuid='.$uuid;
        $secret_key = Mage::helper('alliance_fivehundredfriends')->getTokenKey();

        $sig = $this->_sigHash($request_parameters, $secret_key, $uuid);
        $request_parameters['sig'] = $sig;

        foreach ($request_parameters as $key => $value) {
            $request .= '&' . $key . '=' . urlencode($value);
        }

        return $this->_dispatch($request);
    }
}