<?php

class Alliance_RequiredSKU_Block_Requiredsku extends Mage_Core_Block_Template
{
	private $_catalogResource;
	private $_sizeAttribute;
	
	
	protected function _construct()
	{
		$this->setTemplate('alliance/requiredsku/requiredsku.phtml');
	}
	
	
	public function getProductSize($product)
	{
		$catalogResource = $this->_getCatalogResource();
		$sizeAttribute = $this->_getSizeAttribute();
		
		// Get current store id.
		$storeId = Mage::app()->getStore()->getStoreId();
		
		$productSizeId = $catalogResource->getAttributeRawValue($product->getId(), 'size', $storeId);
		$productSize = $sizeAttribute->getSource()->getOptionText($productSizeId);
		
		return $productSize;
	}
	
	
	public function getConfigTitle()
	{
		return $this->_getHelper()->getTitle();
	}
	public function getConfigDescription()
	{
		return $this->_getHelper()->getDescription();
	}
	public function getConfigEnabled()
	{
		return $this->_getHelper()->getModuleEnabled();
	}
	
	public function checkProductsInCart()
	{
		return $this->_getHelper()->checkProductsInCart();
	}
	
	public function getProductCollection()
	{
		return $this->_getHelper()->getProductCollection();
	}
	
	public function getActionPostUrl()
	{
		$url = $this->getUrl('requiredsku/index/addRequiredsku');
		return $url;
	}
	
	
	private function _getHelper()
	{
		return Mage::helper('requiredsku');
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

