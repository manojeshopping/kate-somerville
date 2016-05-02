<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
define('MAGENTO_ROOT_LIVE', "/home/katesome/katesomerville.com/html");
$currentRoot = MAGENTO_ROOT_DEV3;

$maxOrderProcessed = 20;
$logFileName = $currentRoot . "/var/log/giveawayfb".time().".log";

set_time_limit(0); // Set max limit

// Initialize the log file.
$fp = fopen($logFileName, 'w');
fwrite($fp, date('Y-m-d H:i:s')." - Starts script.\n");


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');


// Load Giveaway Model and helper.
$model = Mage::getModel('giveawayfb/giveawayfb');
$helper = Mage::helper('giveawayfb');

// Get Freesamples Skus
$sampleSkus = $helper->getSampleKitSkus();


$customers = $model->getConfirmedCustomers($maxOrderProcessed);
$count = $customers->count();
fwrite($fp, date('Y-m-d H:i:s')." - Orders to be processed: ".$count.".\n");
fwrite($fp, "===============================\n");
$insertedCount = 0;
$errorCount = 0;
foreach($customers as $_customer) {
	$customerId = $_customer->getId();
	$_real_customerId = $_customer->getCustomerId();
	echo "Giveaway ID: #".$customerId;
	fwrite($fp, date('Y-m-d H:i:s')." - Giveaway ID: #".$customerId);
	
	$magentoCustomerId = $_customer->getCustomerId();
	if(empty($magentoCustomerId)) {
		echo " - EMPTY CUSTOMER ID.\n";
		fwrite($fp, " - EMPTY CUSTOMER ID.\n");
		$errorCount++;
		continue;
	}
	
	echo " - Customer ID: #".$_customer->getCustomerId();
	fwrite($fp, " - Customer ID: #".$_customer->getCustomerId());
	
	// Check Magento orders.
	//$order_id = $helper->checkCustomerOrders($customerId, $sampleSkus);
	$order_id = $helper->checkCustomerOrders($_real_customerId, $sampleSkus);
	if(! empty($order_id)) {
		echo " - OLD order id: #".$order_id."\n";
		fwrite($fp, " - OLD order id: #".$order_id."\n");
		$errorCount++;
		continue;
	}
	
	// Create new Order.
	//Temporary Fix - the product 481 has been replaced by 479
	if( $_customer->getSamplekit() == 481 ) $x_samplekit = 479;
	else $x_samplekit = $_customer->getSamplekit();
	
	$magentoOrder = $model->createNewOrder($magentoCustomerId, $x_samplekit);
	if(! $magentoOrder) {
		echo " - ERROR CREATING ORDER.\n";
		fwrite($fp, " - ERROR CREATING ORDER.\n");
		$errorCount++;
		continue;
	} else {
		$magentoOrderId = $magentoOrder->getId();
		$magentoIncrementId = $magentoOrder->getIncrementId();
		echo " - magentoOrderId: #".$magentoOrderId." - magentoIncrementId: ".$magentoIncrementId;
		fwrite($fp, " - magentoOrderId: #".$magentoOrderId." - magentoIncrementId: ".$magentoIncrementId);
		$insertedCount++;
		
		// Add $10 credit to customer.
		$creditAdded = $model->addInitialCredit($magentoCustomerId);
		fwrite($fp, " - Credit addedd");
		
		$emailSended = $model->sendConfirmationEmail($magentoOrderId, $_customer->getCustomerPassword());
		fwrite($fp, " - email sended");
	}
	
	// Update Giveaway table.
	$updated = $model->updateData($customerId, array(
		'customer_password' => '',
		'order_creation' => date('Y-m-d H:i:s'),
		'order_id' => $magentoOrderId,
		'increment_id' => $magentoIncrementId,
	));
	if(! $updated) {
		echo " - ERROR SAVING DATA.\n";
		fwrite($fp, " - ERROR SAVING DATA.\n");
		continue;
	}
	fwrite($fp, " - data updated.\n");
	
	echo "\n";
}

fwrite($fp, "===============================\n");
fwrite($fp, "Inserted Count: ".$insertedCount."\n");
fwrite($fp, "Error Count: ".$errorCount."\n");
fclose($fp);
echo "END\n";
?>