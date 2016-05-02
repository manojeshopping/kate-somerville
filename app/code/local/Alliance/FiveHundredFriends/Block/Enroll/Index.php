<?php

/**
 * Class Alliance_FiveHundredFriends_Block_Enroll_Index
 */
class Alliance_FiveHundredFriends_Block_Enroll_Index extends Mage_Core_Block_Template
{
    /**
     * Enrolled = Customer is logged in, and is enrolled in 500 Friends
     * Not Enrolled = Customer is logged in, but is not enrolled in 500 Friends
     * Not Logged In = Customer is not logged in
     *
     * @var string
     */
    public $customer_status;
    public $customer;
    public $api;
    public $customer_show;
    public $helper;

    /**
     * Loads object properties on instantiation
     */
    public function __construct()
    {
        $this->loadApi();
        $this->loadHelper();
        $this->loadCustomerStatus();
    }

    /**
     * Loads the 500 Friends API
     */
    protected function loadApi()
    {
        $this->api = Mage::helper('alliance_fivehundredfriends/api');
    }

    /**
     * Loads the FiveHundredFriends helper
     */
    protected function loadHelper()
    {
        $this->helper = Mage::helper('alliance_fivehundredfriends');
    }

    /**
     * Loads the customer's status as it pertains to the enrollment index
     *
     * Enrolled = Customer is logged in, and is enrolled in 500 Friends
     * Not Enrolled = Customer is logged in, but is not enrolled in 500 Friends
     * Not Logged In = Customer is not logged in
     */
    protected function loadCustomerStatus()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->customer = Mage::getSingleton('customer/session')->getCustomer();
            $email = $this->customer->getEmail();
            $request_parameters = array(
                'email' => $email,
            );
            $this->customer_show = $this->api->customerShow($request_parameters);

            if ($this->customer_show['success'] && $this->customer_show['data']['status'] == 'active') {
                $this->customer_status = 'Enrolled';
            } else {
                $this->customer_status = 'Not Enrolled';
            }
        } else {
            $this->customer_status = 'Not Logged In';
        }
    }
}