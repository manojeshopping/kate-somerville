<?php
/**
 * StoreFront Authorize.Net CIM Tokenized Payment Extension
 *
 * This source file is subject to commercial source code license of StoreFront Consulting, Inc.
 *
 * @category	SFC
 * @package    	SFC_AuthnetToken
 * @author      Garth Brantley
 * @website 	http://www.storefrontconsulting.com/
 * @copyright 	Copyright (C) 2009-2013 StoreFront Consulting, Inc. All Rights Reserved.
 * @license     http://www.storefrontconsulting.com/media/downloads/ExtensionLicense.pdf StoreFront Consulting Commercial License
 *
 */

require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

umask(0);

try {
    // Log mem usage
    echo "\n" . 'Memory usage: ' . memory_get_usage() . "\n";

    echo "This script will match CIM customer profiles with Magento customers and output a report...  \n";

    // Create helper
    $helper = Mage::helper('authnettoken/cim');

    // Create / open output file
    $headerColumns = array(
        'email',
        'cim_customer_profile_id',
        'cim_merchant_customer_id',
        'cim_description',
        'magento_customer_id',
        'magento_customer_name',
    );
    /** @var SFC_AuthnetToken_Helper_Tsvfile $file */
    $file = Mage::helper('authnettoken/tsvfile');
    $bSuccess = $file->open('cim_customer_duplicate_report.tsv', $headerColumns);
    // Check success opening file
    if (!$bSuccess) {
        Mage::throwException('Failed to open output file!');
    }

    // Get list of all profiles
    $customerProfileIds = $helper->retrieveAllCustomerProfileIds();
    // Echo
    echo "Found " . count($customerProfileIds) . " customer profiles in Authorize.net CIM.\n";
    // Iterate list
    foreach($customerProfileIds as $curCustomerProfileId) {
        // Echo
        echo "Looking up customer profile in CIM with id: " . $curCustomerProfileId . "\n";
        // Lookup customer profile details
        /** @var  $cimCustomerProfile */
        $cimCustomerProfile = $helper->retrieveCustomerProfile($curCustomerProfileId);
        // Lets check to see if a Magento customer with matching email exists
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToFilter('email', $cimCustomerProfile->email)
            ->getFirstItem();
        $customer->load($customer->getId());
        if(strlen($customer->getId())) {
            // Found a matching Magento customer
            // Echo
            echo "Found a matching Magento customer for email: " . $cimCustomerProfile->email . "\n";
            // Add row to report file
            $rowValues = array(
                'email' => $cimCustomerProfile->email,
                'cim_customer_profile_id' => $cimCustomerProfile->customerProfileId,
                'cim_merchant_customer_id' => $cimCustomerProfile->merchantCustomerId,
                'cim_description' => $cimCustomerProfile->description,
                'magento_customer_id' => $customer->getId(),
                'magento_customer_name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'),
            );
            $file->writeRow($rowValues);
        }
        else {
            // Echo
            echo "Did not find a matching Magento customer for email: " . $cimCustomerProfile->email . "\n";
        }
    }

    // Close file
    $file->close();

    // Log mem usage
    echo "\nMemory usage: " . memory_get_usage() . "\n";

} catch (Exception $e) {
    Mage::printException($e);
}

