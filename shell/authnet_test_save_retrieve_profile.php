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

	// Create helper
	$helper = Mage::helper('authnettoken/cim');

	// Lookup existing Garth customer in DB
	$customer = Mage::getModel('customer/customer')->load(6);
	
	// Create a CIM customer profile
	if(strlen($customer->getData('cim_customer_profile_id')) <= 0) {
		$createdIds = Mage::helper('authnettoken/cim')->createCustomerProfileFromCustomer($customer);
		$customerProfileId = $createdIds['customerProfileId'];
		// Output
		echo "Created CIM customer profile Id: {$customerProfileId} for Customer Id: {$customer->getId()} \n";
	}
	
	// Create model and populate with some data
	$profile = Mage::getModel('authnettoken/cim_payment_profile');
	// Set fields for Magento DB
	$profile->setData('customer_id', $customer->getId());
	$profile->setData('customer_name', 'Garth Brantley');
	$profile->setData('customer_cardnumber', '4111111111111111');
	// Set fields for Auth.net CIM
	$profile->setData('firstname', 'Garth');
	$profile->setData('lastname', 'Brantley');
	$profile->setData('company', 'SFC');
	$profile->setData('address', '123 Somewhere ST');
	$profile->setData('city', 'Baltimore');
	$profile->setData('region', 'MD');
	$profile->setData('postcode', '21212');
	$profile->setData('country_id', 'US');
	$profile->setData('telephone', '4101234567');
	$profile->setData('fax', '4101234567');
	$profile->setData('exp_date', '2014-03');

	// Save profile to Auth.net CIM
	$profile->saveCimProfileData();

	// Now save profile to Magento DB
	$profile->save();
	// And lets keep track of the Magento pk id
	$profileId = $profile->getId();
	// Output
	echo "Created CIM payment profile with Magento DB id: {$profileId} and Auth.Net payment profile Id: {$profile->getData('cim_payment_profile_id')} \n";
	
	
	// Ok, now lets fetch all the data we just saved
	// Load row from magento db
	$profile = Mage::getModel('authnettoken/cim_payment_profile')->load($profileId);
	// Load extra data fields from Auth.Net CIM
	$profile->retrieveCimProfileData();
	// Output
	echo "Retrieved CIM payment profile with Magento DB id: {$profile->getId()} and Auth.Net payment profile Id: {$profile->getData('cim_payment_profile_id')} \n";
	// print out the data
	//print_r($profile->getData());
	
	
	// Finally, lets delete that profile
	// Delete Auth.net CIM profile
	$profile->deleteCimProfile();
	// Now delete Mage db object
	$profile->delete();
	// Output
	echo "Deleted payment profile. \n";
	

	// Log mem usage
	echo 'Memory usage: ' . memory_get_usage() . "\n";

} catch (Exception $e) {
	Mage::printException($e);
}

