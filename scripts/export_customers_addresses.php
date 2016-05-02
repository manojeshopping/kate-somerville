<?php
// Configuration.
define('MAGENTO_ROOT', substr(__DIR__, 0, strrpos(__DIR__, '/')));
$_SERVER['DOCUMENT_ROOT'] = MAGENTO_ROOT;
$csvFile = __DIR__ . "/customer_addresses.csv";
set_time_limit(0); // Set max limit.


// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');

// Open file.
$fp = fopen($csvFile, 'w');
fputcsv($fp, array('Customer ID', 'Email', 'Name', 'Last Name', 'Created At', 'Address Id', 'Address Name', 'Address Last Name', 'Street Address', 'City', 'Country', 'State/Province', 'Zip/Postal Code'));

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToSelect('firstname')
	->addAttributeToSelect('lastname')
;


$count = 0;
foreach($customers as $_customer) {
	// Get customer addresses.
	
	$addresses = Mage::getResourceModel('customer/address_collection')
		->setCustomerFilter($_customer)
		->addAttributeToSelect('*')
		->addAttributeToFilter('firstname', array('nlike'=>'%' . $_customer->getFirstname() . '%'))
		->addAttributeToFilter('lastname', array('nlike'=>'%' . $_customer->getLastname() . '%'))
	;
	
	if($addresses->count() < 2) continue;
	
	$createdAt = date('M/d/Y', strtotime($_customer->getCreatedAt()));
	
	$addressesSaved = array();
	$rowsToSave = array();
	foreach($addresses as $_address) {
		$addressData = array(
			$_address->getFirstname(),
			$_address->getLastname(),
			$_address->getStreetFull(),
			$_address->getCity(),
			$_address->getCountryId(),
			$_address->getRegion(),
			$_address->getPostcode(),
		);
		
		if(! in_array($addressData, $addressesSaved)) {
			$addressesSaved[] = $addressData;
			$rowsToSave[] = array_merge(array(
				$_customer->getId(),
				$_customer->getEmail(),
				$_customer->getFirstname(),
				$_customer->getLastname(),
				$createdAt,
				$_address->getId(),
			), $addressData);
			
		}
	}
	
	if(count($rowsToSave) > 1) {
		foreach($rowsToSave as $_row) {
			fputcsv($fp, $_row);
		}
	}
	
	$count++;
	
}
fclose($fp);
die($count." customers exported.\n");
?>