<?php
class Alliance_RequiredSKU_IndexController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		// Check Module availability.
		if(! $this->_getHelper()->checkRequiredSKUOffer()) {
			$redirect = Mage::getUrl('checkout/cart');
			$this->getResponse()->setRedirect($redirect);
			return;
		}
		
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function addRequiredSKUAction()
	{
		// Get data.
		$productId = $this->getRequest()->getPost('productId');
		$helper = $this->_getHelper();
		
		// Check data.
		if(empty($productId) || ! is_numeric($productId)) {
			$this->_redirectToCheckout();
			return;
		}
		
		// Check Module availability.
		if(! $helper->checkRequiredSKUOffer()) {
			$this->_setErrorMessage("The gift item could not be added to the order.");
			$this->_redirectToCart();
			return;
		}
		
		$product = $helper->initProduct($productId);
		if(! $product) {
			$this->_setErrorMessage("The product was not found.");
			$this->_redirectToCart();
			return;
		}
		
		// Add product to cart.
		try {
			$cart = Mage::getSingleton('checkout/cart');
			$cart->addProduct($product, array('qty' => 1));
			$cart->save();
			$this->_getSession()->setCartWasUpdated(true);
		} catch (Exception $e) {
			Mage::log("addRequiredSKUAction. Exception: ".$e->getMessage().".", null, 'giftofchoice.log');
			
			$this->_setErrorMessage("Error adding product to cart.");
			$this->_redirectToCart();
			return;
		}
		
		// Redirect to Checkout.
		$this->_redirectToCheckout();
		return;
	}
	
	
	private function _redirectToCart()
	{
		$redirect = Mage::getUrl('checkout/cart');
		$this->getResponse()->setRedirect($redirect);
	}
	private function _redirectToCheckout()
	{
		$redirect = Mage::helper('checkout/url')->getCheckoutUrl();
		$this->getResponse()->setRedirect($redirect);
	}
	
	private function _setErrorMessage($msg)
	{
		Mage::getSingleton('core/session')->addError($this->__($msg));
		session_write_close();
	}
	
	private function _getHelper()
	{
		return Mage::helper('giftofchoice');
	}
	private function _getSession()
	{
		return Mage::getSingleton('core/session');
	}

}

