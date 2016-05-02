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

	echo "This will delete ALL customer profiles CIM!  \n";
	$input = readline("Please enter 'YES' to confirm: ");
	if(trim($input) !== 'YES') {
		return;
	}

	// Create helper
	$helper = Mage::helper('authnettoken/cim');

	// Get list of all profiles
	$customerProfileIds = $helper->retrieveAllCustomerProfileIds();
	// Echo
	echo "Found " . count($customerProfileIds) . " customer profiles.\n";
	//print_r($customerProfileIds);
	echo "Deleting profiles...\n";
	// Iterate list
	foreach($customerProfileIds as $curCustomerProfileId) {
		// Delete each profile
		$helper->deleteCustomerProfile($curCustomerProfileId);
		// Echo
		echo ".";
	}
	// Echo
	echo "\nProfiles deleted.\n";

	// Log mem usage
	echo 'Memory usage: ' . memory_get_usage() . "\n";

} catch (Exception $e) {
	Mage::printException($e);
}

