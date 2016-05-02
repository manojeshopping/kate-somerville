<?php

class Alliance_TOA_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $_moduleConfig;
	const TOAcount = 2;
	
	
	public function getTOAcount()
	{
		return self::TOAcount;
	}
	
	public function getModuleEnabled($number)
	{
		$config = $this->_getModuleConfig();
		return $config[$number]['enabled'];
	}
	public function getMinimumAmount($number)
	{
		$config = $this->_getModuleConfig();
		return (float)$config[$number]['minimum_amount'];
	}
	public function getSku($number)
	{
		$config = $this->_getModuleConfig();
		return $config[$number]['sku'];
	}
	public function getMessageEnabled($number)
	{
		$config = $this->_getModuleConfig();
		return $config[$number]['enabled_message'];
	}
	public function getQualifiedMessaget($number)
	{
		$config = $this->_getModuleConfig();
		return $config[$number]['qualified'];
	}
	public function getUnqualifiedMessaget($number)
	{
		$config = $this->_getModuleConfig();
		return $config[$number]['unqualified'];
	}
	
	public function getApplicableSubtotal()
	{
		$quote = $this->_getCart()->getQuote();
		
		// Get all item collection.
		$items = Mage::getModel('sales/quote_item')->getCollection();
		$items->addFieldToFilter('parent_item_id', array('null' => true));
		$items->setQuote($quote);
		
		// Get subtotal excluding Giftcard product type.
		$subtotal = 0;
		foreach($items as $_item) {
			if($_item->getProduct()->getTypeId() != "giftcard") {
				$subtotal += $_item->getRowTotal();
			}
		}
		
		// Discount coupons.
		$totals = $quote->getTotals();
		if(isset($totals["discount"]) && $totals["discount"]->getValue()) {
			$subtotal -= abs($totals["discount"]->getValue());
		}
		
		return $subtotal;
	}
	
	public function checkProductInCart($productId)
	{
		if(empty($productId)) return false;
		
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		if(empty($quote)) return false;
		
		// Mage::log("helper.checkProductInCart: ".$productId, null, 'toa.log');
		$hasProductId = $quote->hasProductId($productId);
		// Mage::log("helper.checkProductInCart - hasProductId: ".$hasProductId, null, 'toa.log');
		if($hasProductId) {
			// Force Qty to 1.
			foreach($quote->getAllItems() as $item) {
				if ($item->getProductId() == $productId) {
					$itemQty = $item->getQty();
					// Mage::log("helper.checkProductInCart - itemQty: ".$itemQty, null, 'toa.log');
					if($itemQty > 1) {
						$cart = Mage::getSingleton('checkout/cart');
						$cartData = array($item->getId() => array('qty' => 1));
						$cartData = $cart->suggestItemsQty($cartData);
						
						$cart->updateItems($cartData);
						$cart->save();
					}
				}
			}
		}
		
		return $hasProductId;
	}
	
	private function _getModuleConfig()
	{
		if(empty($this->_moduleConfig)) {
			$this->_moduleConfig = array(
				array(
					'enabled' => Mage::getStoreConfig('alliance_toa/toa_configuration/enabled'),
					'minimum_amount' => Mage::getStoreConfig('alliance_toa/toa_configuration/minimum_amount'),
					'sku' => Mage::getStoreConfig('alliance_toa/toa_configuration/sku'),
					'enabled_message' => Mage::getStoreConfig('alliance_toa/toa_configuration/enabled_message'),
					'qualified' => Mage::getStoreConfig('alliance_toa/toa_configuration/qualified'),
					'unqualified' => Mage::getStoreConfig('alliance_toa/toa_configuration/unqualified'),
				),
				array(
					'enabled' => Mage::getStoreConfig('alliance_toa/toa_configuration_2/enabled'),
					'minimum_amount' => Mage::getStoreConfig('alliance_toa/toa_configuration_2/minimum_amount'),
					'sku' => Mage::getStoreConfig('alliance_toa/toa_configuration_2/sku'),
					'enabled_message' => Mage::getStoreConfig('alliance_toa/toa_configuration_2/enabled_message'),
					'qualified' => Mage::getStoreConfig('alliance_toa/toa_configuration_2/qualified'),
					'unqualified' => Mage::getStoreConfig('alliance_toa/toa_configuration_2/unqualified'),
				),
			);
		}
		
		return $this->_moduleConfig;
	}
	
	private function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
}

