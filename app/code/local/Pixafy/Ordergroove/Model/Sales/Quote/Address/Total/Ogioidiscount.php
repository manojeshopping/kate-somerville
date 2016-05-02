<?php
/**
 * OrderGroove module initial order incentive discount total.
 * This class handles adding either the item level or 
 * order level discount for Intial Order Incentive (IOI).
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Sales_Quote_Address_Total_Ogioidiscount extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	
	/**
	 * Total code
	 */
	const OG_IOI_TOTAL_CODE	=	'og_ioi_discount';
	
	/**
	 * The Initial Order Incentive data that is
	 * set in the cookie
	 * 
	 * @var Pixafy_Ordergroove_Model_Ioi_Discountdata
	 */
	protected $_ioiData	=	array();
	
	/**
	 * An array of Mage_Sales_Model_Quote_Item ids as they key. Item ids in this array are found
	 * in the IOI data and thus do not need to have their discount reset to 0. Items not found
	 * in this array will have their discounts resets.
	 * 
	 * @var array
	 */
	protected $_ioiItemIds	=	array();
	
	/**
	 * Totals constructor. Set the code for the total.
	 */
	public function __construct(){
		$this->setCode(self::OG_IOI_TOTAL_CODE);
	}
	
	/**
	 * Collect the discount amount
	 * for the Intitial Order Incentive
	 *
	 * @param   Mage_Sales_Model_Quote_Address $address
	 * @return  Pixafy_Ordergroove_Model_Sales_Quote_Address_Total_Ogioidiscount
	 */
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		if(!$this->_isCorrectAddressType($address)){
			return $this;
		}
		
		$quote = $address->getQuote();
		
		/**
		 * Load the IOI item discount data,
		 * and return $this if there is
		 * no data found.
		 */
		$this->_loadIoiItemData();
		
		/*
		echo '<pre>';
		$json = $_COOKIE['og_cart_autoship'];
		$json = json_decode($json, true);
		
		echo '<pre>';print_r($json);echo "</pre>";
		exit;
		
		unset($json[0]['d']);
		echo '<pre>';print_r($json);echo "</pre>";
		echo '<pre>';print_r(json_encode($json));echo "</pre>";
		$_COOKIE['og_cart_autoship'] = json_encode($json);
		exit;
		*/
		$totalDiscount	=	0;
		$discountType	=	Mage::helper('ordergroove/config')->getIoiDiscountType();
		parent::collect($address);
		
		/**
		 * For order level incentives simply take the value from the cookie
		 * and subtract either the fixed value or the percentage value.
		 */
		if($this->getIoiData()->isOrderIncentive()){
			if($this->getIoiData()->isPercentageDiscount()){
				$totalDiscount	=	Mage::helper('ordergroove')->calculatePercentage($address->getSubtotal(), $this->getIoiData()->getOrderIncentiveAmount());
			}
			else if($this->getIoiData()->isFixedAmountDiscount()){
				$totalDiscount	=	$this->getIoiData()->getOrderIncentiveAmount();
			}
			$totalDiscount=$this->_formatDiscount($totalDiscount);
		}
		/**
		 * For item level incentives, iterate over each item on the quote, check if that item exists
		 * in the cookie data, and set the discount  for that item as either a percentage
		 * or a fixed amount.
		 */
		else if($this->getIoiData()->isItemIncentive()){
			foreach($quote->getAllItems() as $item){
				foreach($this->getIoiData()->getIncentiveItems()as $i => $incentiveItem){
					/**
					 * If the current incentive item has a discount
					 * and its product id is equal to the quote items
					 * product id.
					 */
					if($incentiveItem->hasDiscount() && ($incentiveItem->getId() == $item->getProductId())){
						$discount	=	0;
						if($this->getIoiData()->isPercentageDiscount()){
							$discount	=	Mage::helper('ordergroove')->calculatePercentage($item->getPrice(), $incentiveItem->getDiscountAmount());
						}
						else if($this->getIoiData()->isFixedAmountDiscount()){
							$discount	=	$incentiveItem->getDiscountAmount();
						}
						
						if($discount != 0){
							$discount	=	($discount * $item->getQty());
							$totalDiscount+=$discount;
							$totalDiscount=$this->_formatDiscount($totalDiscount);
							$item->setData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_BASE_OG_IOI_ITEM_DISCOUNT, -$discount);
							$item->setData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ITEM_DISCOUNT, -$discount);
							$this->_addFoundItemId($item->getId());
						}
					}
				}
			}
			
			/**
			 * Reset discounts for items that are not in the IOI data
			 */
			$this->_resetDiscountsForUnfoundItems($quote->getAllItems());
		}
		
		/**
		 * Set discount values on the quote.
		 */
		$quote->setData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT, $totalDiscount);
		$quote->setData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_BASE_OG_IOI_ORDER_DISCOUNT, $totalDiscount);
		
		/**
		 * If there is a discount, then add the amount
		 * to this total class.
		 */
		if($totalDiscount){
			$this->_addBaseAmount(-$totalDiscount);
			$this->_addAmount(-$totalDiscount);
		}
		return $this;
	}
	
	/**
	 * Add IOI discount label and total for the pages.
	 *
	 * @param   Mage_Sales_Model_Quote_Address $address
	 * @return  Pixafy_Ordergroove_Model_Sales_Quote_Address_Total_Ogioidiscount
	 */
	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		if(!$this->_isCorrectAddressType($address)){
			return $this;
		}
		$amount = $this->_calculateTotalDiscount($address);
		if ($amount!=0) {
			$title = Mage::helper('ordergroove')->__(Mage::helper('ordergroove/config')->getIoiDiscountLabel());
			$address->addTotal(array(
				'code'	=>	$this->getCode(),
				'title'	=>	$title,
				'value'	=>	-$amount
			));
		}
		return $this;
	}
	
	/**
	 * Return whether the address type to process
	 * IOI data is correct.
	 * 
	 * @param Mage_Sales_Model_Quote_Address $address
	 * @return boolean
	 */
	protected function _isCorrectAddressType($address){
		$quote = $address->getQuote();
		/**
		 * Determine the address type to apply this discount to. We only want
		 * to add the amount once so that the discount is not double counted.
		 * For virtual orders, set the discounts to the billing addresses,
		 * whereas for all remaining orders set the discount to the shipping
		 * address.
		 */
		$addressTypeToProcess		=	Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING;
		if($quote->isVirtual()){
			$addressTypeToProcess	=	Mage_Customer_Model_Address_Abstract::TYPE_BILLING;
		}
		
		/**
		 * Return if we are not dealing with the correct address type.
		 */
		if($address->getAddressType() != $addressTypeToProcess){
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * Return the IOI data
	 * 
	 * @return array
	 */
	public function getIoiData(){
		return $this->_ioiData;
	}
	
	/**
	 * Load the IOI item data from the cookie
	 */
	protected function _loadIoiItemData(){
		$this->_ioiData	=	Mage::getSingleton('ordergroove/session')->getIoiDiscountData();
	}
	
	/**
	 * Given the items currently on the quote, reset the discount back to 0 for any
	 * item that was not found in the IOI data from OrderGroove
	 * 
	 * @param array $items | Mage_Sales_Model_Quote_Item[]
	 */
	protected function _resetDiscountsForUnfoundItems($items){
		foreach($items as $item){
			if(!array_key_exists($item->getId(), $this->getFoundItemIds())){
				$item->setData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_BASE_OG_IOI_ITEM_DISCOUNT, 0);
				$item->setData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ITEM_DISCOUNT, 0);
			}
		}
	}
	
	/**
	 * Add an item id to the array of found ids
	 * 
	 * @param int $id
	 */
	protected function _addFoundItemId($id){
		$this->_ioiItemIds[$id]	=	1;
	}
	
	/**
	 * Return the array of found item ids.
	 * 
	 * @return array
	 */
	public function getFoundItemIds(){
		return $this->_ioiItemIds;
	}
	
	/**
	 * Calculate the total discount amount
	 * 
	 * @param Mage_Sales_Model_Quote_Address $address
	 * @return float
	 */
	protected function _calculateTotalDiscount($address){
		return $address->getQuote()->getData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT);
	}
	
	/**
	 * Format the discount to always have two decimal places.
	 * 
	 * @param float $discount
	 * @return float
	 */
	protected function _formatDiscount($discount){
		return Mage::getModel('directory/currency')->format($discount, array('display'=>Zend_Currency::NO_SYMBOL), false);
	}
}
?>