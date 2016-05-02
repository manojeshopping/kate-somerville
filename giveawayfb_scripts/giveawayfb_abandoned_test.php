<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.armando.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
define('MAGENTO_ROOT_LIVE', "/home/katesome/katesomerville.com/html");
$currentRoot = MAGENTO_ROOT_DEV3;

// $maxOrderProcessed = 10;

set_time_limit(0); // Set max limit


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');

// Load Giveaway Model and helper.
$model = Mage::getModel('giveawayfb/giveawayfb');
$helper = Mage::helper('giveawayfb');

// Get Skus
$sampleSkus = $helper->getSampleKitSkus();

// Get abandoned carts.
$collection = Mage::getResourceModel('reports/quote_collection');
$collection
	->prepareForAbandonedReport(null, array('subtotal' => array('from' => 0, 'to' => 0)))
	->addFieldToFilter('items_count', 1)
	->addFieldToFilter('items_qty', 1)
;
$collection->getSelect()->limit($maxOrderProcessed);
echo "count: ".$collection->count()."\n";
$freesamples = 0;
$createdOrders = 0;
$repeatedCarts = 0;
foreach($collection as $_quote) {
	echo "Quote ID: ".$_quote->getId();
	
	$customerId = $_quote->getCustomerId();
	$customerEmail = $_quote->getCustomerEmail();
	echo " - customerId: ".$customerId." - customerEmail: ".$customerEmail;

	
	// Check if is a FreeSample.
	$items = $_quote->getAllVisibleItems();
	foreach($items as $cartItem) {
		$isSku = in_array($cartItem->getProduct()->getSku(), $sampleSkus);
		$productId = $cartItem->getProductId();
	}
	
	if( $isSku) {
		echo " - It's A FREE SAMPLES (".$customerEmail.").\n";
		continue;
	}
	
	if(! $isSku) {
	//	echo " - NOT A FREE SAMPLES (".$customerEmail.").\n";
		echo " - \n ";
		continue;
	}
	$freesamples++;
	
	
	// Check Magento orders.
	$customerOrders = Mage::getResourceModel('sales/order_grid_collection')->addFieldToSelect('entity_id')->addFieldToFilter('customer_id', $customerId)->addFieldToFilter('base_grand_total', 0);
	$isSku = false;

	echo "\n";
}

echo "END\n";
?>