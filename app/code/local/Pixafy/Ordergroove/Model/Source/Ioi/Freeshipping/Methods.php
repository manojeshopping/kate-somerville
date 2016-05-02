<?php
/**
 * IOI free shipping methods system config source class.
 * Presents a list of shipping methods that the free
 * shipping will apply to for IOI.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Source_Ioi_Freeshipping_Methods extends Pixafy_Ordergroove_Model_Source_Order_Shippingmethod{
	/**
	 * Return list of shipping methods
	 * 
	 * @return array
	 */
	public function toOptionArray()
	{
		return parent::toOptionArray();
		$sets	=	Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId());
		$options = array();
		foreach($sets as $set){
			$options[] = array(
				'value' => $set->getAttributeSetId(),
				'label' => $set->getAttributeSetName()
			);
		}
		return $options;
	}
}
?>
