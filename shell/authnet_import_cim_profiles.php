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

require '../app/Mage.php';

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

	echo "This will import CIM payment profiles from Authorize.Net for existing Magento customers!  \n";
	
	//$input = readline("Please enter 'YES' to confirm: ");
	//if(trim($input) !== 'YES') {
	//	return;
	//}

	// Create helper
	$helper = Mage::helper('authnettoken/cim');

	// Get list of all profiles
	$customerProfileIds = $helper->retrieveAllCustomerProfileIds();
	// Echo
	echo "Found " . count($customerProfileIds) . " customer profiles in Authorize.net CIM.\n";
	//print_r($customerProfileIds);
	echo "Linking profiles...\n";
	// Iterate list
	foreach($customerProfileIds as $curCustomerProfileId) {
		// Echo
		echo "Looking up customer profile in CIM with id: " . $curCustomerProfileId . "\n";			
		// Lookup customer profile details
		$cimCustomerProfile = $helper->retrieveCustomerProfile($curCustomerProfileId);
		// Lets check to see if a Magento customer with matching email exists
		$customer = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('email', $cimCustomerProfile->email)
			->getFirstItem();
		$customer->load($customer->getId());
		if(strlen($customer->getId())) {
			// Found a matching Magento customer
			// Echo
			echo "Found a matching Magento customer for email: " . $cimCustomerProfile->email . "\n";
			// Check if Magento customer already linked to a CIM profile
			if(strlen($customer->getData('cim_customer_profile_id'))) {
				// Echo
				echo "This Magento customer already linked to CIM customer profile id: " . $customer->getData('cim_customer_profile_id') . "\n";
			}
			else {
				// Echo
				echo "Linking Magento customer to CIM profile." . "\n";			
				// Connect Mage customer to customer profile in CIM
				$customer->setData('cim_customer_profile_id', $curCustomerProfileId);
				$customer->save();
				// Echo
				echo "Deleting existing payment profile rows from Magento for this customer." . "\n";			
				// To ensure data integrity and clean up a corrupted DB, do this
				// As a precautionary measure, delete any profiles which are alreayd associated with customer (there should not be any, because 'cim_customer_profile_id' was not set on customer)
				$existingProfileModels = Mage::getModel('authnettoken/cim_payment_profile')->getCollection()
					->addFieldToFilter('customer_id', $customer->getId());
				foreach($existingProfileModels as $curExistingProfileModel) {
					$curExistingProfileModel->delete();
				}
				// Now iterate payment profiles from CIM, and create them in Magento
				foreach($cimCustomerProfile->paymentProfiles as $curCimPaymentProfile) {
					// Save payment profile in Magento DB
					$profileModel = Mage::getModel('authnettoken/cim_payment_profile');
					$profileModel->setData('customer_id', $customer->getId());
        			$profileModel->setData('customer_fname', $curCimPaymentProfile->billTo->firstName);
        			$profileModel->setData('customer_lname', $curCimPaymentProfile->billTo->lastName);
					$profileModel->setData('customer_cardnumber', $curCimPaymentProfile->payment->creditCard->cardNumber);
					$profileModel->setData('cim_payment_profile_id', $curCimPaymentProfile->customerPaymentProfileId);
					$profileModel->save();
					// Echo
					echo "Linked payment profile with id: " . $curCimPaymentProfile->customerPaymentProfileId . "\n";
				}
			}
		}
		else {
			// Echo
			echo "Did not find a matching Magento customer for email: " . $cimCustomerProfile->email . "\n";
		}
	}

	// Log mem usage
	echo "\nMemory usage: " . memory_get_usage() . "\n";

} catch (Exception $e) {
	Mage::printException($e);
}

