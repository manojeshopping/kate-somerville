<?php

/**
 * Class Alliance_FiveHundredFriends_Block_Enroll_Notenrolled
 */
class Alliance_FiveHundredFriends_Block_Enroll_Notenrolled extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Customer_Model_Customer
     */
    public $customer;

    /**
     * Loads the customer model if the customer is logged in
     */
    public function __construct()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->customer = Mage::getSingleton('customer/session')->getCustomer();
        }
    }
}