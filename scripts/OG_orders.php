<?php
// Configuration.
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

define('MAGENTO_ROOT', substr(__DIR__, 0, strrpos(__DIR__, '/')));
set_time_limit(0); // Set max limit.

// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');
$csvFile = "og_orders.csv";




// Open file.
$fp = openFile(__DIR__ . "/".$csvFile);
fputcsv($fp, array(
'Customer_Email', 
'Increment_id',
'Subtotal',
'Tax_Amount',
'Grand_Total',
'Region'
));

$store_name = "OrderGroove Website
OrderGroove Store
OrderGroove Store View";
 
 
$fromDate = '2015-01-01 00:00:00';
$toDate =  '2015-12-01 00:00:00';
 
 
$orders = Mage::getModel('sales/order')->getCollection()
    //->addFieldToFilter('store_name', $store_name)
	->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
    ;
	print_r( $orders->getSelect()->__toString() );
	echo "\n";

	
	
	
	
	
$states = array();	

$states[] = 'California'; 
$states[] = 'Illinois';
$states[] = 'Maryland';
$states[] = 'Washington';
$states[] = 'New Jersey';
$states[] = 'New York';

//print_r($states);
		

	
foreach ($orders as $order) {
    //$email = $order->getCustomerEmail();  //echo $order->getId() . ": '" . $order->getStatus() . "', " . $email . "\n"; //echo "test2 .. ";
	$billingAddress = $order->getShippingAddress();	
	//echo $order->getCustomerEmail()." - ".$order->getIncrementId()." - ".	$order->getSubtotal()." - ".	$order->getTaxAmount()." - ".	$order->getGrandTotal(). " - " .$billingAddress->getRegion()	;
	//echo "\n";

	
	if($billingAddress) $billingAddressRegion = $billingAddress->getRegion();
	else  $billingAddressRegion = "";
	
	
	
	if( in_array( $billingAddressRegion, $states)){

		$items = $order->getAllItems();
		$itemcount = count($items);
	
		if($order->getTaxAmount() == 0 && $order->getGrandTotal() > 0 && $itemcount > 0) {
		
				echo $order->getCustomerEmail()." - ".
					$order->getIncrementId()." - ".
					$order->getSubtotal()." - ".
					$order->getTaxAmount()." - ".
					$order->getGrandTotal(). " - " . 
					$billingAddressRegion
					;
			
				fputcsv($fp, array(
					$order->getCustomerEmail(),
					$order->getIncrementId(),
					$order->getSubtotal(),
					$order->getTaxAmount(),
					$order->getGrandTotal(),
					$billingAddressRegion
				));
			echo "\n";
			//break;			
		}
}	
	
	//print_r($order);	//echo $order->getCustomerEmail();	//$billingAddress = $order->getBillingAddress(); //echo $billingAddress->getRegion(); //getPostcode(); 	//print_r($billingAddress);
	//break;
}

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



?>