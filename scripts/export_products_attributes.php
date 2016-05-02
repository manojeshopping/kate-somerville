<?php




// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katesomerville.com");
$_SERVER['DOCUMENT_ROOT'] = MAGENTO_ROOT_DEV3;
$currentRoot = MAGENTO_ROOT_DEV3;


$benefitsCodes = array('acne_treatment_benefits', 'cleanser_benefits', 'discoloration_benefits', 'exfoliator_benefits', 'eye_cream_benefits', 'moisturizer_benefits', 'serum_benefits', 'spf_benefits');
set_time_limit(0); // Set max limit.


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');

// Load Magento DB Resource
$resource = Mage::getSingleton('core/resource');
$readConn = $resource->getConnection('core_read');
$writeConn = $resource->getConnection('core_write');

// Open file.
$fp = openFile(__DIR__ . "/products_attributes.csv");
fputcsv($fp, array('Product Name', 'Type', 'SKU', 'Attribute Set Name', 'Skin Concern(s)', 'Benefits - Quiz', 'status', 'qty'));

$products = Mage::getModel('catalog/product')->getCollection();
$attributeSets = array();
$skinConcerns = array();
$benefits = array();
$benefitsAttribute = array();
foreach($products as $_product) {
	$product = Mage::getModel('catalog/product')->load($_product->getId());
	
	// Load attribute set name.
	$attributeSetId = $product->getAttributeSetId();
	if(! isset($attributeSets[$attributeSetId])) {
		$attributeSets[$attributeSetId] = Mage::getModel("eav/entity_attribute_set")->load($attributeSetId)->getAttributeSetName();
	}
	
	// Load Skin Concern.
	if(! isset($skinConcernAtribute)) $skinConcernAtribute = $product->getResource()->getAttribute('skin_concern');
	$skinConcernValues = "";
	$skinConcernIds = $product->getSkinConcern();
	if(! empty($skinConcernIds)) {
		$skinConcernIdsArray = explode(',', $skinConcernIds);
		foreach($skinConcernIdsArray as $_skinConcernId) {
			if(! isset($skinConcerns[$_skinConcernId])) {
				$skinConcerns[$_skinConcernId] = $skinConcernAtribute->getSource()->getOptionText($_skinConcernId);
			}
			
			if(! empty($skinConcernValues)) $skinConcernValues .= ";";
			$skinConcernValues .= $skinConcerns[$_skinConcernId];
		}
	}
	
	// Load Benefits.
	$benefitsValues = "";
	foreach($benefitsCodes as $_code) {
		if(! isset($benefitsAttribute[$_code])) $benefitsAttribute[$_code] = $product->getResource()->getAttribute($_code);
		
		$benefitsIds = $product->getData($_code);
		if(! empty($benefitsIds)) {
			$benefitsIdsArray = explode(',', $benefitsIds);
			foreach($benefitsIdsArray as $_benefitId) {
				if(! isset($benefits[$_benefitId])) {
					$benefits[$_benefitId] = $benefitsAttribute[$_code]->getSource()->getOptionText($_benefitId);
				}
				
				if(! empty($benefitsValues)) $benefitsValues .= ";";
				$benefitsValues .= $benefits[$_benefitId];
			}
		}
	}
	
	// Load stock.
	$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
	
	fputcsv($fp, array(
		$product->getName(),
		$product->getTypeId(),
		$product->getSku(),
		$attributeSets[$attributeSetId],
		$skinConcernValues,
		$benefitsValues,
		($product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED ? 'enabled' : 'disabled'),
		(int)$stock->getQty(),
	));
}
fclose($fp);
die("\n");

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





