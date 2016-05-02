<?php
/**
 * IOI Discount data model. Contains the data
 * that is set in the cookie and functions to
 * determine what type of IOI promotion exists.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Ioi_Discountdata extends Varien_Object{
	/**
	 * Initial order incentive types
	 */
	const IOI_TYPE_ORDER_LEVEL	=	'order_level';
	const IOI_TYPE_ITEM_LEVEL	=	'item_level';
	
	/**
	 * Different discount types used in the Initial Order Incentive
	 */
	const IOI_DISCOUNT_TYPE_FIXED_AMOUNT	=	'fixed_amount';
	const IOI_DISCOUNT_TYPE_PERCENTAGE		=	'percentage';
	
	/**
	 * The current incentive type.
	 * 
	 * @var string
	 */
	protected $_incentiveType;
	
	/**
	 * The current discount amount for order level incentives
	 * 
	 * @var double
	 */
	protected $_orderIncentiveAmount;
	
	/**
	 * The discount type, either percentage or
	 * fixed amount. This is a global variable
	 * that is set in the System Configuration
	 * 
	 * @var string
	 */
	protected $_discountType;
	
	/**
	 * An array of items that belong
	 * to the item level discounts
	 * 
	 * @param array | Pixafy_Ordergroove_Model_Ioi_Discountdata_Item
	 */
	protected $_discountItems	=	array();
	
	/**
	 * Constructor. Set the discount type
	 */
	public function _construct(){
		$this->_loadDiscountType();
	}
	
	/**
	 * Set the current incentive type to order level
	 */
	public function setDiscountAsOrderLevel(){
		$this->_incentiveType	=	self::IOI_TYPE_ORDER_LEVEL;
	}
	
	/**
	 * Set the current incentive type to item level
	 */
	public function setDiscountAsItemLevel(){
		$this->_incentiveType	=	self::IOI_TYPE_ITEM_LEVEL;
	}
	
	/**
	 * Return whether or not the current discount type is order level.
	 * 
	 * @return boolean
	 */
	public function isOrderIncentive(){
		return ($this->_incentiveType	==	self::IOI_TYPE_ORDER_LEVEL ? TRUE : FALSE);
	}
	
	/**
	 * Return whether or not the current discount type is item level.
	 * 
	 * @return boolean
	 */
	public function isItemIncentive(){
		return ($this->_incentiveType	==	self::IOI_TYPE_ITEM_LEVEL ? TRUE : FALSE);
	}
	
	/**
	 * Set the order incentive amount.
	 * 
	 * @param double $amount
	 */
	public function setOrderIncentiveAmount($amount){
		$this->_orderIncentiveAmount	=	$amount;
	}
	
	/**
	 * Get the current order incentive amount
	 * 
	 * @return double
	 */
	public function getOrderIncentiveAmount(){
		return $this->_orderIncentiveAmount;
	}
	
	/**
	 * Load the discount type from the system config
	 */
	protected function _loadDiscountType(){
		$this->_discountType	=	Mage::helper('ordergroove/config')->getIoiDiscountType();
	}
	
	/**
	 * Return whether or not the discount is a percentage discount
	 * 
	 * @return boolean
	 */
	public function isPercentageDiscount(){
		return ($this->getDiscountType() == self::IOI_DISCOUNT_TYPE_PERCENTAGE ? TRUE : FALSE);
	}
	
	/**
	 * Return whether or not the discount is a fixed amount
	 * 
	 * @return boolean
	 */
	public function isFixedAmountDiscount(){
		return ($this->getDiscountType() == self::IOI_DISCOUNT_TYPE_FIXED_AMOUNT ? TRUE : FALSE);
	}
	
	/**
	 * Return the current discount type.
	 * 
	 * @return string
	 */
	public function getDiscountType(){
		return $this->_discountType;
	}
	
	/**
	 * Create the incentive item models and add 
	 * them to the array of items for this class.
	 * 
	 * @param array $items
	 */
	public function setIncentiveItems($items){
		foreach($items as $item){
			if(array_key_exists(Pixafy_Ordergroove_Model_Ioi_Discountdata_Item::KEY_PRODUCT_ID, $item)){
				$discountItem	=	$this->getDiscountItemModel();
				$discountItem->setId($item[Pixafy_Ordergroove_Model_Ioi_Discountdata_Item::KEY_PRODUCT_ID]);
				if(array_key_exists(Pixafy_Ordergroove_Model_Ioi_Discountdata_Item::KEY_DISCOUNT_VALUE, $item)){
					$discountItem->setDiscountAmount($item[Pixafy_Ordergroove_Model_Ioi_Discountdata_Item::KEY_DISCOUNT_VALUE]);
				}
				else{
					$discountItem->setDiscountAmount(0);
				}
				
				$this->_addDiscountItem($discountItem);
			}
		}
	}
	
	/**
	 * Return the discount item model
	 * 
	 * @return Pixafy_Ordergroove_Model_Ioi_Discountdata_Item
	 */
	public function getDiscountItemModel(){
		return Mage::getModel('ordergroove/ioi_discountdata_item');
	}
	
	/**
	 * Add a discount item to the array of items
	 * 
	 * @param Pixafy_Ordergroove_Model_Ioi_Discountdata_Item $item
	 */
	protected function _addDiscountItem($item){
		$items					=	$this->getIncentiveItems();
		$items[]				=	$item;
		$this->_discountItems	=	$items;
	}
	
	/**
	 * Return the array of discount items for an item level IOI
	 * 
	 * @return array | Pixafy_Ordergroove_Model_Ioi_Discountdata_Item[]
	 */
	public function getIncentiveItems(){
		return $this->_discountItems;
	}
}
?>