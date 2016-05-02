<?php
define('MAGENTO_ROOT', "../..");


// Configuration.
set_time_limit(0); // Set max limit.
$defaultView = "default";
$tabletView = "t_kate";


// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');

// Load stores.
$stores = Mage::app()->getStores();
$storesArray = array();
foreach($stores as $_store) {
	$storesArray[$_store->getCode()] = $_store->getStoreId();
}


$urlCollection = Mage::getModel('enterprise_urlrewrite/redirect')
	->getCollection()
	->addFieldToFilter('store_id', $storesArray[$defaultView])
;
echo "============\n";
echo "Default Redirections: ".$urlCollection->count()."\n";
echo "============\n";
$count = 0;
foreach($urlCollection as $_redirection) {
	$identifier = $_redirection->getIdentifier();
	
	// Check in tablet view.
	$checkUrlCollection = Mage::getModel('enterprise_urlrewrite/redirect')
		->getCollection()
		->addFieldToFilter('store_id', $storesArray[$tabletView])
		->addFieldToFilter('identifier', $identifier)
	;
	if($checkUrlCollection->count() == 0) {
		echo "Identifier: ".$identifier." - ";
		
		$newRedirectData = $_redirection->getData();
		$newRedirectData['store_id'] = $storesArray[$tabletView];
		unset($newRedirectData['redirect_id']);
		
		$newRedirect = Mage::getModel('enterprise_urlrewrite/redirect');
		$newRedirect->addData($newRedirectData);
		$newRedirect->save();
		echo "newRedirect: ".$newRedirect->getId()." - ";
		
		$count++;
		echo "OK\n";
	}
}
echo "============\n";
echo "Inserted ".$count." urls\n";
echo "============\n";

?>