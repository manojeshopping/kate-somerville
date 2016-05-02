<?php
class Alliance_TOA_Model_Observer {
	
	public function addGiftProduct($observer)
	{
		// Mage::log("addGiftProduct: ".$observer->getEvent()->getName().".", null, 'toa.log');
		// Get cart.
		$cart = $observer->getCart();
		if(! $cart) {
			$cart = Mage::getSingleton('checkout/cart');
		}
		
		// Initalize helper.
		$helper = $this->_getHelper();
		$TOAcount = $helper->getTOAcount();
		$toa_product_removed = array();
		$cartAmount = $helper->getApplicableSubtotal();
		
		for($i = 0; $i < $TOAcount; $i++) {
			// Mage::log("addGiftProduct.TOA count: ".$i.".", null, 'toa.log');
			
			// Check module enabled.
			if(! $helper->getModuleEnabled($i)) continue;
			
			// Check if toa product removed.
			$toa_product_removed[$i] = $this->_getSession()->getData('toa_product_removed_'.$i);
			$this->_getSession()->setData('toa_product_removed_'.$i, false);
			// Mage::log("addGiftProduct.toa_product_removed[".$i."]: ".$toa_product_removed[$i].".", null, 'toa.log');
			if($toa_product_removed[$i]) continue;
			
			// Check cart amount.
			$minimumAmount = $helper->getMinimumAmount($i);
			// Mage::log("cartAmount[".$i."]: ".$cartAmount." - minimumAmount[".$i."]: ".$minimumAmount.".", null, 'toa.log');
			if($cartAmount < $minimumAmount) {
				// Check if TOA product is loaded in cart.
				// Mage::log("addGiftProduct.RemoveGift[".$i."].", null, 'toa.log');
				$product = $this->_getTOAProduct($i);
				$item = $cart->getQuote()->getItemByProduct($product);
				
				if(! empty($item)) {
					try {
						// Mage::log("addGiftProduct.removeItem[".$i."]: ".$item->getId().".", null, 'toa.log');
						$cart->removeItem($item->getId());
						// Mage::log("addGiftProduct.save before[".$i."].", null, 'toa.log');
						$cart->save();
						$this->_getSession()->setCartWasUpdated(true);
					} catch (Exception $e) {
						Mage::log("Exception[".$i."]: ".$e->getMessage().".", null, 'toa.log');
					}
				}
				
				continue;
			}
			
			// Check TOA product.
			// Mage::log("_loadTOAProduct[".$i."].", null, 'toa.log');
			$product = $this->_loadTOAProduct($i);
			if(! $product) continue;
			// Mage::log("product[".$i."]: ".$product->getId()." - name: ".$product->getName().".", null, 'toa.log');
			
			// Add TOA product to cart.
			// Mage::log("addProduct[".$i."].", null, 'toa.log');
			try {
				$cart->addProduct($product, array('qty' => 1));
				$cart->save();
				$this->_getSession()->setCartWasUpdated(true);
			} catch (Exception $e) {
				Mage::log("Exception[".$i."]: ".$e->getMessage().".", null, 'toa.log');
			}
		}
		
		
		// Mage::log("Done.", null, 'toa.log');
		return true;
	}
	
	public function removeGiftProduct($observer)
	{
		// Mage::log("removeGiftProduct: ".$observer->getEvent()->getName().".", null, 'toa.log');
		
		
		// Get item removed.
		$itemRemoved = $observer->getQuoteItem();
		$itemSku = $itemRemoved->getSku();
		// Mage::log("removeGiftProduct.itemRemoved SKU: ".$itemSku.".", null, 'toa.log');
		
		
		$helper = $this->_getHelper();
		$TOAcount = $helper->getTOAcount();
		// Mage::log("removeGiftProduct.TOAcount: ".$TOAcount.".", null, 'toa.log');
		
		for($i = 0; $i < $TOAcount; $i++) {
			$toaSku = $helper->getSku($i);
			
			// Check if items is TOA
			// Mage::log("removeGiftProduct[".$i."]: ".$itemSku.' == '.$toaSku.".", null, 'toa.log');
			if($itemSku == $toaSku) {
				$this->_getSession()->setData('toa_product_removed_'.$i, true);
			} else {
				$this->_getSession()->setData('toa_product_removed_'.$i, false);
			}
		}
		
		
		// Mage::log("removeGiftProduct: Done.", null, 'toa.log');
		return true;
	}
	
	
	protected function _getTOAProduct($number)
	{
		$sku = $this->_getHelper()->getSku($number);
		if(empty($sku)) return false;
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		
		return $product;
	}
	
	protected function _loadTOAProduct($number)
	{
		// Get TOA Product.
		$product = $this->_getTOAProduct($number);
		// Mage::log("_loadTOAProduct: ".$product->getId(), null, 'toa.log');
		
		// Check product availability.
		if(! $product) return false;
		if(! $product->isSaleable()) return false;
		
		// Check if TOA already added.
		// Mage::log("checkProductInCart.", null, 'toa.log');
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
		// Mage::log("stockData: ".print_r($stockData, 1).".", null, 'toa.log');
		
		return $product;
	}
	
	protected function _getHelper()
	{
		return Mage::helper('toa');
	}
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
}


