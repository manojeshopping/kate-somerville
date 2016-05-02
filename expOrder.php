<?php
require_once 'app/Mage.php';
$app = Mage::app();

$gp = Mage::getModel('gp/gp');
$gp->getBatchOrders();
?>