<?php
/**
 * Class Alliance_Subscriptions_Block_Customer_Subscriptions
 */
class Alliance_Subscriptions_Block_Customer_Subscriptions extends Mage_Core_Block_Template
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







}