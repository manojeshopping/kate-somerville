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


class SFC_AuthnetToken_Model_Cim_Transaction_Response
{
    /**
     * Construct
     */
    protected function _construct()
    {
    }

    /**
     * Build and return an array mapping response message code, AVS response and CVV response to friendly error messages for customers
     *
     * Authorize.Net CIM response message codes can be found here:
     *      http://www.authorize.net/support/merchant/Transaction_Response/Response_Reason_Codes_and_Response_Reason_Text.htm
     *
     * Authorize.Net CIM AVS and CVV responses can be found here:
     *      http://www.authorize.net/support/merchant/Transaction_Response/Transaction_Response.htm
     *
     * @return array
     */
    public function getResponseToMessageMap()
    {
        // Array which maps response codes to error messages
        // 1st field is response message code
        // 2nd field is AVS response code
        // 3rd field is CVV response code
        // When null is specified for AVS response and / or CVV response, the message will be used in cases when AVS and / or CVV response is not present
        return array(
            // AVS Mismatches
            array(
                'E00027',
                'G',
                null,
                'Your card issuer does not support a billing address / zip code check!'
            ),
            array(
                'E00027',
                'S',
                null,
                'Your card issuer does not support a billing address / zip code check!'
            ),
            array(
                'E00027',
                'Y',
                null,
                'The extended zip code for your billing address does not match!'
            ),
            array(
                'E00027',
                'E',
                'P',
                'Your card issuer does not support a billing address / zip code check!'
            ),
            array(
                'E00027',
                'R',
                'P',
                'We were unable to verify your billing address, please try again later!'
            ),
            array(
                'E00027',
                'G',
                'P',
                'Your card issuing bank does not support address verification service!'
            ),
            array(
                'E00027',
                'U',
                'P',
                'The address information for the cardholder is unavailable!'
            ),
            array(
                'E00027',
                'S',
                'P',
                'Your card issuing bank does not support address verification service!'
            ),
            array(
                'E00027',
                'N',
                'P',
                'Billing address does not match!'
            ),
            array(
                'E00027',
                'A',
                'P',
                'Billing address does not match!'
            ),
            array(
                'E00027',
                'Z',
                'P',
                'Billing address does not match!'
            ),
            array(
                'E00027',
                'W',
                'P',
                'Billing address does not match!'
            ),
            array(
                'E00027',
                'Y',
                'P',
                'Your Extended Zip Code does not match!'
            ),
            array(
                'E00027',
                null,
                null,
                'Billing address does not match!'
            ),
            // CVV Mismatches
            array(
                'E00027',
                'Y',
                'N',
                'CCV Code does not match!'
            ),
            array(
                'E00027',
                'Y',
                'S',
                'CCV Code was not processed!'
            ),
            array(
                'E00027',
                'Y',
                'U',
                'Issuer is not certified or has not provided encryption key!'
            ),
            array(
                'E00027',
                null,
                'N',
                'CVV code does not match!'
            ),
            array(
                'E00044',
                null,
                'N',
                'CVV code does not match!'
            ),
            array(
                'E00044',
                null,
                'U',
                'Your card issuer does not support a CVV match!'
            ),
            array(
                'E00044',
                null,
                null,
                'CVV code does not match!'
            ),
            // Other error messages
            array(
                'E00002',
                null,
                null,
                'The transaction has been declined!'
            ),
            array(
                'E00003',
                null,
                null,
                'The transaction has been declined!'
            ),
            array(
                'E00004',
                null,
                null,
                'The transaction has been declined!'
            ),
            array(
                'E00006',
                null,
                null,
                'The credit card number is invalid!'
            ),
            array(
                'E00007',
                null,
                null,
                'The credit card expiration date is invalid!'
            ),
            array(
                'E00037',
                null,
                null,
                'The credit card number is invalid!'
            ),
        );
    }

}
