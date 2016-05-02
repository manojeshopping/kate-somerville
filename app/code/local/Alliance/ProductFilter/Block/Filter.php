<?php

class Alliance_ProductFilter_Block_Filter extends Mage_Core_Block_Template
{
	// Print only if the Module is enabled.
	protected function _toHtml()
	{
		if($this->_getHelper()->getModuleEnabled()) {
			return parent::_toHtml();
		}
		return '';
	}
	
	public function getFiltrableAttributes()
	{
		$attributeCollection = $this->_getHelper()->getAttributeCollection();
		
		$filtrableAttributes = array();
		foreach($attributeCollection as $_attribute) {
			$filtrableAttributes[$_attribute->getAttributeCode()] = array(
				'code' => $_attribute->getAttributeCode(),
				'title' => $_attribute->getFrontendLabel(),
				'options' => $_attribute->getSource()->getAllOptions(false),
			);
		}
		
		// Manually order for the filters.
		$filtrableAttributesOrdered = array();
		if(isset($filtrableAttributes['skin_concerns'])) $filtrableAttributesOrdered[] = $filtrableAttributes['skin_concerns'];
		foreach($filtrableAttributes as $_attribute) {
			if(strpos($_attribute['code'], 'benefits_') !== false) {
				$filtrableAttributesOrdered[] = $_attribute;
				unset($filtrableAttributes[$_attribute['code']]);
				continue;
			}
		}			
		if(isset($filtrableAttributes['skin_types'])) $filtrableAttributesOrdered[] = $filtrableAttributes['skin_types'];
		if(isset($filtrableAttributes['product_types'])) $filtrableAttributesOrdered[] = $filtrableAttributes['product_types'];
		if(isset($filtrableAttributes['ingredients_preferred'])) $filtrableAttributesOrdered[] = $filtrableAttributes['ingredients_preferred'];
		if(isset($filtrableAttributes['feel_finish'])) $filtrableAttributesOrdered[] = $filtrableAttributes['feel_finish'];	
		unset($filtrableAttributes['skin_concerns'], $filtrableAttributes['skin_types'], $filtrableAttributes['product_types'], $filtrableAttributes['ingredients_preferred'], $filtrableAttributes['feel_finish']);
		
		// Add the other filters.
		foreach($filtrableAttributes as $_attribute) {
			$filtrableAttributesOrdered[] = $_attribute;
		}
		
		
		return $filtrableAttributesOrdered;
	}
	
	public function getFilterData()
	{
		// Get all attributes.
		$attributeCollection = $this->_getHelper()->getAttributeCollection();
		
		// Get product collection from current layer.
		$productCollection = $this->_getLayer()->getCurrentCategory()->getProductCollection();
		foreach($attributeCollection as $_attribute) {
			$productCollection->joinAttribute($_attribute->getAttributeCode(), 'catalog_product/'.$_attribute->getAttributeCode(), 'entity_id', null, 'left');
		}
		
		$filterData = array();
		foreach($productCollection as $_product) {
			$filterData[$_product->getId()] = '';
			foreach($attributeCollection as $_attribute) {
				$attributeData = $_product->getData($_attribute->getAttributeCode());
				if(! empty($attributeData)) {
					if(! empty($filterData[$_product->getId()])) $filterData[$_product->getId()] .= ',';
					$filterData[$_product->getId()] .= $attributeData;
				}
			}
		}
		
		
		return $filterData;
	}
	
	
	protected function _getHelper()
	{
		return Mage::helper('alliance_productfilter');
	}
	protected function _getLayer()
    {
		$layer = Mage::registry('current_layer');
		if($layer) {
			return $layer;
		}
		return Mage::getSingleton('catalog/layer');
    }
}

