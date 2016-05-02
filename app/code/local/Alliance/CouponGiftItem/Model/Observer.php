<?php
class Alliance_CouponGiftItem_Model_Observer {
	public function addCouponCodeGiftProduct($observer)
	{
		Mage::log("addGiftProduct: ".$observer->getEvent()->getName().".", null, 'CouponCodeGift.log');
		// Get cart.
		$cart = $observer->getCart();
		
		// Initalize helper.
		$helper = $this->_getHelper();

		// Check if gift product removed.
		$coupon_gift_sku_removed = $this->_getSession()->getData('coupon_gift_sku_removed');
		$this->_getSession()->setData('coupon_gift_sku_removed', false);
		if($coupon_gift_sku_removed) return true;

		$coupon_code = $this->_getQuote()->getCouponCode();
		
		//if(!$coupon_code) return false;		
		$sku = $this->_getHelper()->getGiftSkuByCode($coupon_code);
		$product = $this->_getGiftSkuProduct($sku);
		
		if(! $coupon_code){
			$item = $cart->getQuote()->getItemByProduct($product);
			if(! empty($item)) {
				try {
					// Mage::log("addGiftProduct.removeItem: ".$item->getId().".", null, 'CouponCodeGift.log');
					$cart->removeItem($item->getId());
					// Mage::log("addGiftProduct.save before.", null, 'CouponCodeGift.log');
					$cart->save();
					$this->_getSession()->setCartWasUpdated(true);
				} catch (Exception $e) {
					Mage::log("Exception: ".$e->getMessage().".", null, 'CouponCodeGift.log');
				}
			}
		return true;
		}
		
		$product = $this->_loadGiftSkuProduct($product);
		if(! $product) return true;
		
		// Add Gift product to cart.
		// Mage::log("addProduct.", null, 'CouponCodeGift.log');
		try {
			$cart->addProduct($product, array('qty' => 1));
			$cart->save();
			$this->_getSession()->setCartWasUpdated(true);
			Mage::getSingleton('checkout/session')->setCouponGiftSku($sku);
		} catch (Exception $e) {
			Mage::log("Exception: ".$e->getMessage().".", null, 'CouponCodeGift.log');
		}
		// Mage::log("Done.", null, 'CouponCodeGift.log');
		return true;
	}

	
	public function removeCouponCodeGiftProduct($observer)
	{
		Mage::log('removeCouponCodeGiftProduct v1 ', null, 'CouponCodeGift.log');
		$giftSku = Mage::getSingleton('checkout/session')->getCouponGiftSku();	
		$couponCode = Mage::getSingleton('checkout/session')->getQuote()->getCouponCode();
		
		if($couponCode == '' && $giftSku != '')  {
			Mage::getSingleton('checkout/session')->setCouponGiftSku('');
			$cartHelper = Mage::helper('checkout/cart');
			$items = $cartHelper->getCart()->getItems();
			foreach ($items as $item) {
				if($item->getSku() == $giftSku) {
			    	$cartHelper->getCart()->removeItem($item->getId())->save();
			    	break;
			    }
			}
		}
	}
	
	
	protected function _getGiftSkuProduct($value)
	{
		$sku = $value;
		if(empty($sku)) return false;
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		
		return $product;
	}
	
	protected function _loadGiftSkuProduct($value)
	{
		// Get Gift Product.
		$product = $value;
		
		// Check product availability.
		if(! $product) return false;
		if(! $product->isSaleable()) return false;
		
		// Check if Gift already added.
		//Mage::log("checkProductInCart.", null, 'CouponCodeGift.log');
		if($this->_getHelper()->checkProductInCart($product->getId())) return false;
		
		// Check product stock.
		$stockData = $product->getStockData();
		if(! $stockData) {
			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
			
			$product = $product->load($product->getId());
			$stockData = array(
				'manage_stock' => $stock->getManageStock(),
				'is_in_stock' => $stock->getIsInStock(),
				'qty' => $stock->getQty(),
			);
			$product->setStockData($stockData);
			
		}
		return $product;
	}
	
	protected function _getHelper()
	{
		return Mage::helper('coupongiftitem');
	}
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	
	protected function _getQuote()
	{
		return Mage::getSingleton('checkout/session')->getQuote();
	}
}


