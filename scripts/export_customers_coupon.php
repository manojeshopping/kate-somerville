<?php
// Get params.
if(empty($argv[1])) {
	echo "Missing coupon code.".PHP_EOL;
	exit;
}
$couponCode = $argv[1];

// Configuration.
define('MAGENTO_ROOT', substr(__DIR__, 0, strrpos(__DIR__, '/')));
$_SERVER['DOCUMENT_ROOT'] = MAGENTO_ROOT;
$csvFile = __DIR__ . "/customers_coupon_".$couponCode.".csv";
set_time_limit(0); // Set max limit.

// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');


// Open file.
$fp = fopen($csvFile, 'w');
fputcsv($fp, array(
	'Customer ID', 'Email', 'Name', 'Last Name', 'Street Address', 'City', 'Country', 'State/Province', 'Zip/Postal Code', 
	'Order ID', 'Order Amount Total', 'Order Status'
));

$orders = Mage::getModel('sales/order')
	->getCollection()
	->addFieldToFilter('coupon_code', $couponCode)
;
$orders->getSelect()->joinLeft(
	array('shipping' => 'sales_flat_order_address'),
	'main_table.entity_id = shipping.parent_id AND shipping.address_type = "shipping"',
	array('shipping.street', 'shipping.city', 'shipping.country_id', 'shipping.region', 'shipping.postcode')
);
$count = 0;
foreach($orders as $_order) {
	$count++;
	fputcsv($fp, array(
		$_order->getCustomerId(),
		$_order->getCustomerEmail(),
		$_order->getCustomerFirstname(),
		$_order->getCustomerLastname(),
		$_order->getStreet(),
		$_order->getCity(),
		$_order->getCountryId(),
		$_order->getRegion(),
		$_order->getPostcode(),
		$_order->getIncrementId(),
		$_order->getGrandTotal(),
		$_order->getStatusLabel(),
	));
}
echo $count." order exported in ".$csvFile." file.".PHP_EOL;
fclose($fp);
exit;
?>