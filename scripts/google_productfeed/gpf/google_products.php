<?php

function run() {
require_once $_SERVER['DOCUMENT_ROOT'].'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);

umask(0);
Mage::app();

$fhandle = fopen("kateproducts.xml", "w");
$products = Mage::getModel('catalog/product')->getCollection()
                                             ->addAttributeToSelect('*');
fwrite($fhandle, '<?xml version="1.0"?>' . "\n");
fwrite($fhandle, '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">' . "\n");
fwrite($fhandle, '<title>Katesomerville.com Google Products XML</title>' . "\n");
fwrite($fhandle, '<link href="http://katedev112.armando.alliance-global.com.com" rel="alternate" type="text/html" />' . "\n");
//fwrite($fhandle, '<updated>2006-06-11T18:30:02Z</updated>' . "\n");
fwrite($fhandle, '<id>tag:katesomerville.com,' . date("Y-m-d") . '</id>' . "\n");
foreach ($products as $product) {
  fwrite($fhandle, '<entry>' .  "\n");
  fwrite($fhandle, '<title>' . $product->getName() . '</title>' . "\n");
  fwrite($fhandle, '<id>' . $product->getSku() . '</id>' . "\n");
  fwrite($fhandle, '<link href="' . $product->getProductUrl() . '"/>' . "\n");
  fwrite($fhandle, '<description>' .  
	strip_tags(str_replace(array("\x0D", "&mdash;"), array("","-"), trim($product->getDescription()))) . '</description>' . "\n");
  $upc = $product->getResource()->getAttributeRawValue($product->getId(), "upc_code", Mage::app()->getStore()); 
  fwrite($fhandle, '<g:mpn>' . $upc . '</g:mpn>' . "\n");
  fwrite($fhandle, '<g:image_link>' . $product->getImageUrl() . '</g:image_link>' . "\n");
  fwrite($fhandle, '<g:price>' . round($product->getFinalPrice(), 2) . ' USD</g:price>' . "\n"); 
  fwrite($fhandle, '<g:condition>new</g:condition>' . "\n");
  fwrite($fhandle, '</entry>' . "\n");
}
fwrite($fhandle, '</feed>' . "\n");

fclose($fhandle);

}

$password = "ks6006le";

if (isset($_POST['submit'])) {
  if ($_POST['password'] == $password) {
    run();
  }
}

$time = filectime("kateproducts.txt");
printf("Timestamp of kateproducts.txt - " . date("Y-m-d H:i:s", $time) . "<br/>");
?>
<form method=post>
<input type=password name="password"/>
<input type=submit name="submit"/>
</form>
