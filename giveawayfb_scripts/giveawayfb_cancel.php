<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
define('MAGENTO_ROOT_LIVE', "/home/katesome/katesomerville.com/html");
$currentRoot = MAGENTO_ROOT_DEV3;


// $maxOrderProcessed = 10;
$csvFileName = __DIR__ . "/Kate Somerville 128430 Batch 2_tobecancelled.csv";
$delimiter = ",";
set_time_limit(0); // Set max limit


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');

// Get csv file.
$handle = fopen($csvFileName, "r");
echo "Opening file: ".$csvFileName."\n";
if ($handle === FALSE) {
	echo "Error to open file.\n";
	die();
}


// Load Giveaway Model and helper.
$model = Mage::getModel('giveawayfb/giveawayfb');
$helper = Mage::helper('giveawayfb');


// Walk through the file.
$line = 0;
$processedCount = 0;
$errorsCount = 0;
while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
	// Break.
	if(! empty($maxOrderProcessed) && $processedCount >= $maxOrderProcessed) {
		echo "\n--------\nBreak: ".$maxOrderProcessed."\n--------\n";
		break;
	}
	
	$line++;
	echo "Line #".$line;
	
	// *** First Line *** //
	if($line == 1) {
		echo " - Firs line: TITLES\n";
		
		// Get titles names in $titles array.
		$num = count($data);
		for($i = 0; $i < $num; $i++) {
			$titles[$data[$i]] = $i;
		}
		
		continue;
	}
	// *** First Line *** //
	
	// Get data.
	$orderNumber = $data[$titles['Order Number']];
	echo " - orderNumber: ".$orderNumber;
	
	try {
		// Cancel order.
		$order = Mage::getModel('sales/order');
		$order->load($orderNumber, 'increment_id');
		// if(! $order->canCancel()) {
			// echo " - The order can't be canceled\n";
			// $errorsCount++;
			// continue;
		// }
		// $order->cancel();
		$order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED, true);
		$order->save();
		
		echo " - OK";
		$processedCount++;
	} catch (Exception $e) {
		echo " - ERROR: ".$e->getMessage()."\n";
		$errorsCount++;
		continue;
	}
	
	echo "\n";
}

echo "errorsCount: ".$errorsCount."\n";
echo "processedCount: ".$processedCount."\n";
echo "END\n";
die();
?>