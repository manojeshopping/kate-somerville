<?php

class Alliance_FreeCleanser_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $_moduleConfig;
	private $_productCollection;
	private $_productIds;
	
	
	public function checkFreeCleanser($checProductInCart = true)
	{
		// Get customer.
		$customer = Mage::helper('customer');
		
		// Check module availability.
		// Mage::log("checkFreeCleanser.getModuleEnabled: ".$this->getModuleEnabled().".", null, 'freecleanser.log');
		if(! $this->getModuleEnabled()) return false;
		
		// Check user logged.
		// Mage::log("checkFreeCleanser.isLoggedIn: ".(Mage::helper('customer')->isLoggedIn() ? 1 : 0).".", null, 'giftofchoice.log');
		$customerGroup = $this->getCustomerGroup();
		if(! $customer->isLoggedIn() && ! in_array(-1, $customerGroup)) return false;
		
		// Check user group.
		// Mage::log("checkFreeCleanser.userGroup configured: ".print_r($this->getCustomerGroup(), 1)." - ".$customer->getCustomer()->getGroupId().".", null, 'giftofchoice.log');
		if($customer->isLoggedIn() && ! in_array($customer->getCustomer()->getGroupId(), $customerGroup)) return false;
		
		// Check Rule name.
		$ruleApplied = $this->checkRuleName();
		// Mage::log("checkFreeCleanser.ruleApplied: ".($ruleApplied ? 1 : 0).".", null, 'freecleanser.log');
		if(! $ruleApplied) return false;
		
		// Check cart amout.
		$cartAmount = $this->getApplicableSubtotal();
		// Mage::log("checkFreeCleanser.cartAmount: ".$cartAmount.".", null, 'freecleanser.log');
		if($cartAmount == 0 || $cartAmount <= 0) return false;
		
		// Get product collection.
		$productCollection = $this->getProductCollection();
		// Mage::log("checkFreeCleanser.productCollection: ".$productCollection->count().".", null, 'freecleanser.log');
		if(empty($productCollection)) return false;
		
		// Check products in cart.
		if($checProductInCart) {
			$productsInCart = $this->checkProductsInCart();
			// Mage::log("checkFreeCleanser.productsInCart: ".($productsInCart ? 1 : 0).".", null, 'freecleanser.log');
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
		
		// Check if product is a Gift.
		$productCollection = $this->getProductCollection();
		foreach($productCollection as $_product) {
			if($_product->getId() == $productId) return $product;
		}
		
		return false;
	}
	
	public function checkRuleName()
	{
		// Get Website Id
		$websiteId = Mage::app()->getWebsite()->getId();
		
		// Get rule name in model configuration.
		$ruleName = $this->getRuleName();
		
		// Get Rule Ids from qute.
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$ruleIds = $quote->getAppliedRuleIds();
		// Mage::log("checkRuleName: ".$ruleIds." - ruleName: ".$ruleName.".", null, 'freecleanser.log');
		$ruleIdsArray = explode(',', $ruleIds);
		$ruleApplied = false;
		foreach($ruleIdsArray as $_ruleId) {
			// Get Rule name and check.
			$rule = Mage::getModel('salesrule/rule')->load($_ruleId);
			$rule->setWebsiteIds($websiteId);
			if($ruleName == $rule->getName()) {
				$ruleApplied = true;
				break;
			}
		}
		
		return $ruleApplied;
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
	

	private function _getModuleConfig()
	{
		if(empty($this->_moduleConfig)) {
			$this->_moduleConfig = array(
				'enabled' => Mage::getStoreConfig('alliance_offers/freecleanser_configuration/enabled'),
				'title' => Mage::getStoreConfig('alliance_offers/freecleanser_configuration/title'),
				'description' => Mage::getStoreConfig('alliance_offers/freecleanser_configuration/description'),
				'customer_group' => Mage::getStoreConfig('alliance_offers/freecleanser_configuration/customer_group'),
				'rule_name' => Mage::getStoreConfig('alliance_offers/freecleanser_configuration/rule_name'),
				'category_id' => Mage::getStoreConfig('alliance_offers/freecleanser_configuration/category_id'),
			);
		}
		
		return $this->_moduleConfig;
	}

	private function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
}

