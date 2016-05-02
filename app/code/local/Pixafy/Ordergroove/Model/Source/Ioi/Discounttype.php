<?php
/**
 * Configuration source class. Returns an array of
 * discount types that are used when calculating
 * Initial Order Incentive discounts
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Source_Ioi_Discounttype{
	/**
	 * Return the array of discount types
	 * 
	 * @return array
	 */
	public function toOptionArray()
	{
		$options	=	array(
			array(
				'value'	=>	Pixafy_Ordergroove_Model_Ioi_Discountdata::IOI_DISCOUNT_TYPE_FIXED_AMOUNT,
				'label'	=>	'Fixed Amount'
			),
			array(
				'value'	=>	Pixafy_Ordergroove_Model_Ioi_Discountdata::IOI_DISCOUNT_TYPE_PERCENTAGE,
				'label'	=>	'Percentage'
			)
		);
		
		return $options;
	}
}
?>
