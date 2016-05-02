<?php

// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katedev113.dev3.alliance-global.com");
define('MAGENTO_ROOT_MDEV', "/var/vhosts/katedev113.mdev.alliance-global.com");
define('MAGENTO_ROOT_ARMANDO', "/var/vhosts/katedev113.armando.alliance-global.com");
define('MAGENTO_ROOT_LIVE', "/home/katesome/katesomerville.com/html");
$currentRoot = MAGENTO_ROOT_DEV3;

//$maxOrderProcessed = 10;
$csvFileName = __DIR__ . "/giveawayfb_missing_export.csv";
set_time_limit(0); // Set max limit


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');


// Load Giveaway Model and helper.
$model = Mage::getModel('giveawayfb/giveawayfb');
$helper = Mage::helper('giveawayfb');

// Get state list.
$statesArray = $helper->getUSAStates();

// Get concerns lists.
$concerns1Array = processConcernList($helper->getConcernsList());

// Get SampleKits list.
$sampleKitsArray = $helper->getProductList();


// Get without orders registers.
$collection = $model->getCollection()
	->addFieldToFilter('confirmid', array('notnull' => true))
	->addFieldToFilter('order_id', array('null' => true))
;
$collection->getSelect()->group('email');
$collection->getSelect()->order('giveawayfb_id ASC');
if(isset($maxOrderProcessed)) $collection->getSelect()->limit($maxOrderProcessed);
echo "count: ".$collection->count()."\n";

// Save data in CSV file.
$fp = fopen($csvFileName, 'w');
$csvHeader = array(
	'giveawayfb_id' => "GiveAwayFb Id",
	'name' => "Name",
	'lastname' => "Lastname",
	'email' => "Email",
	'birthdate' => "Birthdate",
	'telephone' => "Telephone",
	'zip' => "Zip",
	'address1' => "Address Line 1",
	'address2' => "Address Line 2",
	'city' => "City",
	'state' => "State",
	'skin_concern1' => "Skin Concern 1",
	'skin_concern2' => "Skin Concern 2",
	'samplekit' => "Sample Kit",
	'customer_id' => "Magento Customer Id",
);
fputcsv($fp, $csvHeader);


foreach($collection as $_customer) {
	echo "Giveaway ID: #".$_customer->getId();
	
	// Load Giveaway table data.
	$csvRow = array(
		'giveawayfb_id' => $_customer->getId(),
		'name' => $_customer->getName(),
		'lastname' => $_customer->getLastname(),
		'email' => $_customer->getEmail(),
		'birthdate' => $_customer->getBirthdateMonth().'/'.$_customer->getBirthdateDay().'/'.$_customer->getBirthdateYear(),
		'telephone' => $_customer->getTelephone(),
		'zip' => $_customer->getZip(),
		'address1' => $_customer->getAddress1(),
		'address2' => $_customer->getAddress2(),
		'city' => $_customer->getCity(),
		'state' => $statesArray[$_customer->getState()]['title'],
		'skin_concern1' => $concerns1Array[$_customer->getSkinConcern1()],
		'skin_concern2' => $concerns1Array[$_customer->getSkinConcern2()],
		'samplekit' => $sampleKitsArray[$_customer->getSamplekit()]['title'],
	);
	
	// Get Mangento customer.
	$magentoCustomerId = $_customer->getCustomerId();
	if(empty($magentoCustomerId)) {
		// Load customer by email.
		$magentoCustomer = $model->customerExists($csvRow['email']);
		if($magentoCustomer) {
			$magentoCustomerId = $magentoCustomer->getId();
		} else {
			echo " - NOT IN MAGENTO (".$csvRow['email'].")\n";
			continue;
		}
	}
	
	echo " - magentoCustomerId: #".$magentoCustomerId;
	$csvRow['customer_id'] = $magentoCustomerId;
	
	
	fputcsv($fp, $csvRow);
	echo "\n";
}
fclose($fp);

echo "END\n";
die();




function processConcernList($concernList)
{
	$newList = array();
	foreach($concernList as $_concern) {
		if(empty($_concern['value'])) continue;
		
		$newList[$_concern['value']] = $_concern['label'];
	}
	
	return $newList;
}
?>