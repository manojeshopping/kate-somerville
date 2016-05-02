<?php
//Load Magento API
require_once '../app/Mage.php';
Mage::app();
 
 //avatax/observer::processQueue
 //avatax/observer::cleanLog
 //catalogrule/observer::dailyCatalogUpdate
 
 
//First we load the model
//$model = Mage::getModel('avatax/observer');
//$model = Mage::getModel('catalogrule/observer'); 
 
 $model =  Mage::getModel('productalert/observer');
 
//Then execute the task
//$model->processQueue();
//$model->cleanLog();
//$model->dailyCatalogUpdate();


$model->process();