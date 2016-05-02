<?php




// Configuration.
define('MAGENTO_ROOT_DEV3', "/var/vhosts/katesomerville.com");
$_SERVER['DOCUMENT_ROOT'] = MAGENTO_ROOT_DEV3;
$currentRoot = MAGENTO_ROOT_DEV3;


// $maxRowsProcessed = 1;
set_time_limit(0); // Set max limit.


// Load Magento environment.
require_once $currentRoot.'/app/Mage.php';
$app = Mage::app('default');

// Load Magento DB Resource
$resource = Mage::getSingleton('core/resource');
$readConn = $resource->getConnection('core_read');
$writeConn = $resource->getConnection('core_write');

// Open file.
$fp = openFile("products.csv");
fputcsv($fp, array('sku', 'meta_title', 'meta_description'));

$products = Mage::getModel('catalog/product')->getCollection();
foreach($products as $_product) {
	$product = Mage::getModel('catalog/product')->load($_product->getId());
	
	fputcsv($fp, array(
		$_product->getSku(), 
		$product->getMetaTitle(),
		$product->getMetaDescription(),
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





