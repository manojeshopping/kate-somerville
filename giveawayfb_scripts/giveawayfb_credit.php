<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
define('MAGENTO_ROOT_ARMANDO', "/var/vhosts/katedev113.armando.alliance-global.com");
define('MAGENTO_ROOT_LIVE', "/home/katesome/katesomerville.com/html");
$currentRoot = MAGENTO_ROOT_ARMANDO;


 $maxOrderProcessed = 10000;
set_time_limit(0); // Set max limit


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');


// Load Giveaway Model and helper.
$model = Mage::getModel('giveawayfb/giveawayfb');
$helper = Mage::helper('giveawayfb');


// Get without orders registers.
$collection = $model->getCollection()
	->addFieldToFilter('order_id', array('notnull' => true))
;
$collection->getSelect()->group('email');
$collection->getSelect()->order('giveawayfb_id ASC');
echo "count: ".$collection->count()."\n";
$newCredits = 0;
foreach($collection as $_customer) {
	// Break.
	if(! empty($maxOrderProcessed) && $newCredits >= $maxOrderProcessed) {
		echo "\n--------\nBreak: ".$maxOrderProcessed."\n--------\n";
		break;
	}
	
	echo "Giveaway ID: #".$_customer->getId();
	
	$customerId = $_customer->getCustomerId();
	echo " - customerId: #".$customerId;
	
	// Get Balance.
	$balance = Mage::getModel('enterprise_customerbalance/balance')
		->setCustomerId($customerId)
		->loadByCustomer()
	;
	
	// Check current amount.
	$amount = $balance->getAmount();
	echo " - amount: ".$amount;
	if($amount > 0) {
		echo "\n";
		continue;
	}
	
	// Check balance history.
	$history = Mage::getModel('enterprise_customerbalance/balance_history')
		->getCollection()
		->addFieldToFilter('customer_id', $customerId);
	;
	$historyCount = $history->count();
	if($historyCount > 0) {
		echo "history: ".$historyCount."\n";
		continue;
	}
	
	// Add $10 credit to customer.
	$creditAdded = $model->addInitialCredit($customerId);
	if($creditAdded) {
		echo " - OK";
		$newCredits++;
	} else {
		echo " - ERROR ADDING CREDIT";
	}
	
	echo "\n";
}


echo "newCredits: ".$newCredits."\n";
echo "END\n";
die();