<?php

/**
 * Class Alliance_FiveHundredFriends_Block_Enroll_Notloggedin_Login
 */
class Alliance_FiveHundredFriends_Block_Enroll_Notloggedin_Login extends Mage_Customer_Block_Form_Login
{
    /**
     * Sets a redirect for customers registering through the UKR enrollment page
     */
    public function __construct()
    {
        $session = Mage::getSingleton('customer/session');
        $session->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());
    }
}