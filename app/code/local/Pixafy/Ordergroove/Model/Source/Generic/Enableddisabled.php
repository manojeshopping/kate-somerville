<?php
/**
 * Configuration source class. Returns an 
 * array with two values: Enabled and Disabled
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Source_Generic_Enableddisabled{
	/**
	 * Return the array of options
	 * 
	 * @return array
	 */
	public function toOptionArray()
	{
		$options	=	array(
			array(
				'value'	=>	Pixafy_Ordergroove_Helper_Constants::FUNCTIONALITY_CHECK_ENABLED,
				'label'	=>	Mage::helper('ordergroove')->__("Use Default Logic")
			),
			array(
				'value'	=>	Pixafy_Ordergroove_Helper_Constants::FUNCTIONALITY_CHECK_DISABLED,
				'label'	=>	Mage::helper('ordergroove')->__("Disabled")
			)
		);
		
		return $options;
	}
}
?>