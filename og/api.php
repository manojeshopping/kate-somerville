<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * Api execution file. Runs the OrderGroove store,
 * which is a part of the OrderGroove Website.
 * 
 * Orders created from this website will
 * have different starting order ids from 
 * those created in the standard website
 */
	//var_dump($_POST);  // OG Debug 2015-06-15
	error_log("\nREQUEST: \n" . print_r($_REQUEST,true));
	error_log("\nPOST: \n" . print_r($_POST,true));
	error_log("\nSERVER: \n" . print_r($_SERVER,true));
	
	$storeCode	=	trim($_GET['store_code']);
	if(!$storeCode){
		$storeCode = 'og_store';
	}
	
	try{
		require_once '../app/Mage.php';
		Mage::app($storeCode)->setUseSessionInUrl(false);
		umask(0);
	}
	catch(Exception $e){
		$xml= '<?xml version="1.0" encoding="UTF-8"?>
		<order>
			<code>ERROR</code>
			<errorCode>999</errorCode>
			<errorMsg>Could not load Magento store. Please make sure that the store_code parameter is correct. The store code used in this request is:'.$storeCode.'</errorMsg>
		</order>';
		echo $xml;
		exit;
	}
	
/**
 * API class. Contains logic to handle the POST
 * request and hook into the Magento OrderGroove
 * api.
 */
class Api{
	public function run(){
		/**
		 * Set the flag to skip order subscription creation
		 */
		Mage::register(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_SKIP_SUBSCRIPTION, 1);
		if($this->_validate()){
			
			/**
			 * Instantiate order model, set the data received,
			 * and process the order
			 */    
			//OG Debug 2015-06-15
			//Zend_Debug::dump(Mage::app()->getRequest()->getParams());
			
			Mage::log('run(): params',null,'ordergroove_debug.log');
			Mage::log(Mage::app()->getRequest()->getParams(),null,'ordergroove_debug.log');			
			 
			$order	=	Mage::getModel('ordergroove/api_order');
			$order->setFeedData(Mage::app()->getRequest()->getPost());
			$order->process();
			
			/**
			 * Output result
			 */
			echo $order->getResult();
		}
	}

	protected function _validate(){
		return TRUE;
	}
}
	/**
	 * Execute code to create order.
	 */
	$api	=	new Api();
	$api->run();