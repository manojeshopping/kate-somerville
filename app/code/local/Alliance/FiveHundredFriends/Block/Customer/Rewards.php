<?php

/**
 * Class Alliance_FiveHundredFriends_Block_Customer_Rewards
 */
class Alliance_FiveHundredFriends_Block_Customer_Rewards extends Mage_Core_Block_Template
{
    /**
     * Returns current customer's email address
     *
     * @return string
     */
    protected function _getCustomerEmail()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    }

    /**
     * Returns current customer's auth token via 500 Friends
     *
     * @return string
     */
    protected function _getAuthToken()
    {
        $api = Mage::helper('alliance_fivehundredfriends/api');
        $request_parameters = array(
            'email' => $this->_getCustomerEmail(),
        );
        $response = $api->customerAuthToken($request_parameters);
		Mage::log($response, null, 'fivehundredfriends.log');

		if(! isset($response['data']) || ! isset($response['data']['auth_token'])) return '';
		
        return $response['data']['auth_token'];
    }

    /**
     * Returns args JSON string for current customer's Rewards widget
     *
     * @return string
     */
    public function getArgs()
    {
        return "{email: '" . $this->_getCustomerEmail() . "', auth_token: '" . $this->_getAuthToken() . "', auto_resize: true}";
    }

    /**
     * Returns the configured User ID for 500 Friends authentication, located in System Configuration
     * under 'alliance_fivehundredfriends/access_credentials/user_id'
     *
     * @return mixed
     */
    public function getUserId()
    {
        return Mage::helper('alliance_fivehundredfriends')->getUserId();
    }
}