<?php

/**
 * Class Alliance_FiveHundredFriends_Model_Customer
 *
 * Models the currently logged in customer's 500 Friends account data
 */
class Alliance_FiveHundredFriends_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Customer_Model_Customer
     */
    public $customer;

    /**
     * JSON-decoded array of response from 500 Friends customer_show API request
     *
     * @var array
     */
    public $customer_show;

    /**
     * @var Alliance_FiveHundredFriends_Helper_Api
     */
    public $api;

    /**
     * Constructor builds the object based on the currently logged in customer's
     * 500 Friends customer_show data, if any
     */
    public function __construct()
    {
        $this->api = Mage::helper('alliance_fivehundredfriends/api');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->customer = Mage::getSingleton('customer/session')->getCustomer();
            $email = $this->customer->getEmail();
            $request_parameters = array(
                'email' => $email,
            );
            $this->customer_show = $this->api->customerShow($request_parameters);
        }
    }

    /**
     * Returns the currently logged in customer's 500 Friends account status
     * (usually 'active' or 'paused' or NULL)
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->customer_show['data']['status'];
    }
}