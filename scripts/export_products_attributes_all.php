<?php

// Configuration.
define('MAGENTO_ROOT', substr(__DIR__, 0, strrpos(__DIR__, '/')));
set_time_limit(0); // Set max limit.
$csvFile = "products_attributes_all.csv";


// Load Magento environment.
require_once MAGENTO_ROOT.'/app/Mage.php';
$app = Mage::app('default');

// Get all atributes.
$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
	->addVisibleFilter()
	->addFieldToFilter('is_user_defined', 1)
	->addFieldToFilter('attribute_code', array(
		'in' => array(
			'acne_treatment_benefits', 
			'anti_aging_treatment', 
			'cleanser_benefits', 
			'discoloration_benefits', 
			'exfoliator_benefits', 
			'eye_cream_benefits', 
			'ingredients',
			'moisturizer_benefits',
			'moisturizer_fragrance',
			'serum_benefits',
			'size',
			'skin_concern',
			'spf_benefits',
		),
	))
;
$attributeTitles = array();
foreach($attributeCollection as $_attribute) {
	$attributeTitles[$_attribute->getId()] = $_attribute->getFrontendLabel();
	$attributeCodes[$_attribute->getId()] = $_attribute->getAttributeCode();
}

// Open file.
$fp = fopen(__DIR__ . "/" . $csvFile, 'w');
if ($fp === false) {
	echo "The file ".$csvFile." does not exist." . PHP_EOL;
	exit;
}

// Add titles.
fputcsv($fp, array_merge(array('SKU', 'Attribute Set Name'), $attributeTitles));


// Get all products.
$products = Mage::getModel('catalog/product')->getCollection();
$attributeSets = array();
$count = 0;
foreach($products as $_product) {
	$product = Mage::getModel('catalog/product')->load($_product->getId());
	
	// Load attribute set name.
	$attributeSetId = $product->getAttributeSetId();
	if(! isset($attributeSets[$attributeSetId])) {
		$attributeSets[$attributeSetId] = Mage::getModel("eav/entity_attribute_set")->load($attributeSetId)->getAttributeSetName();
	}
	
	// Load attribute values.
	$attributeValues = array();
	foreach($attributeCodes as $_attributeId => $_code) {
		$attributeValues[] = $product->getResource()->getAttribute($_code)->getFrontend()->getValue($product);
	}
	
	// Save file.
	fputcsv($fp, array_merge(array(
		$_product->getSku(),
		$attributeSets[$attributeSetId],
	), $attributeValues));
	$count++;
}
fclose($fp);
die($count . " products exported" . PHP_EOL);
?>