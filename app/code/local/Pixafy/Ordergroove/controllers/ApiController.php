<?php
/**
 * Deprecated class.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_ApiController extends Mage_Core_Controller_Front_Action{
	/*
	//
	//	Order API function. Receive order 
	//	XML from OrderGroove and create
	//	an order within Magento and send
	//	response back to Ordergroove
	//
	public function orderAction(){
		Mage::register(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_SKIP_SUBSCRIPTION, 1);
		Mage::log($this->getRequest()->getPost(), null, 'api.log', true);
		Mage::log('***************', null, 'api.log', true);
		Mage::log('***************', null, 'api.log', true);
		Mage::log('***************', null, 'api.log', true);
		
		if($this->_validate()){
			$order	=	Mage::getModel('ordergroove/api_order');
			$order->setFeedData($this->getRequest()->getPost());
			$order->process();
			Mage::log($order->getResult(), null, 'api.log', true);
			echo $order->getResult();
		}
	}
	
	//
	//	Validate function. Perform validation
	//	on the received API request.
	//
	//	#todo - Implement validation, for
	//	now just return true so that we can
	//	begin processing data
	//
	protected function _validate(){
		return TRUE;
	}
	*/
}
