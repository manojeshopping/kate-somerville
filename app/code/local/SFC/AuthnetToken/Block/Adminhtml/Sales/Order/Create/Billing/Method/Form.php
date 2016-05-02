<?php
/**
 * StoreFront Authorize.Net CIM Tokenized Payment Extension for Magento
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to commercial source code license of StoreFront Consulting, Inc.
 *
 * @category  SFC
 * @package   SFC_AuthnetToken
 * @author    Garth Brantley <garth@storefrontconsulting.com>
 * @copyright 2009-2013 StoreFront Consulting, Inc. All Rights Reserved.
 * @license   http://www.storefrontconsulting.com/media/downloads/ExtensionLicense.pdf StoreFront Consulting Commercial License
 * @link      http://www.storefrontconsulting.com/authorize-net-cim-saved-credit-cards-extension-for-magento/
 *
 */

class SFC_AuthnetToken_Block_Adminhtml_Sales_Order_Create_Billing_Method_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form
{

    public function getSavedProfiles()
    {
        // Grab customer object
        $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        // Lookup profiles for this customer
        $profileCollection = Mage::getModel('authnettoken/cim_payment_profile')->getCollection();
        $profileCollection
            ->addFieldToFilter('customer_id', $customer->getId());
        // Return collection of profiles
        return $profileCollection;
    }

    public function getSavedCcLast4()
    {
        return $this->getQuote()->getPayment()->getAdditionalInformation('saved_cc_last_4');
    }

}
