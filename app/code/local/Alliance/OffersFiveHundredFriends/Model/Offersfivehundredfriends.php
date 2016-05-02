<?php


class Alliance_OffersFiveHundredFriends_Model_Offersfivehundredfriends extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('offersfivehundredfriends/offersfivehundredfriends');
	}
	
	
	public function checkOffer()
	{
		// Mage::log("checkOffersPage: checkOffer.", null, 'alliance_offers.log');
		
		return $this->_getHelper()->checkOffersFiveHundredFriends();
	}
	
	public function addOffer($request)
	{
		// Mage::log("addOffer.", null, 'alliance_offers.log');
		
		// Get data.
		$productId = $request['productId'];
		$helper = $this->_getHelper();
		Mage::log("addOffer - productId: ".$productId.".", null, 'alliance_offers.log');
		
		// Check data. If no productId, then, the user does not selected any offer.
		if(empty($productId) || ! is_numeric($productId)) {
			return true;
		}
		
		// Check Module availability.
		if(! $helper->checkOffersFiveHundredFriends()) {
			$this->_setErrorMessage("The gift item could not be added to the order.");
			return false;
		}
		
		$product = $helper->initProduct($productId);
		// Mage::log("addOffer - product: ".$product->getSku().".", null, 'alliance_offers.log');
		if(! $product) {
			$this->_setErrorMessage("The product was not found.");
			return false;
		}
		
		// Add product to cart.
		try {
			$cart = Mage::getSingleton('checkout/cart');
			$cart->addProduct($product, array('qty' => 1));
			$cart->save();
			// Mage::log("addOffer - saved.", null, 'alliance_offers.log');
			$this->_getSession()->setCartWasUpdated(true);
		} catch (Exception $e) {
			Mage::log("addGiftAction. Exception: ".$e->getMessage().".", null, 'alliance_offers.log');
			
			$this->_setErrorMessage("Error adding product to cart.");
			return false;
		}
		
		return true;
	}
	
	public function checkOfferInCart()
	{
		Mage::log("checkOfferInCart.", null, 'alliance_offers.log');
		
		// Get cart and helper.
		$cart = Mage::getSingleton('checkout/cart');
		$helper = $this->_getHelper();
		Mage::log("checkOfferInCart.Loaded cart and helper.", null, 'alliance_offers.log');
		
		// Get item in cart.
		$cartItem = $helper->getItemInCart();
		if(! $cartItem) return true;
		Mage::log("checkOfferInCart.cartItem: ".$cartItem->getId().".", null, 'alliance_offers.log');
		
		// Check product quantity.
		$qty = $cartItem->getQty();
		Mage::log("checkOfferInCart.qty: ".$qty.".", null, 'alliance_offers.log');
		if($qty > 1) {
			// Update item.
			try {
				$cartData = array($cartItem->getId() => array('qty' => 1));
				$cartData = $cart->suggestItemsQty($cartData);
				Mage::log("checkOfferInCart.cartData: ".print_r($cartData, 1).".", null, 'alliance_offers.log');
				
				$cart->updateItems($cartData);
				$cart->save();
				return true;
			} catch (Exception $e) {
				Mage::log("checkOfferInCart.getMessage: ".$e->getMessage().".", null, 'alliance_offers.log');
				return true;
			}
		}
		
		// Check Minimum Order Amount.
		$deleted = $this->_getSession()->getData('offersfivehundredfriends_product_deleted');
		$this->_getSession()->setData('offersfivehundredfriends_product_deleted', false);
		Mage::log("checkOfferInCart.deleted: ".($deleted ? 1 : 0).".", null, 'alliance_offers.log');
		if($deleted) return true;
		
		// Mage::log("checkOfferInCart: checkGiftOfChoice: ".($helper->checkGiftOfChoice(false) ? 1 : 0).".", null, 'alliance_offers.log');
		if(! $helper->checkOffersFiveHundredFriends(false)) {
			// Remove item.
			try {
				Mage::log("checkOfferInCart.removeItem: ".$cartItem->getId().".", null, 'alliance_offers.log');
				$cart->removeItem($cartItem->getId());
				
				// To avoid loop.
				$this->_getSession()->setData('offersfivehundredfriends_product_deleted', true);
				
				Mage::log("checkOfferInCart.save before.", null, 'alliance_offers.log');
				$cart->save();
				return true;
			} catch (Exception $e) {
				Mage::log("checkOfferInCart.Exception: ".$e->getMessage().".", null, 'alliance_offers.log');
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
		return Mage::helper('offersfivehundredfriends');
	}
	private function _getSession()
	{
		return Mage::getSingleton('core/session');
	}
}
