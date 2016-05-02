<?php

class Alliance_ProductFilter_Model_System_Config_Source_Attributes
{
	public function toOptionArray()
	{
		// Get attribute collection.
		$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
			->addVisibleFilter()
			->addFieldToFilter('is_user_defined', 1)
			->addFieldToFilter('frontend_input', "multiselect")
		;
		
		// Add options.
		$options = array();
		foreach($attributeCollection as $_attribute) {
			$options[] = array(
				'label' => $_attribute->getFrontendLabel(),
				'value' => $_attribute->getId()
			);
		}
		
		return $options;
	}
}


