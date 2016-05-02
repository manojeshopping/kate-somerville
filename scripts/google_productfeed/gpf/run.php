<?php
function run() {
require_once $_SERVER['DOCUMENT_ROOT'].'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);

umask(0);
Mage::app();

$fhandle = fopen("kateproducts.php", "w");
$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('status',array('eq' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED));
fwrite($fhandle, "<? header('Content-Disposition: attachment; filename=\"kateproducts.txt\"'); ?>");
fwrite($fhandle, "id    title   description     link    price   condition       image link      MPN\n");
foreach ($products as $product) {
  if ($product->getFinalPrice() <= 0) continue;
  $upc = $product->getResource()->getAttributeRawValue($product->getId(), "upc_code", Mage::app()->getStore());
  fwrite($fhandle, $product->getSku() . "\t" . $product->getName() . "\t" .
        strip_tags(str_replace(array("\x0D", "&mdash;" , "\t", "\n"), array("","-"," "," "), trim($product->getDescription())))
         . "\t" . $product->getProductUrl() . "\t" . round($product->getFinalPrice(), 2) . " USD" . "\t" . "new" . "\t" .
        $product->getImageUrl() . "\t" . $upc . "\n");
}

fclose($fhandle);

}

run();

?>
