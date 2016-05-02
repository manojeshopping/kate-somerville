<?php
class Alliance_Ajaxcart_Model_Observer {
	
	public function checkFreesamples($observer)
	{
		// Mage::log("checkFreesamples: ".$observer->getEvent()->getName().".", null, 'ajaxcart.log');
		
		// Initiate helper.
		$helper = $this->_getHelper();
		// Initiate model.
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Check if module is enabled.
		if(! $helper->getAjaxcartEnabled()) {
			Mage::log("checkFreesamples: Module disbabled.", null, 'ajaxcart.log');
			return true;
		}
		
		// Check if freesamples is enabled.
		if(! $helper->getFreesampleEnabled()) {
			Mage::log("checkFreesamples: Freesamples disbabled.", null, 'ajaxcart.log');
			return true;
		}
		
		
		// Check freesamples.
		$model->checkFreesamples();
		// Mage::log("checkFreesamples: Freesamples checked.", null, 'ajaxcart.log');
		return true;
	}
	
	/**
	* Executes on dispatched event "controller_action_predispatch_checkout_cart_add".
	* Check if the product is $0. If so, the item is not added to cart.
	*
	* @param $observer
	*/

	public function checkFreeItems($observer)
	{
		// Mage::log("checkFreeItems: ".$observer->getEvent()->getName().".", null, 'ajaxcart.log');
		$productId = (int)Mage::app()->getRequest()->getParam('product');
		// Mage::log("checkFreeItems - productId: ".$productId.".", null, 'ajaxcart.log');
		
		$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
		// Mage::log("checkFreeItems - price: ".$product->getPrice()." - getTypeId: ".$product->getTypeId().".", null, 'ajaxcart.log');
		
		
		// Check if product is $0 dollar.
		if(($product->getTypeId() != 'giftcard' && $product->getPrice() == 0) || ($product->getTypeId() == 'giftcard' && (float)Mage::app()->getRequest()->getParam('giftcard_amount') == 0)) {
			Mage::log("checkFreeItems - Trying to add $0 item. Redirecting.", null, 'ajaxcart.log');
			$this->_getSession()->addError('Error adding item.');
			// Mage::log("checkFreeItems - Error added.", null, 'ajaxcart.log');
			Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/cart"))->sendResponse();
			// Mage::log("checkFreeItems - Redirected.", null, 'ajaxcart.log');
			exit;
		}
	}
	
	
	protected function _getHelper()
	{
		return Mage::helper('ajaxcart');
	}

	/**
	* Get checkout session model instance
	*
	* @return Mage_Checkout_Model_Session
	*/
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
}
