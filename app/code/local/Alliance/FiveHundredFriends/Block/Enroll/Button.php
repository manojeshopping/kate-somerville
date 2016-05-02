<?php

/**
 * Class Alliance_FiveHundredFriends_Block_Enroll
 *
 * Block for 500 Friends One-Click Enrollment Button
 */
class Alliance_FiveHundredFriends_Block_Enroll_Button extends Mage_Core_Block_Template
{
    /**
     * Currently logged in customer model
     *
     * @var Mage_Customer_Model_Customer
     */
    public $customer;

    /**
     * Currently logged in customer ID
     *
     * @var int
     */
    public $customer_id;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->customer = Mage::getSingleton('customer/session')->getCustomer();
        $this->customer_id = $this->customer->getId();
    }

    /**
     * Checks if current user is logged in or not
     *
     * @return bool
     */
    public function customerIsLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Returns the customer ID of the currently logged in customer, FALSE otherwise
     *
     * @return mixed
     */
    public function getCustomerId()
    {
        $logged_in = Mage::getSingleton('customer/session')->isLoggedIn();

        if ($logged_in) {
            return $this->customer->getId();
        }
        else {
            return FALSE;
        }
    }

    /**
     * Determines whether the block should display or not. Customer must be logged in and not yet enrolled
     *
     * @return bool
     */
    public function shouldDisplay()
    {
        if ($this->customerIsLoggedIn()) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}