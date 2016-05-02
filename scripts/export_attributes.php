<?php

// Configuration.
define('MAGENTO_ROOT', substr(__DIR__, 0, strrpos(__DIR__, '/')));
set_time_limit(0); // Set max limit.
$csvFile = "attributes.csv";


// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');

// Open file.
$fp = fopen(__DIR__ . "/" . $csvFile, 'w');
if ($fp === false) {
	echo "The file ".$csvFile." does not exist." . PHP_EOL;
	exit;
}

// Add titles.
fputcsv($fp, array('Attribute Code', 'Attribute Label', 'Label Options'));


// Get all atributes.
$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
	->addVisibleFilter()
	->addFieldToFilter('is_user_defined', 1)
;
$count = 0;
foreach($attributeCollection as $_attribute) {
	$optionsArray = $_attribute->getSource()->getAllOptions(false);
	$options = "";
	foreach($optionsArray as $_option) {
		if(! empty($options)) $options .= ", ";
		$options .= $_option['label'];
	}
	
	// Save file.
	fputcsv($fp, array(
		$_attribute->getAttributeCode(),
		$_attribute->getFrontendLabel(),
		$options,
	));
	$count++;
}
fclose($fp);
die($count . " attributes exported" . PHP_EOL);
?>