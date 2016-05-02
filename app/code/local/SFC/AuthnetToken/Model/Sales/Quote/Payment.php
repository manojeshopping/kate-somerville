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
 * Quote payment information
 *
 */
class SFC_AuthnetToken_Model_Sales_Quote_Payment extends Mage_Sales_Model_Quote_Payment
{

    /**
     * Import data array to payment method object,
     * Method calls quote totals collect because payment method availability
     * can be related to quote totals
     *
     * @param   array $data
     * @throws  Mage_Core_Exception
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function importData(array $data)
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

        return parent::importData($data);
    }

    /**
     * Import data array to payment method object,
     * Method calls quote totals collect because payment method availability
     * can be related to quote totals
     *
     * @param   array $data
     * @throws  Mage_Core_Exception
     * @return  Mage_Sales_Model_Quote_Payment
     */
    public function addData(array $data)
    {
        // Get $data['method'] and determine if it includes last 4 cc digits
        // Check if the saved credit card payment profile data is embedded in this method code string
        if (strpos($data['method'], SFC_AuthnetToken_Model_Cim::METHOD_CODE_KEY_CC_LAST4) !== false) {
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

        return parent::addData($data);
    }
}
