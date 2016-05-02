<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
define('MAGENTO_ROOT_LIVE', "/home/katesome/katesomerville.com/html");
$currentRoot = MAGENTO_ROOT_LIVE;


// $maxCustomerProcessed = 5;
$csvFileName = __DIR__ . "/giveawayfb_duplicates_".time().".csv";
set_time_limit(0); // Set max limit


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');


// Load Giveaway Model and helper.
$model = Mage::getModel('giveawayfb/giveawayfb');
$helper = Mage::helper('giveawayfb');

// Get Freesamples Skus
$sampleSkus = $helper->getSampleKitSkus();

// Save data in CSV file.
$fp = fopen($csvFileName, 'w');
$csvHeader = array(
	'order_id' => "Order Id",
	'increment_id' => "Order Number",
	'customer_id' => "Customer Id",
	'date' => "Date",
	'status' => "Status",
	'sku' => "Sku",
	'name' => "Full Name",
	'email' => "Email",
);
fputcsv($fp, $csvHeader);

// Get Customer Collection.
$customerCollection = Mage::getResourceModel('customer/customer_collection')
	->addFieldToFilter('group_id', $helper->getCustomerGroupId())
;
$customerCollection
	->getSelect()
	->reset(Zend_Db_Select::COLUMNS)
	->columns('entity_id')
;
$processedCount = 0;
$errorCount = 0;
foreach($customerCollection as $_customer) {
	// Break.
	if(! empty($maxCustomerProcessed) && $processedCount >= $maxCustomerProcessed) {
		echo "\n--------\nBreak: ".$maxCustomerProcessed."\n--------\n";
		break;
	}
	
	$customerId = $_customer->getId();
	
	$customerOrders = Mage::getResourceModel('sales/order_grid_collection')
		->addFieldToSelect('entity_id')
		->addFieldToSelect('increment_id')
		->addFieldToSelect('created_at')
		->addFieldToFilter('customer_id', $customerId)
		->addFieldToFilter('base_grand_total', 0)
	;
	if($customerOrders->count() < 2) {
		continue;
	}
	
	
	// Check order items.
	$skuCount = 0;
	foreach($customerOrders as $_order) {
		unset($csvRow);
		
		$order = Mage::getModel('sales/order')->load($_order->getIncrementId(), 'increment_id');
		
		// Check items.
		$items = $_order->getAllItems();
		foreach ($items as $_item) {
			$sku = $_item->getProduct()->getSku();
			$isSku = in_array($sku, $sampleSkus);
			
			if($isSku) break;
		}
		
		if($isSku) {
			$skuCount++;
			
			if($skuCount == 1) {
				$csvRowAux = array(
					'order_id' => $_order->getId(),
					'increment_id' => $_order->getIncrementId(),
					'customer_id' => $customerId,
					'date' => $_order->getCreatedAt(),
					'status' => $order->getStatusLabel(),
					'sku' => $sku,
				);
			}
			
			// If it has more than one order.
			if($skuCount > 1) {
				echo "customerId: ".$customerId." - skuCount: ".$skuCount;
				$processedCount++;
				
				$customer = Mage::getModel("customer/customer")->load($customerId);
				
				$csvRow = array(
					'order_id' => $_order->getId(),
					'increment_id' => $_order->getIncrementId(),
					'customer_id' => $customerId,
					'date' => $_order->getCreatedAt(),
					'status' => $order->getStatusLabel(),
					'sku' => $sku,
					'name' => $customer->getName(),
					'email' => $customer->getEmail(),
				);
				
				if(! empty($csvRowAux)) {
					$csvRowAux += array(
						'name' => $csvRow['name'],
						'email' => $csvRow['email'],
					);
					fputcsv($fp, $csvRowAux);
				}
				
				fputcsv($fp, $csvRow);
				
				echo "\n";
			}
		}
	}
}
fclose($fp);

echo "processedCount: ".$processedCount."\n";
echo "errorCount: ".$errorCount."\n";
echo "END\n";
die();
