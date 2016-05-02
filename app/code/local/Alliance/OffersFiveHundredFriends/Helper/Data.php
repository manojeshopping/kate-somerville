<?php

class Alliance_OffersFiveHundredFriends_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $_moduleConfig;
	private $_productCollection;
	private $_productIds;
	
	
	public function checkOffersFiveHundredFriends($checProductInCart = true)
	{
		// Get customer.
		$customer = Mage::helper('customer');
		
		// Check module availability.
		// Mage::log("checkOffersFiveHundredFriends.getModuleEnabled: ".$this->getModuleEnabled().".", null, 'alliance_offers.log');
		if(! $this->getModuleEnabled()) return false;
		
		// Check 500Frieds Rewards.
		$redemptionModel = $this->_getRedemptionModel();
		if(! $redemptionModel) return false;
		$redemptionModel->loadCurrentQuote();
		// Mage::log("checkOffersFiveHundredFriends.checkOffersApplied: ".$redemptionModel->checkOffersApplied().".", null, 'alliance_offers.log');
		if(! $redemptionModel->checkOffersApplied()) return false;
		
		// Check user logged.
		// Mage::log("checkOffersFiveHundredFriends.isLoggedIn: ".(Mage::helper('customer')->isLoggedIn() ? 1 : 0).".", null, 'alliance_offers.log');
		$customerGroup = $this->getCustomerGroup();
		if(! $customer->isLoggedIn() && ! in_array(-1, $customerGroup)) return false;
		
		// Check user group.
		// Mage::log("checkOffersFiveHundredFriends.userGroup configured: ".print_r($this->getCustomerGroup(), 1)." - ".$customer->getCustomer()->getGroupId().".", null, 'alliance_offers.log');
		if($customer->isLoggedIn() && ! in_array($customer->getCustomer()->getGroupId(), $customerGroup)) return false;
		
		// Check Minimum Order Amount.
		$cartAmount = $this->getApplicableSubtotal();
		$minimumAmount = $this->getMinimumAmount();
		// Mage::log("checkOffersFiveHundredFriends: ".$cartAmount." < ".$minimumAmount.".", null, 'alliance_offers.log');
		if($cartAmount == 0 || $cartAmount < $minimumAmount) return false;
		
		// Get product collection.
		$productCollection = $this->getProductCollection();
		// Mage::log("checkOffersFiveHundredFriends.productCollection: ".$productCollection->count().".", null, 'alliance_offers.log');
		if(empty($productCollection)) return false;
		
		// Check products in cart.
		if($checProductInCart) {
			$productsInCart = $this->checkProductsInCart();
			// Mage::log("checkOffersFiveHundredFriends.productsInCart: ".($productsInCart ? 1 : 0).".", null, 'alliance_offers.log');
			if($productsInCart) return false;
		}
		
		
		return true;
	}
	
	public function getProductCollection()
	{
		if(empty($this->_productCollection)) {
			$categoryId = $this->getCategoryId();
			$storeId = Mage::app()->getStore()->getData('store_id');
			
			$productCollection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
				->addStoreFilter($storeId)
				->addAttributeToFilter('category_id', $categoryId)
				->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			;
			$productCollection->getSelect()->order('position', 'ASC');
			
			// Filter out of stock items.
			$productCollection->getSelect()->joinLeft(
				array('stock' => 'cataloginventory_stock_item'),
				"e.entity_id = stock.product_id",
				array('stock.is_in_stock')
			)->where('stock.is_in_stock = 1');
			
			$this->_productCollection = $productCollection;
		}
		
		return $this->_productCollection;
	}
	
	public function getProductIds()
	{
		if(empty($this->_productIds)) {
			$productCollection = $this->getProductCollection();
			if(empty($productCollection)) return false;
			$this->_productIds = $productCollection->getAllIds();
		}
		
		return $this->_productIds;
	}
	
	public function checkProductsInCart()
	{
		$productCollection = $this->getProductCollection();
		if(empty($productCollection)) return false;
		
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		if(empty($quote)) return false;
		
		foreach($productCollection as $oneProduct) {
			if($quote->hasProductId($oneProduct->getId())) return true;
		}
		
		return false;
	}
	
	public function getItemInCart()
	{
		// Get product collection.
		$productIds = $this->getProductIds();
		
		// Get quote.
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		if(empty($quote)) return false;
		
		// Get item in cart.
		$items = $quote->getItemsCollection();
		foreach($items as $_item) {
			if(in_array($_item->getProductId(), $productIds)) return $_item;
		}
		
		
		return false;
	}
	
	public function initProduct($productId)
	{
		// Check product Id
		if(empty($productId)) return false;
		
		// Load product.
		$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId)
		;
		
		// Check if product is loaded.
		if (! $product->getId()) return false;
		
		// Check if product is a 500Friends.
		$productCollection = $this->getProductCollection();
		foreach($productCollection as $_product) {
			if($_product->getId() == $productId) return $product;
		}
		
		return false;
	}
	
	public function getModuleEnabled()
	{
		$config = $this->_getModuleConfig();
		return $config['enabled'];
	}
	public function getTitle()
	{
		$config = $this->_getModuleConfig();
		return $config['title'];
	}
	public function getDescription()
	{
		$config = $this->_getModuleConfig();
		return $config['description'];
	}
	public function getCustomerGroup()
	{
		$config = $this->_getModuleConfig();
		return explode(',', $config['customer_group']);
	}
	public function getRuleName()
	{
		$config = $this->_getModuleConfig();
		return $config['rule_name'];
	}
	public function getMinimumAmount()
	{
		$config = $this->_getModuleConfig();
		return (float)$config['minimum_amount'];
	}
	public function getCategoryId()
	{
		$config = $this->_getModuleConfig();
		return $config['category_id'];
	}
	
	public function getApplicableSubtotal()
	{
		// Get all item collection.
		$items = Mage::getModel('sales/quote_item')->getCollection();
		$items->addFieldToFilter('parent_item_id', array('null' => true));
		$items->setQuote($this->_getCart()->getQuote());
		
		// Get subtotal excluding Giftcard product type.
		$subtotal = 0;
		foreach($items as $_item) {
			if($_item->getProduct()->getTypeId() != "giftcard") {
				$subtotal += $_item->getRowTotal();
			}
		}
		
		return $subtotal;
	}
	
	
	protected function _getRedemptionModel()
	{
		return Mage::getModel('alliance_fivehundredfriends/redemption');
	}
	private function _getModuleConfig()
	{
		if(empty($this->_moduleConfig)) {
			$this->_moduleConfig = array(
				'enabled' => Mage::getStoreConfig('alliance_offers/offersfivehundredfriends_configuration/enabled'),
				'title' => Mage::getStoreConfig('alliance_offers/offersfivehundredfriends_configuration/title'),
				'description' => Mage::getStoreConfig('alliance_offers/offersfivehundredfriends_configuration/description'),
				'customer_group' => Mage::getStoreConfig('alliance_offers/offersfivehundredfriends_configuration/customer_group'),
				'minimum_amount' => Mage::getStoreConfig('alliance_offers/offersfivehundredfriends_configuration/minimum_amount'),
				'category_id' => Mage::getStoreConfig('alliance_offers/offersfivehundredfriends_configuration/category_id'),
				'rule_name' => Mage::getStoreConfig('alliance_offers/offersfivehundredfriends_configuration/rule_name'),
			);
		}
		
		return $this->_moduleConfig;
	}

	private function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
}

