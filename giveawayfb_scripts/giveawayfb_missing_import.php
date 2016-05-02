<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
define('MAGENTO_ROOT_LIVE', "/home/katesome/katesomerville.com/html");
$currentRoot = MAGENTO_ROOT_DEV3;


$maxOrderProcessed = 5;
$csvFileName = __DIR__ . "/giveawayfb_missing_export.csv";
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

// Get Freesamples Skus
$sampleSkus = $helper->getSampleKitSkus();




// Walk through the file.
$line = 0;
$newOrdersCreated = 0;
$errors = 0;
while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
	// Break.
	if(! empty($maxOrderProcessed) && $newOrdersCreated >= $maxOrderProcessed) {
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
	$giveawayfb_id = $data[$titles['GiveAwayFb Id']];
	$customer_id = $data[$titles['Magento Customer Id']];
	
	// Check data.
	if(empty($giveawayfb_id)) {
		$errors++;
		echo ' - INVALID giveawayfb_id'."\n";
		continue;
	}
	if(empty($customer_id)) {
		$errors++;
		echo ' - INVALID customer_id'."\n";
		continue;
	}
	echo " - giveawayfb_id: #".$giveawayfb_id;
	
	// Get data from table.
	$model->load($giveawayfb_id);
	
	// If the table doen't has cutomer id, set it.
	if($model->getCustomerId() == null) {
		$model->setCustomerId($customer_id);
	}
	
	// Check Magento orders.
	$order_id = $helper->checkCustomerOrders($customer_id, $sampleSkus);
	
	if(! empty($order_id)) {
		echo " - OLD order id: #".$order_id;
		$order = Mage::getModel('sales/order')->load($order_id);
		$magentoIncrementId = $order->getIncrementId();
	} else {
		// Load customer data.
		$customer = Mage::getModel("customer/customer")->load($customer_id);
		$data = $model->getData();
		
		
		// Check for duplated emails.
		$customerCollection = Mage::getResourceModel('customer/customer_collection');
		$customerCollection->addAttributeToFilter('email', $data['email']);
		echo " - customerCollection: ".$customerCollection->count();
		
		if($customerCollection->count() > 1) {
			foreach($customerCollection as $_customer) {
				$_customer = Mage::getModel("customer/customer")->load($_customer->getId());
				
				$customerAddressId = $_customer->getDefaultBilling();
				if(! $customerAddressId) {
					Mage::register('isSecureArea', true);
					$_customer->delete();
					Mage::unregister('isSecureArea');
					echo " - customer delete: ".$_customer->getId();
				} else {
					// Reload customer.
					$customer_id = $_customer->getId();
					$customer = Mage::getModel("customer/customer")->load($customer_id);
					$model->setCustomerId($customer_id);
				}
			}
			
			echo " - new customer: ".$customer_id;
		}
		
		
		// Check address customer.
		$customerAddressId = $customer->getDefaultBilling();
		echo " - customerAddressId: ".$customerAddressId;
		if(! $customerAddressId) {
			// Load new Address.
			$customerAddressId = $model->createCustomerAddress($customer_id, $data);
			echo " - new customerAddressId: ".$customerAddressId;
		}
		
		// Create order.
		$newOrder = $model->createNewOrder($customer_id, $model->getSamplekit());
		$newOrdersCreated++;
		
		if(! $newOrder) {
			$errors++;
			echo " - ERROR CREATING ORDER (Customer Id #".$customer_id." - Samplekit: ".$model->getSamplekit().").\n";
			
			// Save data in table.
			$model->save();
			continue;
		}
		$order_id = $newOrder->getId();
		$magentoIncrementId = $newOrder->getIncrementId();
		echo " - NEW order id: #".$order_id." (".$newOrdersCreated.")";
		
		// Add $10 credit to customer.
		$creditAdded = $model->addInitialCredit($customer_id);
		
		// Generate customer passwords and send email.
		$newPassword = $helper->generateNewPassword();
		$customer->setPassword($newPassword);
		$customer->save();
		$emailSended = $model->sendConfirmationEmail($order_id, $newPassword);
		
		$model->setOrderCreation(date('Y-m-d H:i:s'));
	}
	$model->setOrderId($order_id);
	$model->setIncrementId($magentoIncrementId);
	
	// Save data in table.
	$model->save();
	
	
	// Clean model.
	$model->cleanModelCache();
	$model->clearInstance();
	
	echo "\n";
}

echo "newOrdersCreated: ".$newOrdersCreated."\n";
echo "errors: ".$errors."\n";
echo "END\n";
die();
?>