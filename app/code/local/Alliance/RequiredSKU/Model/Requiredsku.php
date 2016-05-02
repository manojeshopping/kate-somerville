<?php


class Alliance_RequiredSKU_Model_Requiredsku extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('requiredsku/requiredsku');
	}
	
	
	public function checkOffer()
	{
		// Mage::log("checkOffersPage: checkOffer.", null, 'requiredsku.log');
		
		return $this->_getHelper()->checkRequiredSKUOffer();
	}
	
	public function addOffer($request)
	{
		// Mage::log("addOffer.", null, 'requiredsku.log');
		
		// Get data.
		$productId = $request['productId'];
		$helper = $this->_getHelper();
		
		// Check data. If no productId, then, the user does not selected any offer.
		// Mage::log("addOffer. productId: ".$productId, null, 'requiredsku.log');
		if(empty($productId) || ! is_numeric($productId)) {
			return true;
		}
		
		// Check Module availability.
		// Mage::log("addOffer. checkRequiredSKUOffer - Before", null, 'requiredsku.log');
		if(! $helper->checkRequiredSKUOffer()) {
			$this->_setErrorMessage("The travel item could not be added to the order.");
			return false;
		}
		// Mage::log("addOffer. checkRequiredSKUOffer - After", null, 'requiredsku.log');
		
		$product = $helper->initProduct($productId);
		if(! $product) {
			$this->_setErrorMessage("The product was not found.");
			return false;
		}
		
		// Add product to cart.
		try {
			// Mage::log("addOffer. Add Product - Before", null, 'requiredsku.log');
			$cart = Mage::getSingleton('checkout/cart');
			$cart->addProduct($product, array('qty' => 1));
			$cart->save();
			$this->_getSession()->setCartWasUpdated(true);
			// Mage::log("addOffer. Add Product - After", null, 'requiredsku.log');
		} catch (Exception $e) {
			Mage::log("addGiftAction. Exception: ".$e->getMessage().".", null, 'requiredsku.log');
			
			$this->_setErrorMessage("Error adding product to cart.");
			return false;
		}
		
		return true;
	}
	
	public function checkOfferInCart()
	{
		// Mage::log("checkOfferInCart - Model.", null, 'requiredsku.log');
		
		// Get cart and helper.
		$cart = Mage::getSingleton('checkout/cart');
		$helper = $this->_getHelper();
		// Mage::log("checkOfferInCart.Loaded cart and helper.", null, 'requiredsku.log');
		
		// Get item in cart.
		$cartItem = $helper->getItemInCart();
		if(! $cartItem) return true;
		// Mage::log("checkOfferInCart.cartItem: ".$cartItem->getId().".", null, 'requiredsku.log');
		
		// Check product quantity.
		$qty = $cartItem->getQty();
		// Mage::log("checkOfferInCart.qty: ".$qty.".", null, 'requiredsku.log');
		if($qty > 1) {
			// Update item.
			try {
				$cartData = array($cartItem->getId() => array('qty' => 1));
				$cartData = $cart->suggestItemsQty($cartData);
				// Mage::log("checkOfferInCart.cartData: ".print_r($cartData, 1).".", null, 'requiredsku.log');
				
				$cart->updateItems($cartData);
				$cart->save();
				return true;
			} catch (Exception $e) {
				Mage::log("checkOfferInCart.getMessage: ".$e->getMessage().".", null, 'requiredsku.log');
				return true;
			}
		}
		
		// Check if items was deleted.
		$deleted = $this->_getSession()->getData('requiredsku_product_deleted');
		$this->_getSession()->setData('requiredsku_product_deleted', false);
		// Mage::log("checkOfferInCart.deleted: ".($deleted ? 1 : 0).".", null, 'requiredsku.log');
		if($deleted) return true;
		
		// Check if can be applied.
		// Mage::log("checkOfferInCart: checkGiftOfChoice: ".($helper->checkGiftOfChoice(false) ? 1 : 0).".", null, 'requiredsku.log');
		if(! $helper->checkRequiredSKUOffer(false)) {
			// Remove item.
			try {
				// Mage::log("checkOfferInCart.removeItem: ".$cartItem->getId().".", null, 'requiredsku.log');
				$cart->removeItem($cartItem->getId());
				
				// To avoid loop.
				$this->_getSession()->setData('requiredsku_product_deleted', true);
				
				// Mage::log("checkOfferInCart.save before.", null, 'requiredsku.log');
				$cart->save();
				return true;
			} catch (Exception $e) {
				Mage::log("checkOfferInCart.Exception: ".$e->getMessage().".", null, 'requiredsku.log');
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
		return Mage::helper('requiredsku');
	}
	private function _getSession()
	{
		return Mage::getSingleton('core/session');
	}
}
