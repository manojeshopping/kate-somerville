<?php
function run() {
require_once $_SERVER['DOCUMENT_ROOT'].'app/Mage.php';

Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);

umask(0);
Mage::app();

$fhandle = fopen("kateproducts.php", "w");
$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)->addFieldToFilter(array(
        array('attribute'=>'price','gt'=>'0')
));
$products = $products->joinField( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' ) ->addAttributeToFilter('qty', array('gt' => 0));
//$products = Mage::getModel('catalog/product')->getCollection();
//$products = Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
//$product = Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
$count =  count($products);
fwrite($fhandle, "<? header('Content-Disposition: attachment; filename=\"kateproducts.txt\"'); ?>");
fwrite($fhandle, "id    title   description     link    price   condition       image link      MPN\n");
foreach ($products as $product) {
  //printf("sku = " . $product->getSku() . "</br>\n");
  if (stristr($product->getSku(),"_") !== FALSE) {
    $count--;
    continue;
  }
  //printf("count = " . $count . "\n");
  //if ($product->getFinalPrice() <= 0) continue;
  $upc = $product->getResource()->getAttributeRawValue($product->getId(), "upc_code", Mage::app()->getStore());
  fwrite($fhandle, $product->getSku() . "\t" . $product->getName() . "\t" .
        strip_tags(str_replace(array("\x0D", "&mdash;" , "\t", "\n"), array("","-"," "," "), trim($product->getDescription())))
         . "\t" . $product->getProductUrl() . "\t" . round($product->getFinalPrice(), 2) . " USD" . "\t" . "new" . "\t" .
        $product->getImageUrl() . "\t" . $upc . "\n");
}

fclose($fhandle);

}

$password = "ks6006le";

if (isset($_POST['run'])) {
  run();
}

if (isset($_GET['key']) && $_GET['key'] == "true") {
  $_SESSION['logged'] = true;
}

if (isset($_POST['logout'])) {
  session_start();
  session_destroy();
}

?>
<!doctype html>
<html>
<head>
  <script src="js/jquery-1.10.2.min.js"></script>
  <script src="js/jquery-ui-1.10.3/ui/jquery-ui.js"></script>
  <link rel="stylesheet" href="js/jquery-ui-1.10.3/themes/base/jquery-ui.css"/>
</head>
<body>
<span>
<h2>Kate Products 1.0</h2>
</span>
<div id="main"></div>
<?
  if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
$time = filectime("kateproducts.php");
printf("Timestamp of kateproducts.txt - " . date("Y-m-d H:i:s", $time) . "<br/>");
?>
<form method=post>
<input type=submit name="run" value="Generate"/>
</form>
<input type="button" value="Download Now!" onclick="window.location = 'kateproducts.php';">
<form method=post>
<input type=submit name="logout" value="Logout"/>
</form>
<?
  } else {
?> 
<script>
  $(function() {
    $( "#dialog-message" ).dialog({
      modal: true,
      buttons: {
        Ok: function() {
          value = $("#password").val();
	  $.ajax({
	    url: "pwd.php?pwd=" + value,
            type: "POST",
            data: { pwd: value },
            success: function(msg) {
						alert(msg);
                       if (msg != "fail") {
					   alert('loading');
                         window.location.href = "index.php?key=" + msg;
                       } else {
                       alert('fail');
					   }
                     }
          });
        }
      }
    });
  });
</script>
</head>
<?
  }

  if (!isset($_SESSION['logged']) || $_SESSION['logged'] != true) {
?>
<div id="dialog-message" title="Google Product Feed">
<p>
<span id="msg">Enter your password</span>
<input type="password" id="password" class="text ui-widget-content ui-corner-all"/>
</p>
</div>
<?
  }
?>

</body>
</html>
