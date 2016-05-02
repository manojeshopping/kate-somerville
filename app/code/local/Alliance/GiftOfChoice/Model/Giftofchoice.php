<?php


class Alliance_GiftOfChoice_Model_Giftofchoice extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('giftofchoice/giftofchoice');
	}
	
	
	public function checkOffer()
	{
		// Mage::log("checkOffersPage: checkOffer.", null, 'giftofchoice.log');
		
		return $this->_getHelper()->checkGiftOfChoice();
	}
	
	public function addOffer($request)
	{
		// Get data.
		$productId = $request['productId'];
		$helper = $this->_getHelper();
		
		// Check data. If no productId, then, the user does not selected any offer.
		if(empty($productId) || ! is_numeric($productId)) {
			return true;
		}
		
		// Check Module availability.
		if(! $helper->checkGiftOfChoice()) {
			$this->_setErrorMessage("The gift item could not be added to the order.");
			return false;
		}
		
		$product = $helper->initProduct($productId);
		if(! $product) {
			$this->_setErrorMessage("The product was not found.");
			return false;
		}
		
		// Add product to cart.
		try {
			$cart = Mage::getSingleton('checkout/cart');
			$cart->addProduct($product, array('qty' => 1));
			$cart->save();
			$this->_getSession()->setCartWasUpdated(true);
		} catch (Exception $e) {
			Mage::log("addGiftAction. Exception: ".$e->getMessage().".", null, 'giftofchoice.log');
			
			$this->_setErrorMessage("Error adding product to cart.");
			return false;
		}
		
		return true;
	}
	
	public function checkOfferInCart()
	{
		// Mage::log("checkOfferInCart.", null, 'giftofchoice.log');
		
		// Get cart and helper.
		$cart = Mage::getSingleton('checkout/cart');
		$helper = $this->_getHelper();
		// Mage::log("checkOfferInCart.Loaded cart and helper.", null, 'giftofchoice.log');
		
		// Get item in cart.
		$cartItem = $helper->getItemInCart();
		if(! $cartItem) return true;
		// Mage::log("checkOfferInCart.cartItem: ".$cartItem->getId().".", null, 'giftofchoice.log');
		
		// Check product quantity.
		$qty = $cartItem->getQty();
		// Mage::log("checkOfferInCart.qty: ".$qty.".", null, 'giftofchoice.log');
		if($qty > 1) {
			// Update item.
			try {
				$cartData = array($cartItem->getId() => array('qty' => 1));
				$cartData = $cart->suggestItemsQty($cartData);
				Mage::log("checkOfferInCart.cartData: ".print_r($cartData, 1).".", null, 'giftofchoice.log');
				
				$cart->updateItems($cartData);
				$cart->save();
				return true;
			} catch (Exception $e) {
				Mage::log("checkOfferInCart.getMessage: ".$e->getMessage().".", null, 'giftofchoice.log');
				return true;
			}
		}
		
		// Check if already deleted.
		$deleted = $this->_getSession()->getData('giftofchoice_product_deleted');
		$this->_getSession()->setData('giftofchoice_product_deleted', false);
		// Mage::log("checkOfferInCart.deleted: ".($deleted ? 1 : 0).".", null, 'giftofchoice.log');
		if($deleted) return true;
		
		// Check if can be applied.
		// Mage::log("checkOfferInCart: checkGiftOfChoice: ".($helper->checkGiftOfChoice(false) ? 1 : 0).".", null, 'giftofchoice.log');
		if(! $helper->checkGiftOfChoice(false)) {
			// Remove item.
			try {
				Mage::log("checkOfferInCart.removeItem: ".$cartItem->getId().".", null, 'giftofchoice.log');
				$cart->removeItem($cartItem->getId());
				
				// To avoid loop.
				$this->_getSession()->setData('giftofchoice_product_deleted', true);
				
				// Mage::log("checkOfferInCart.save before.", null, 'giftofchoice.log');
				$cart->save();
				return true;
			} catch (Exception $e) {
				Mage::log("checkOfferInCart.Exception: ".$e->getMessage().".", null, 'giftofchoice.log');
				return true;
			}
		}
		
		
		return true;
	}
	
	
	private function _setErrorMessage($msg)
	{
		$this->_getSession()->addError($msg);
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
