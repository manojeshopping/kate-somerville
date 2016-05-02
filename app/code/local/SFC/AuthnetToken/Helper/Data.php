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

class SFC_AuthnetToken_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Constants
     */
    // Log file name
    const LOG_FILE = 'authnettoken.log';

    /**
     * Example of how logging should be done in this extension:
     *     Mage::log($message, Zend_Log::ERR, SFC_AuthnetToken_Helper_Data::LOG_FILE);
     */

    /**
     * Output the current call stack to module log file
     */
    public function logCallStack()
    {
        $exception = new Exception;
        Mage::log("Current call stack:\n" . $exception->getTraceAsString(), Zend_Log::INFO, self::LOG_FILE);
    }

}
