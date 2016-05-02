<?php
/**
 * IOI Discount item data model. Contains
 * data for individual records located
 * within the discount data json.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Ioi_Discountdata_Item extends Varien_Object{
	/**
	 * Keys to pull the item data
	 * out of the IOI cookie data
	 */
	const KEY_PRODUCT_ID		=	'id';
	const KEY_DISCOUNT_VALUE	=	'd';

	/**
	 * Flag indicating whether or not the item has a discount.
	 * Start this value at -1 so we can check whether
	 * or not a discount has been checked for.
	 * 
	 * @var boolean
	 */
	protected $_hasDiscount	=	-1;
	
	
	/**
	 * Return whether or not this item has a discount
	 * 
	 * @return boolean
	 */
	public function hasDiscount(){
		if($this->_hasDiscount	==	-1){
			$this->_hasDiscount	=	( ($this->getDiscountAmount() != 0) ? TRUE : FALSE);
		}
		return $this->_hasDiscount;
	}
}