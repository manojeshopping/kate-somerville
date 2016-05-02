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

/**
 * One page checkout processing model
 */
class SFC_AuthnetToken_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{

    /**
     * Specify quote payment method
     *
     * @param   array $data
     * @return  array
     */
    public function savePayment($data)
    {
        // Get $data['method'] and determine if it includes last 4 cc digits
        // Check if the saved credit card payment profile data is embedded in this method code string
        if (isset($data['method']) && strpos($data['method'], SFC_AuthnetToken_Model_Cim::METHOD_CODE_KEY_CC_LAST4) !== false) {
            $encodedMethod = $data['method'];
            $encodedMethodParts = explode(SFC_AuthnetToken_Model_Cim::METHOD_CODE_KEY_CC_LAST4, $encodedMethod);
            $method = $encodedMethodParts[0];
            $ccLastDigits = $encodedMethodParts[1];

            // If necessary, save the last 4 of the saved CC in quote
            // This acts as a flag that we are using a saved CC and also indicates which saved CC
            if (strlen($ccLastDigits) == 4) {
                $data['saved_cc_last_4'] = $ccLastDigits;
            }

            // Modify data to strip out saved CC info from method code
            // This means payment process will be handled by SFC_AuthnetToken_Model_Cim method class and config settings
            $data['method'] = $method;

            // Now get cc_cid and payment_profile_id from form for this method
            // cc_cid wont always be available
            if (array_key_exists($encodedMethod . '_cc_cid', $data)) {
                $data['cc_cid'] = $data[$encodedMethod . '_cc_cid'];
            }
            $data['payment_profile_id'] = $data[$encodedMethod . '_payment_profile_id'];
        }

        // Call parent savePayment and return value
        return parent::savePayment($data);
    }

    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @throws Exception
     * @return Mage_Checkout_Model_Type_Onepage
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function saveOrder()
    {
        // Log
        Mage::log('Magento is saving order...', Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);

        // Get quote
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getQuote();

        // Check if payment method is the CIM extension
        if(0 !== strpos($quote->getPayment()->getMethod(), SFC_AuthnetToken_Model_Cim::METHOD_CODE)) {
            // This is some other checkout method, just call parent saveOrder method
            parent::saveOrder();
        }
        else {
            // This the CIM ext checkout payment method
            // Log
            Mage::log('This is a CIM order, handle any exceptions by cleaning up any created CIM profiles.', Zend_Log::INFO,
                SFC_AuthnetToken_Helper_Data::LOG_FILE);
            // Get profiles created data from customer session
            $alreadyCreatedCimCustomerProfileId = Mage::getSingleton('customer/session')->getCreatedCimCustomerProfileId();
            $alreadyCreatedCimPaymentProfileId = Mage::getSingleton('customer/session')->getCreatedCimPaymentProfileId();

            // Wrap parent saveOrder method in a try / catch to handle any errors
            try {
                parent::saveOrder();
            }
            catch (Exception $eOrder) {
                // Log
                Mage::log('Caught order exception in order with CIM payment method.', Zend_Log::INFO,
                    SFC_AuthnetToken_Helper_Data::LOG_FILE);
                Mage::log('Caught order exception: ' . $eOrder->getMessage(), Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                // Catch any exception thrown during order saving process
                // Check if a new customer profile was created
                $createdCimCustomerProfileId = Mage::getSingleton('customer/session')->getCreatedCimCustomerProfileId();
                $createdCimPaymentProfileId = Mage::getSingleton('customer/session')->getCreatedCimPaymentProfileId();
                $existingCimCustomerProfileId = Mage::getSingleton('customer/session')->getExistingCimCustomerProfileId();
                // Log
                Mage::log('alreadyCreatedCimCustomerProfileId: ' .
                $alreadyCreatedCimCustomerProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                Mage::log('alreadyCreatedCimPaymentProfileId: ' .
                $alreadyCreatedCimPaymentProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                Mage::log('createdCimCustomerProfileId: ' .
                $createdCimCustomerProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                Mage::log('createdCimPaymentProfileId: ' .
                $createdCimPaymentProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                Mage::log('existingCimCustomerProfileId: ' .
                $existingCimCustomerProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                // Check ids saved in session and decide if we need to delete anything
                if (strlen($createdCimCustomerProfileId) && $createdCimCustomerProfileId != $alreadyCreatedCimCustomerProfileId) {
                    // A new customer profile was created during this saveOrder, but exception was thrown and order aborted
                    // Log
                    Mage::log('Order failed, but CIM customer profile was created.  Deleting profile: ' .
                    $createdCimCustomerProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                    // Lets delete the customer profile that was created
                    try {
                        $customer = Mage::getSingleton('customer/session')->getCustomer();
                        /** @var SFC_AuthnetToken_Helper_Cim $cimHelper */
                        $cimHelper = Mage::helper('authnettoken/cim');
                        $cimHelper->setConfigWebsite($customer->getWebsiteId());
                        $cimHelper->deleteCustomerProfile($createdCimCustomerProfileId);
                    }
                    catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
                        Mage::log('Failed to delete CIM customer profile: ' .
                        $createdCimCustomerProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                        Mage::log('Error: ' . $eCim->getMessage(), Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                    }
                }
                else {
                    if (strlen($createdCimPaymentProfileId) && $createdCimPaymentProfileId != $alreadyCreatedCimPaymentProfileId &&
                        !strlen($createdCimCustomerProfileId)
                    ) {
                        // A new payment profile was created during this saveOrder, but exception was thrown and order aborted
                        // Log
                        Mage::log('Order failed, but CIM payment profile (for existing customer profile) was created.  Deleting profile: ' .
                        $createdCimPaymentProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                        // Lets delete the customer profile that was created
                        try {
                            $customer = Mage::getSingleton('customer/session')->getCustomer();
                            /** @var SFC_AuthnetToken_Helper_Cim $cimHelper */
                            $cimHelper = Mage::helper('authnettoken/cim');
                            $cimHelper->setConfigWebsite($customer->getWebsiteId());
                            $cimHelper->deletePaymentProfile($existingCimCustomerProfileId, $createdCimPaymentProfileId);
                        }
                        catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
                            Mage::log('Failed to delete CIM payment profile: ' .
                            $createdCimPaymentProfileId, Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                            Mage::log('Error: ' . $eCim->getMessage(), Zend_Log::INFO, SFC_AuthnetToken_Helper_Data::LOG_FILE);
                        }
                    }
                }
                // Now rethrow exception which caused order to fail
                throw $eOrder;
            }
        }

        return $this;
    }

}
