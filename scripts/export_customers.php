<?php
// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katesomerville.com");
$_SERVER['DOCUMENT_ROOT'] = MAGENTO_ROOT_DEV3;
$currentRoot = MAGENTO_ROOT_DEV3;


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
$fp = openFile(__DIR__ . "/customers.csv");
fputcsv($fp, array('First name', 'Last name', 'email'));

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToSelect('firstname')
	->addAttributeToSelect('lastname')
;
$count = 0;
foreach($customers as $_customer) {
	fputcsv($fp, array(
		$_customer->getFirstname(),
		$_customer->getLastname(),
		$_customer->getEmail(),
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





