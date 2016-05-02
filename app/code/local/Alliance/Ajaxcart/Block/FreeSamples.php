<?php

class Alliance_Ajaxcart_Block_FreeSamples extends Mage_Core_Block_Template
{
	public function getProductCollection()
	{
		$freeSampleCategoryId = Mage::helper('ajaxcart')->getFreesampleCategoryId();
		$storeId = Mage::app()->getStore()->getData('store_id');
		
		$productCollection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*')
			->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
			->addStoreFilter($storeId)
			->addAttributeToFilter('category_id', $freeSampleCategoryId)
			->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
		;
		$productCollection->getSelect()->order('position', 'ASC');
		
		// Filter out of stock items.
		$productCollection->getSelect()->joinLeft(
			array('stock' => 'cataloginventory_stock_item'),
			"e.entity_id = stock.product_id",
			array('stock.is_in_stock')
		)->where('stock.is_in_stock = 1');
		
		
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
	
	public function freeSampleEnabled()
	{
		return Mage::helper('ajaxcart')->getFreesampleEnabled();
	}
	public function freeSampleTitle()
	{
		return Mage::helper('ajaxcart')->getFreesampleTitle();
	}
}

