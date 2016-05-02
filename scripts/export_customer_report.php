<?php
// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katesomerville.com");
$_SERVER['DOCUMENT_ROOT'] = MAGENTO_ROOT_DEV3;
$currentRoot = MAGENTO_ROOT_DEV3;

$purchasedDayLimit = 120;


// $maxRowsProcessed = 1;
set_time_limit(0); // Set max limit.


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');

// Load Magento DB Resource
$resource = Mage::getSingleton('core/resource');
$readConn = $resource->getConnection('core_read');
$writeConn = $resource->getConnection('core_write');

// Open file.
$fp = openFile(__DIR__ . "/customer_report.csv");
fputcsv($fp, array('Email', 'First name', 'Last name', 'Lifetime Value', 'Latency', 'Last Purchase Date'));


// Get day to filter.
$created_at = (time() - ($purchasedDayLimit * 86400));

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToSelect('firstname')
	->addAttributeToSelect('lastname')
;
$count = 0;
foreach($customers as $_customer) {
	// Get orders.
	$orders = Mage::getModel('sales/order')
		->getCollection()
		->addFieldToFilter('customer_id', $_customer->getId())
		->addFieldToFilter('status', 'complete')
	;
	$orders->getSelect()->order('created_at DESC');
	if($orders->count() == 0) continue;
	
	$lastPurchaseTime = strtotime($orders->getFirstItem()->getCreatedAt());
	
	// Check last order created at.
	if($lastPurchaseTime > $created_at) continue;
	
	// Get latency and last purchase.
	$latency = floor((time() - $lastPurchaseTime) / 86400);
	$lastPurchase = date('m/d/Y', $lastPurchaseTime);
	
	// Get Lifetime Value.
	$lifetimeValue = 0;
	foreach($orders as $_order) {
		$lifetimeValue += $_order->getGrandTotal();
	}
	
	// Save into file.
	fputcsv($fp, array(
		$_customer->getEmail(),
		$_customer->getFirstname(),
		$_customer->getLastname(),
		$lifetimeValue,
		$latency,
		$lastPurchase,
	));
	
	$count++;
}
fclose($fp);
die($count." customers exported.\n");

function openFile($file)
{
	$fp = fopen($file, 'w');
	if ($fp === false) {
		printLog("The file ".$file." does not exist.", true);
		printLog("===============", true);
		return false;
	}
	
	return $fp;
}





