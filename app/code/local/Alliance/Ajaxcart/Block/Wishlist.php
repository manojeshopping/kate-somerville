<?php

class Alliance_Ajaxcart_Block_Wishlist extends Mage_Core_Block_Template
{
	private $_ajaxcartHelper;
	private $_catalogResource;
	private $_sizeAttribute;
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getProductCollection()
	{
		$productCollection = Mage::helper('wishlist')->getWishlistItemCollection();
		$productCollection->getSelect()->order("wishlist_item_id DESC");
		
		return $productCollection;
	}
	
	public function getSummaryCount()
	{
		if ($this->getData('summary_qty')) {
			return $this->getData('summary_qty');
		}
		return Mage::getSingleton('checkout/cart')->getSummaryQty();
	}
	
	public function checkProductInCart($productId)
	{
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		return $quote->hasProductId($productId);
	}
	
	public function wishlistEnabled()
	{
		return $this->_getAjaxcartHelper()->getWishlistShowCart();
	}
	
	public function getWishlistUrl()
	{
		return Mage::helper('wishlist')->getListUrl();
	}
	
	public function getOptions($item)
	{
		$helper = $this->_getAjaxcartHelper();
		
		$attributes = $item->getOptionByCode('attributes');
		if(empty($attributes)) return false;
		
		$attributesValue = $attributes->getValue();
		$attributesValue = unserialize($attributesValue);
		$options = array();
		foreach($attributesValue as $attributeId => $attributeValue) {
			$options[$attributeId] = $helper->getOptionData($attributeId, $attributeValue);
		}
		
		return $options;
	}
	
	public function isNotEditable($product)
	{
		$ajaxcartHelper = $this->_getAjaxcartHelper();
		$ajaxcartHelper->isNotEditable($product);
	}
	
	public function getProductSize($product)
	{
		$catalogResource = $this->_getCatalogResource();
		$sizeAttribute = $this->_getSizeAttribute();
		$productData = $product->getProduct();
		
		// Get current store id.
		$storeId = Mage::app()->getStore()->getStoreId();
		
		$productSizeId = $catalogResource->getAttributeRawValue($productData->getId(), 'size', $storeId);
		$productSize = $sizeAttribute->getSource()->getOptionText($productSizeId);
		
		return $productSize;
	}
	
	public function getProductPrice($product)
	{	
		if($product->getTypeId() == "configurable") {
			$block = Mage::app()->getLayout()->createBlock('catalog/product_view_type_configurable');
			$block->setProduct($product);
			$config = json_decode($block->getJsonConfig(), true);
			$price = $config['basePrice'];
		} else {
			$price = $product->getPrice();
		}
		
		return $price;
	}
	
	public function getPriceFormated($price)
	{
		$coreHelper = $this->helper('core');
		return $coreHelper->currency($price, true, false);
	}
	
	public function getProductImage($product, $width, $height)
	{
		$productData = $product->getProduct();
		$image = $this->helper('catalog/image')->init($productData, 'thumbnail')->resize($width, $height);
		
		return $image;
	}
	
	
	private function _getAjaxcartHelper()
	{
		if(empty($this->_ajaxcartHelper)) {
			$this->_ajaxcartHelper = Mage::helper('ajaxcart');
		}
		
		return $this->_ajaxcartHelper;
	}
	
	private function _getCatalogResource()
	{
		if(empty($this->_catalogResource)) {
			$this->_catalogResource = Mage::getResourceModel('catalog/product');
		}
		
		return $this->_catalogResource;
	}
	
	private function _getSizeAttribute()
	{
		if(empty($this->_sizeAttribute)) {
			$this->_sizeAttribute = $this->_getCatalogResource()->getAttribute('size');
		}
		
		return $this->_sizeAttribute;
	}
}

