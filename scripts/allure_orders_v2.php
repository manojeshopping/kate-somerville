<?php
//Script time
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime; 


// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/import.mdev.alliance-global.com");
$currentRoot = MAGENTO_ROOT_DEV3;

$fileName = __DIR__ . "/Quench_Hydrating_Face_Serum_winner_list_8_4_14.csv";
$delimiter = ",";
$passwordLength = 8;
$customerGroup = 1;
$defaultPhoneNumber = "55-555-5555";
$countryCode = "US";

$productSKU = "16272";
$shippingMethod = "alliance_shipping_promo_shipping";
$paymentMethod = "purchaseorder";
$poNumber = "AllureGift";
$orderComment = "Order created by Allure script.";

$templateExistingCustomer = "New Order_updated";
$templateNewCustomer = "New_Order_Temp_Password";

ini_set("auto_detect_line_endings", true); // For MAC end line.
set_time_limit(0); // Set max limit


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');

// Get csv file.
$handle = fopen($fileName, "r");
echo "Opening file: ".$fileName."\n";
if ($handle === FALSE) {
	echo "Error to open file.\n";
	die();
}


// Get website id and store.
$websiteId = Mage::app()->getWebsite()->getId();
$store = Mage::app()->getStore();

	
// Load product.
$product = Mage::getModel('catalog/product'); 
$product->load($product->getIdBySku($productSKU)); 
$productId = $product->getId();
if(empty($productId)) {
	echo 'Product not found (#'.$productId.')'."\n";
	continue;
}


// Walk through the file.
$line = 0;
while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
	$line++;
	echo "Line #".$line." - ";
	
	// *** First Line *** //
	if($line == 1) {
		echo "Firs line: TITLES\n";
		
		// Get titles names in $titles array.
		$num = count($data);
		for($i = 0; $i < $num; $i++) {
			$titles[$data[$i]] = $i;
		}
		
		continue;
	}
	// *** First Line *** //
	
	// *** Validate Data *** //
	$email = $data[$titles['Email']];
	if(empty($email) || ! Zend_Validate::is($email, 'EmailAddress')) {
		echo 'INVALID EMAIL'."\n";
		die("\n");
	}
	
	echo "Email: ".$email." - ";
	// *** Validate Data *** //
	
	
	// *** Customer *** //
	$customer = Mage::getModel('customer/customer')->loadByEmail($email);
	$newCustomer = false;
	$password = "";
	$customerId = $customer->getId();
	if (! $customerId) {
		$newCustomer = true;
		$password = $customer->generatePassword($passwordLength);
		
		// Create customer.
		$customer->setWebsiteId = $websiteId;
		$customer->setStore($store);
		
		$customer->setFirstname($data[$titles['First Name']]);
		$customer->setLastname($data[$titles['Last Name']]);
		$customer->setEmail($email);
		$customer->setPasswordHash();
		$customer->setGroupId($customerGroup);
		
		try {
			$customerId = $customer->save()->getId();
		} catch (Exception $e) {
			echo 'Creation customer error: '.$e->getMessage().''."\n";
			die("\n");
		}
		echo 'Customer created (#'.$customerId.') - ';
		$addressId = "";
	} else {
		$addressId = $customer->getDefaultBilling();
		echo 'Customer already exists (#'.$customerId.') - ';
	}
	// *** Customer *** //
	
	// *** Address *** //
	if(empty($addressId)) {
		// Create address for customer.
		$regionModel = Mage::getModel('directory/region')->loadByCode($data[$titles['State']], $countryCode);
		$stateId = $regionModel->getId();
		
		$address = Mage::getModel("customer/address");
		$address->setCustomerId($customerId);
		$address->setFirstname($customer->getFirstname());
		$address->setLastname($customer->getLastname());
		$address->setTelephone($defaultPhoneNumber);
		$address->setStreet(array($data[$titles['Address1']], $data[$titles['Address2']]));
		$address->setPostcode($data[$titles['Zip']]);
		$address->setCity($data[$titles['City']]);
		$address->setRegion($stateId);
		$address->setCountryId($countryCode);
		
		$address->setIsDefaultBilling(true);
		$address->setIsDefaultShipping(true);
		
		try {
			$addressId = $address->save()->getId();
			$addressData = $address->getData();
			echo 'Address created (#'.$addressId.') - ';
			
			// Set as default to customer.
			$customer->setDefaultBilling($addressId);
			$customer->setDefaultShipping($addressId);
			$customer->save();
		} catch (Exception $e) {
			echo 'Creation customer address error: '.$e->getMessage().''."\n";
			die("\n");
		}
	} else {
		$address = Mage::getModel('customer/address')->load($addressId);
		$addressData = $address->getData();
		echo 'Address already exists (#'.$addressId.') - ';
	}
	// *** Address *** //
	
	
	// *** Order *** //
	// Load storeid.
	$storeId = $store->getStoreId();
	
	try {
		// Create Quote.
		$quote = Mage::getModel('sales/quote')->setStoreId($storeId);
		$quote->setCustomer($customer);
		$quote->addProduct($product);
		
		// Set Address and Payment method.
		$billingAddress  = $quote->getBillingAddress()->addData($addressData);
		$shippingAddress = $quote->getShippingAddress()->addData($addressData);
		$shippingAddress->setCollectShippingRates(true)->collectShippingRates();
		$shippingAddress->setShippingMethod($shippingMethod);
		$shippingAddress->setFreeShipping(true);
		// $address->setShippingDescription($rate->getCarrierTitle().' - '.$rate->getMethodTitle());
		
		$quotePayment = $quote->getPayment();
		// $quotePayment->importData(array('method' => $paymentMethod));
		$quotePayment->setMethod($paymentMethod);
		$quotePayment->setPoNumber($poNumber);
		$quote->setPayment($quotePayment);
		
		// Save Quote.
		$quote->collectTotals()->save();
	} catch (Exception $e) {
		echo 'Creation quote error: '.$e->getMessage().''."\n";die();
		die("\n");
	}
	
	// Create order.
	$service = Mage::getModel('sales/service_quote', $quote);
	echo 'Create order - ';
	try {
		$service->submitAll();
		$newOrder = $service->getOrder();
		
		// $newOrder->setShippingAmount(0);
		// $newOrder->setBaseShippingAmount(0);
		
		// Save order.
		$orderId = $newOrder->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $orderComment)->save()->getId();
		echo 'Order Created (#'.$orderId.') - ';
	} catch (Exception $e) {
		echo 'Creation order error: '.$e->getMessage().''."\n";die();
		die("\n");
	}
	// *** Order *** //
	
	
	// *** Emails *** //
	// Get template.
	$emailTemplateName = ($newCustomer) ? $templateNewCustomer : $templateExistingCustomer;
	$emailTemplate = Mage::getModel('core/email_template')->loadByCode($emailTemplateName);
	
	// Set senders by default.
	$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
	$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
	
	// Set vars.
	$paymentBlock = Mage::helper('payment')->getInfoBlock($newOrder->getPayment());
	$vars = array(
		'store' => $store,
		'order' => $newOrder,
		'payment_html' => $paymentBlock->toHtml(),
		'password' => $password,
	);
	
	// XXXXXXXXXXXXXXXXX CHANGE!!!!!!! XXXXXXXXXXXXXXXXXX
	// $emailTemplate->send($email, $customer->getFirstname()." ".$customer->getLastname(), $vars);
	$emailTemplate->send("nycsistemas@gmail.com", $customer->getFirstname()." ".$customer->getLastname(), $vars);
	// XXXXXXXXXXXXXXXXX CHANGE!!!!!!! XXXXXXXXXXXXXXXXXX
	// *** Emails *** //
	
	print("OK\n");
}


   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $endtime = $mtime; 
   $totaltime = ($endtime - $starttime); 
   echo "\n This page was created in ".$totaltime." seconds"; 
	
die("\n END\n");
?>