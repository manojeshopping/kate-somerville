<?php
/**
 * A lite version of the Magento sales quote. This class performs
 * many actions usually handled by Magento's quote class, including
 * adding products and calculating totals
 * 
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method addProducts()
 * @method _extractItems()
 * @method _addItemsToQuote()
 * @method getItems()
 * @method extractTotals()
 * @method currencyFormat(float $val)
 * @method _resetQuoteTotals()
 * @method _resetAddressTotals(Mage_Sales_Model_Quote_Address $address)
 * @method getCurrencyCode()
 */
class Pixafy_Ordergroove_Model_Api_Order_Quotelite extends Pixafy_Ordergroove_Model_Api_Order_Address{
	
	/**
	 *	XML keys to extract values from OrderGroove data
	 */
	const KEY_TOTAL_GRAND		=	'orderTotalValue';
	const KEY_TOTAL_SUBTOTAL	=	'orderSubtotalValue';
	const KEY_TOTAL_TAX			=	'orderSalesTax';
	const KEY_TOTAL_DISCOUNT	=	'orderDiscount';
	const KEY_TOTAL_SHIPPING	=	'orderShipping';
	const KEY_ORDER_CURRENCY	=	'orderCurrency';
	
	/**
	 * Currency code constant
	 */
	const DATA_KEY_CURRENCY		=	'currency_code';
	
	/**
	 * Array of items extract from the XML
	 *
	 * @var array Varien_Object[]
	 */
	protected $_items	=	array();
	
	/**
	 * Extract products from XML and add to quite
	 */
	public function addProducts(){
		$this->_extractItems();
		$this->_addItemsToQuote();
	}
	
	/**
	 *	Convert each item from the XML to a Varien_Object
	 * 	and add to the _items array
	 */
	protected function _extractItems(){
		/**
		 * If we have only one item, convert it to an array for easier processing
		*/
		if(!is_array($this->getFeedData()->items->item)){
			$this->getFeedData()->items->item	=	array($this->getFeedData()->items->item);
		}
		foreach($this->getFeedData()->items->item as $key => $itemObj){
			$item	=	new Varien_Object();
			$item->setQty((string)$itemObj->qty);
			$item->setSku((string)$itemObj->sku);
			$item->setName((string)$itemObj->name);
			$item->setProductId((string)$itemObj->product_id);
			$item->setDiscount(((string)$itemObj->discount)/((string)$itemObj->qty));
			$item->setRawDiscount((string)$itemObj->discount);
			$item->setFinalPrice((string)$itemObj->finalPrice);
			$item->setPrice((string)$itemObj->price);
			
			
			if(array_key_exists($item->getProductId(), $this->_items)){
				$currentItem = $this->_items[$item->getProductId()];
				$item->setQty($item->getQty() + $currentItem->getQty());
				$item->setDiscount(($currentItem->getRawDiscount() + $item->getRawDiscount()) / $item->getQty());
				$item->setFinalPrice($currentItem->getFinalPrice() + $item->getFinalPrice());
			}
			
			$this->_items[$item->getProductId()]	=	$item;
		}
	}
	
	/**
	 * Add items parsed from the XML to our quote
	 */
	protected function _addItemsToQuote(){
		foreach($this->getItems() as $item){
			//$product	=	Mage::getModel('catalog/product')->load($item->getProductId());
			$product	=	Mage::getModel('ordergroove/catalog_product')->load($item->getProductId());
			$product->setOrdergroovePrice($item->getPrice());
			$buyInfo	=	array('qty' => $item->getQty());
			$this->getQuote()->addProduct($product, new Varien_Object($buyInfo));
		}
	}
	
	/**
	 * Return the current array of items
	 * from the parsed XML
	 *
	 * @return Varien_Object[]
	 */
	public function getItems(){
		return $this->_items;
	}
	
	
	/**
	* Extract the totals from our received XML
	* and apply all discount
	*/
	public function extractTotals(){
		$this->data			=	$this->getFeedData()->head;
		/**
		 * Use the existing subtotal and discount fields
		 * to determine if a coupon was previously added
		 * and applied a discount.
		 */
		$currentSubtotal 				=	$this->getQuote()->getSubtotal();
		$currentSubtotalWithDiscount 	= 	$this->getQuote()->getSubtotalWithDiscount();
		
		$existingDiscount = false;
		if($currentSubtotal != $currentSubtotalWithDiscount){
			if($currentSubtotal > $currentSubtotalWithDiscount){
				$existingDiscount = ($currentSubtotal-$currentSubtotalWithDiscount);
			}
		}
		
		$discountAmount		=	$this->extractField(self::KEY_TOTAL_DISCOUNT);
		if(!$discountAmount || !is_numeric($discountAmount)){
			$discountAmount = 0;
		}
		
		/**
		 * Add the coupon discount if it exists.
		 */
		if($existingDiscount){
			$combinedDiscount = $discountAmount+$existingDiscount;
		}
			
		/**
		 *	Reset subtotal, grand total, discount total,
		 *	and base total of each back to 0
		*/
		$this->_resetQuoteTotals();
		
		/**
		 *	Determine the address type
		 */
		$addressType	=	($this->getQuote()->isVirtual() ? (self::ADDRESS_TYPE_BILLING) : (self::ADDRESS_TYPE_SHIPPING));
		foreach ($this->getQuote()->getAllAddresses() as $address) {
			
			/**
			 *	Reset address totals back to 0
			 */
			$address	=	$this->_resetAddressTotals($address);

			/**
			 *	Set subtotal and base subtotal
			 */
			$this->getQuote()->setSubtotal((float) $this->getQuote()->getSubtotal() + $address->getSubtotal());
			$this->getQuote()->setBaseSubtotal((float) $this->getQuote()->getBaseSubtotal() + $address->getBaseSubtotal());

			/**
			 *	Set subtotal and base subtotal with discount
			 */
			$this->getQuote()->setSubtotalWithDiscount((float) $this->getQuote()->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount());
			$this->getQuote()->setBaseSubtotalWithDiscount((float) $this->getQuote()->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount());
			
			/**
			 *	Set grand and base grand total
			 */
			$this->getQuote()->setGrandTotal((float) $this->getQuote()->getGrandTotal() + $address->getGrandTotal());
			$this->getQuote()->setBaseGrandTotal((float) $this->getQuote()->getBaseGrandTotal() + $address->getBaseGrandTotal());
			
			/**
			 *	Save the quote before applying discounts
			 */
			$this->_saveQuote();
			
			/**
			 *	Apply the discount to grand and base grand totals
			 */
			$this->getQuote()->setGrandTotal($this->getQuote()->getBaseSubtotal()-$discountAmount);
			$this->getQuote()->setBaseGrandTotal($this->getQuote()->getBaseSubtotal()-$discountAmount);
			
			/**
			 *	Apply discount to subtotal and base subtotl with discounts
			 */
			$this->getQuote()->setSubtotalWithDiscount($this->getQuote()->getBaseSubtotal()-$discountAmount);
			$this->getQuote()->setBaseSubtotalWithDiscount($this->getQuote()->getBaseSubtotal()-$discountAmount);
			
			/**
			 *	Save the final quote prices
			 */
			$this->_saveQuote();
			
			/**
			 *	Apply prices to the appropriate address
			 *	as determined above
			 */
			if($address->getAddressType()==$addressType){
				
				/**
				 *	Set subtotal and base subtotal with discount to the address
				 */
				$address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount()-$discountAmount);
				$address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount()-$discountAmount);
				
				/**
				 *	Set grand total and base grand total to the address with discount
				 */
				$address->setGrandTotal((float) $address->getGrandTotal()-$discountAmount);
				$address->setBaseGrandTotal((float) $address->getBaseGrandTotal()-$discountAmount);
				
				/**
				 *	Set the discount amount to the address. Should always be a negative
				 *	number so that it will subtract the amount
				 */
				$address->setDiscountAmount(-($combinedDiscount));
				$address->setBaseDiscountAmount(-($combinedDiscount));
				if($this->getQuote()->getCouponCode()){
					$address->setDiscountDescription(self::DISCOUNT_TYPE_DESCRIPTION. ' ('.$this->getQuote()->getCouponCode().')');
				}
				else{
					$address->setDiscountDescription(self::DISCOUNT_TYPE_DESCRIPTION);
				}
				
				$address->save();
			}
			
			/**
			 *	Now apply the discount for each individual line item
			 */
			foreach($this->getQuote()->getAllItems() as $item){
				$apiItem	=	$this->_items[$item->getProductId()];
				$item->setDiscountAmount( $item->getDiscountAmount() + ($apiItem->getDiscount()*$item->getQty()) );
				$item->setBaseDiscountAmount( $item->getBaseDiscountAmount() + ($apiItem->getDiscount()*$item->getQty()) );
			}
		}
		
		$addressType	=	($this->getQuote()->isVirtual() ? (self::ADDRESS_TYPE_BILLING) : (self::ADDRESS_TYPE_SHIPPING));
		foreach ($this->getQuote()->getAllAddresses() as $address) {
			if($address->getAddressType() == $addressType){
				if($address->getGrandTotal() <= 0){
					Mage::register(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_NEGATIVE_TOTAL_API_ORDER, 1);
					$address->setBaseGrandTotal(0);
					$address->setGrandTotal(0);
					$address->setSubtotalWithDiscount(0);
					$address->setBaseSubtotalWithDiscount(0);
					
					$this->getQuote()->setGrandTotal(0);
					$this->getQuote()->setBaseGrandTotal(0);
					
					$this->getQuote()->setSubtotalWithDiscount(0);
					$this->getQuote()->setBaseSubtotalWithDiscount(0);
					$this->_saveQuote();
				}
			}
		}
	}

	/**
	 * Format currency value
	 *
	 * @param float $val
	 * @return float
	 */
	protected function currencyFormat($val){
		return Mage::helper('core')->currency($val, false, false);
	}
	
	/**
	 * Reset our quote totals
	 */
	protected function _resetQuoteTotals(){
		$this->getQuote()->setSubtotal(0);
		$this->getQuote()->setBaseSubtotal(0);

		$this->getQuote()->setSubtotalWithDiscount(0);
		$this->getQuote()->setBaseSubtotalWithDiscount(0);

		$this->getQuote()->setGrandTotal(0);
		$this->getQuote()->setBaseGrandTotal(0);
	}
	
	/**
	 * Reset address totals
	 * @param 	Mage_Sales_Model_Quote_Address $address
	 * @return 	Mage_Sales_Model_Quote_Address
	 */
	protected function _resetAddressTotals($address){
		$address->setSubtotal(0);
		$address->setBaseSubtotal(0);
		$address->setGrandTotal(0);
		$address->setBaseGrandTotal(0);
		$address->collectTotals();
		return $address;
	}
	
	/**
	 * Return the currency code as defined in the XML
	 * 
	 * @return string
	 */
	public function getCurrencyCode(){
		if(!$this->getData(self::DATA_KEY_CURRENCY)){
			$this->data			=	$this->getFeedData()->head;
			$this->setData(self::DATA_KEY_CURRENCY, $this->extractField(self::KEY_ORDER_CURRENCY));
		}
		return $this->getData(self::DATA_KEY_CURRENCY);
	}
	
	/**
	 * Apply a coupon to the order
	 */
	public function applyCoupon(){
		if(property_exists($this->getFeedData()->head, 'orderCoupon')){
			$code = trim($this->getFeedData()->head->orderCoupon);
			if($code){
				$this->getQuote()->setCouponCode($code);
			}
		}
	}
}
?>
