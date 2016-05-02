<?php

class Alliance_ProductFilter_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_moduleConfig;
	
	
	public function getModuleConfig()
	{
		if(empty($this->_moduleConfig)) {
			$this->_moduleConfig = array(
				'enabled' => Mage::getStoreConfig('alliance_productfilter/productfiltert_config/enabled'),
				'attributes' => Mage::getStoreConfig('alliance_productfilter/productfiltert_config/attributes'),
			);
		}
		
		return $this->_moduleConfig;
	}
	public function getModuleEnabled()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['enabled'];
	}
	public function getConfiguredAttributes()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['attributes'];
	}

	
	public function getAttributeCollection()
	{
		$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
			->addVisibleFilter()
			->addFieldToFilter('main_table.attribute_id', array('in' => explode(',', $this->getConfiguredAttributes())))
		;
		
		return $attributeCollection;
	}
}

