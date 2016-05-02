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

class SFC_AuthnetToken_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{

    public function getSavedProfiles()
    {
        // Grab customer object
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if(strlen($customer->getId())) {
            // Lookup profiles for this customer
            $profileCollection = Mage::getModel('authnettoken/cim_payment_profile')->getCollection();
            $profileCollection
                ->addFieldToFilter('customer_id', $customer->getId());
            // Return collection of profiles
            return $profileCollection;
        }
        else {
            return array();
        }
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    public function getSelectedMethodCode()
    {
        if (!strlen($this->getSavedCcLast4())) {
            if ($method = $this->getQuote()->getPayment()->getMethod()) {
                return $method;
            }
        }
        return false;
    }

    public function getSavedCcLast4()
    {
        return $this->getQuote()->getPayment()->getAdditionalInformation('saved_cc_last_4');
    }

    /**
     * Retrive has verification configuration
     *
     * @return boolean
     */
    public function hasVerification()
    {
        $storeId = $this->getStore();
        $path = 'payment/' . SFC_AuthnetToken_Model_Cim::METHOD_CODE . '/useccv';
        $configData = Mage::getStoreConfig($path, $storeId);
        if (is_null($configData)) {
            return true;
        }
        return (bool)$configData;
    }

}
