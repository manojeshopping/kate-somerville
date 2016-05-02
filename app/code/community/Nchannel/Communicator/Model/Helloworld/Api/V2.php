<?php

class Nchannel_Communicator_Model_Helloworld_Api_V2 extends Nchannel_Communicator_Model_Helloworld_Api
{
	
	public function hello($msg) {
		$rtnString = "";
		$attributes = Mage::getSingleton('eav/config')
			->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection();

		$attributes->addStoreLabel(Mage::app()->getStore()->getId());

		// Loop over all attributes
		foreach ($attributes as $attr) {
			/* @var $attr Mage_Eav_Model_Entity_Attribute */
			// get the store label value
			$label = $attr->getStoreLabel() ? $attr->getStoreLabel() : $attr->getFrontendLabel();
			$rtnString = $rtnString . "Attribute: {$label}\n";

			// If it is an attribute with predefined values
			if ($attr->usesSource()) {

				// Get all option values ans labels
				$options = $attr->getSource()->getAllOptions();

				// Output all option labels and values
				foreach ($options as $option)
				{
					$rtnString = $rtnString . "    {$option['label']} (Value {$option['value']})\n";
				}
			}
			else
			{
				// Just for clarification of the debug code
				$rtnString = $rtnString . "    No select or multiselect attribute\n";
			}
		}
		
		return $rtnString;
	}
}