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


class SFC_AuthnetToken_Model_Cim_Payment_Profile extends Mage_Core_Model_Abstract
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('authnettoken/cim_payment_profile');
    }

    /**
     * Init profile with customer data from customer record
     */
    public function initCimProfileWithCustomerDefault($customerId)
    {
        // Load customer
        /** @var Mage_Customer_Model_Customer $model */
        $model = Mage::getModel('customer/customer')->load($customerId);
        // Set basic customer data fields
        $this->setData('customer_id', $customerId);
        $this->setData('customer_fname', $model->getData('firstname'));
        $this->setData('customer_lname', $model->getData('lastname'));
        // Grab default billing addy id
        $addressId = $model->getData('default_billing');
        // Add address data if default billing addy exists
        if ($addressId) {
            // Get address
            $address = Mage::getModel('customer/address')->load($addressId);
            // Set address
            $this->setBillingAddressFields($address);
        }
    }

    /**
     * Set billing address fields on payment profile from a Magento customer address
     * @param Mage_Customer_Model_Address_Abstract $billingAddress
     */
    public function setBillingAddressFields(Mage_Customer_Model_Address_Abstract $billingAddress)
    {
        $this->setData('firstname', $billingAddress->getData('firstname'));
        $this->setData('customer_fname', $billingAddress->getData('firstname'));
        $this->setData('lastname', $billingAddress->getData('lastname'));
        $this->setData('customer_lname', $billingAddress->getData('lastname'));
        $this->setData('company', $billingAddress->getData('company'));
        $this->setData('street', $billingAddress->getStreet(1) . ' ' . $billingAddress->getStreet(2));
        $this->setData('city', $billingAddress->getData('city'));
        $this->setData('region', $billingAddress->getData('region'));
        $this->setData('postcode', $billingAddress->getData('postcode'));
        $this->setData('country_id', $billingAddress->getData('country_id'));
        $this->setData('telephone', $billingAddress->getData('telephone'));
        $this->setData('fax', $billingAddress->getData('fax'));
    }

    /**
     * Retrieve extra data fields for payment profile from CIM and set them on model object
     */
    public function retrieveCimProfileData()
    {
        // Lookup customer in DB
        /** @var Mage_Customer_Model_Customer $model */
        $customer = Mage::getModel('customer/customer')->load($this->getData('customer_id'));
        // Get customer CIM profile id
        $customerProfileId = $customer->getData('cim_customer_profile_id');
        // Get payment profile id
        $paymentProfileId = $this->getData('cim_payment_profile_id');
        /** @var SFC_AuthnetToken_Helper_Cim $cimHelper */
        $cimHelper = Mage::helper('authnettoken/cim');
        $cimHelper->setConfigWebsite($customer->getData('website_id'));
        // Call out to Auth.net API to retrieve profile data
        $data = $cimHelper->retrievePaymentProfileAsData($customerProfileId, $paymentProfileId);
        // Now inject the CIM data into this model object
        $this->addData($data);
    }

    /**
     * Save payment profile to Authorize.Net CIM via API
     *
     * @param boolean $createCustProfile If this is true, then create the Authorize.Net CIM customer profile when it
     *                                   doesn't exist and save the CIM ID in the Magento customer record.
     */
    public function saveCimProfileData($createCustProfile = false)
    {
        // Lookup customer in DB
        /** @var Mage_Customer_Model_Customer $model */
        $customer = Mage::getModel('customer/customer')->load($this->getData('customer_id'));
        // Get customer CIM profile id
        $customerProfileId = $customer->getData('cim_customer_profile_id');
        // Get payment profile id
        $paymentProfileId = $this->getData('cim_payment_profile_id');
        /** @var SFC_AuthnetToken_Helper_Cim $cimHelper */
        $cimHelper = Mage::helper('authnettoken/cim');
        $cimHelper->setConfigWebsite($customer->getData('website_id'));
        // If there is a cim_payment_profile_id, then assume there is an existing record in CIM
        if (strlen($paymentProfileId) > 0) {
            // Save existing payment profile
            $cimHelper->updatePaymentProfileFromData($customerProfileId, $paymentProfileId, $this->getData());
        }
        else {
            // Check if customer exists
            if (strlen($customerProfileId) <= 0) {
                if (!$createCustProfile) {
                    Mage::throwException('Customer profile does not exist in Authorize.Net CIM!');
                }
                // Call API to create customer profile in CIM
                $customerProfileInfo = $cimHelper->createCustomerProfileFromCustomer($customer);
                $customerProfileId = $customerProfileInfo['customerProfileId'];
            }
            // Call API
            $paymentProfileId = $cimHelper->createPaymentProfileFromData($customerProfileId, $this->getData());
        }

        // Now save payment profile id to model if it doesn't already exist
        if ($paymentProfileId) {
            $this->setData('cim_payment_profile_id', $paymentProfileId);
        }

        // Remask the card number
        $this->setData('customer_cardnumber',
            SFC_AuthnetToken_Helper_Cim::CC_MASK . substr($this->getData('customer_cardnumber'), -4, 4));
    }

    /**
     * Delete the payment profile from Authorize.Net CIM
     */
    public function deleteCimProfile()
    {
        // Lookup customer in DB
        /** @var Mage_Customer_Model_Customer $model */
        $customer = Mage::getModel('customer/customer')->load($this->getData('customer_id'));
        // Get customer CIM profile id
        $customerProfileId = $customer->getData('cim_customer_profile_id');
        // Get payment profile id
        $paymentProfileId = $this->getData('cim_payment_profile_id');
        /** @var SFC_AuthnetToken_Helper_Cim $cimHelper */
        $cimHelper = Mage::helper('authnettoken/cim');
        $cimHelper->setConfigWebsite($customer->getData('website_id'));
        // If there is a cim_payment_profile_id, then assume there is an existing record in CIM
        if (strlen($paymentProfileId) > 0) {
            // Delete existing payment profile
            $cimHelper->deletePaymentProfile($customerProfileId, $paymentProfileId);
        }
        // Now reset profile id in this model object
        $this->setData('cim_payment_profile_id', null);
    }

}
