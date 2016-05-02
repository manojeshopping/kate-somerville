<?php

class Alliance_OffersFiveHundredFriends_Model_System_Config_Source_Category
{
	public function toOptionArray($addEmpty = true)
	{
		$tree = Mage::getResourceModel('catalog/category_tree');

		$collection = Mage::getResourceModel('catalog/category_collection');

		$collection->addAttributeToSelect('name')
			->addRootLevelFilter()
			->load()
		;

		$options = array();

		if ($addEmpty) {
			$options[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Please Select a Category --'),
				'value' => ''
			);
		}
		foreach ($collection as $category) {
			$options[] = array(
				'label' => $category->getName(),
				'value' => $category->getId()
			);
			
			$collectionSub = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToSelect('name')
				->addFieldToFilter('parent_id', $category->getId())
			;
			foreach ($collectionSub as $categorySub) {
				$options[] = array(
					'label' => "----- ".$categorySub->getName(),
					'value' => $categorySub->getId()
				);
			}
		}

		return $options;
	}
}


