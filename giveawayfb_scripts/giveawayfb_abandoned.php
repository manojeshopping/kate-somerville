<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
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
	if(! $isSku) {
		echo " - NOT A FREE SAMPLES (".$customerEmail.").\n";
		continue;
	}
	$freesamples++;
	
	// Check Magento orders.
	$customerOrders = Mage::getResourceModel('sales/order_grid_collection')
		->addFieldToSelect('entity_id')
		->addFieldToFilter('customer_id', $customerId)
		->addFieldToFilter('base_grand_total', 0)
	;
	$isSku = false;
	if($customerOrders->count() > 0) {
		// Check order items.
		foreach($customerOrders as $_order) {
			$items = $_order->getAllItems();
			foreach ($items as $_item) {
				$isSku = in_array($_item->getProduct()->getSku(), $sampleSkus);
				if($isSku) break;
			}
			
			if($isSku) {
				$order_id = $_order->getId();
				echo " - OLD order id: #".$order_id."\n";
				break;
			}
		}
	}
	if($isSku) {
		$repeatedCarts++;
		
		// Empty cart.
		$_quote->setIsActive(false);
		$_quote->delete();
		
		continue;
	}
	
	// Create order.
	$createdOrders++;
	$order = $model->createNewOrder($customerId, $productId);
	if(! $order) {
		echo " - ERROR CREATING ORDER.\n";
		continue;
	}
	
	$magentoOrderId = $order->getId();
	$magentoIncrementId = $order->getIncrementId();
	echo " - New Order: ".$order->getId();
	
	// Add $10 credit to customer.
	$creditAdded = $model->addInitialCredit($customerId);
	
	// Generate customer passwords and send email.
	$newPassword = $helper->generateNewPassword();
	$customer = Mage::getModel("customer/customer")->load($customerId);
	$customer->setPassword($newPassword);
	$customer->save();
	echo " - Customer saved: ".$customerId;
	$emailSended = $model->sendConfirmationEmail($magentoOrderId, $newPassword);
	
	// Save giveaway table.
	echo " - magentoIncrementId: ".$magentoIncrementId;
	$updated = $model->updateData($customerEmail, array(
		'customer_password' => '',
		'customer_id' => $customerId,
		'order_creation' => date('Y-m-d H:i:s'),
		'order_id' => $magentoOrderId,
		'increment_id' => $magentoIncrementId,
	), 'email');
	if(! $updated) {
		echo " - ERROR SAVING DATA.\n";
		continue;
	}
	
	// Empty cart.
	$_quote->setIsActive(false);
	$_quote->delete();
	
	
	echo "\n";
}

echo "freesamples: ".$freesamples."\n";
echo "createdOrders: ".$createdOrders."\n";
echo "repeatedCarts: ".$repeatedCarts."\n";
echo "END\n";
?>