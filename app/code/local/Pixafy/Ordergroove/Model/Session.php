<?php
/**
 * Ordergroove Session class. Checks for cookies set into
 * the $_COOKIES superglobal that are set by OrderGroove
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Session extends Mage_Core_Model_Session_Abstract{

	/**
	 * Cookie key for OrderGroove session id
	 */
	const COOKIE_TAG_SESSION_ID			=	'og_session_id';
	const COOKIE_TAG_HAS_AUTOSHIP_ITEMS	=	'og_autoship';
	const COOKIE_TAG_IOI_ITEM_DISCOUNT	=	'og_cart_autoship';
	const COOKIE_TAG_IOI_ORDER_DISCOUNT	=	'og_coupon_code';
	
	/**
	 * Check for and return the OrderGroove Session Id
	 * 
	 * @return string
	 */
	public function getOrdergrooveSessionId(){
		return (isset($_COOKIE[self::COOKIE_TAG_SESSION_ID]) ? $_COOKIE[self::COOKIE_TAG_SESSION_ID] : '');
	}
	
	/**
	 * Check for the OrderGrovoe autoship cookie.
	 * If it exists and the value is set to 1, then
	 * return true. Otherwise return false
	 * 
	 * @return boolean
	 */
	public function hasAutoshipItems(){
		if(isset($_COOKIE[self::COOKIE_TAG_HAS_AUTOSHIP_ITEMS])){
			if($_COOKIE[self::COOKIE_TAG_HAS_AUTOSHIP_ITEMS] == 1){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * Check for the og_cart_autoship cookie. If it is set
	 * then there is some intial order incentive data to 
	 * process.
	 * 
	 * @return boolean
	 */
	public function hasIoiItemDiscountData(){
		if(isset($_COOKIE[self::COOKIE_TAG_IOI_ITEM_DISCOUNT])){
			if(!is_null($_COOKIE[self::COOKIE_TAG_IOI_ITEM_DISCOUNT])){
				if($_COOKIE[self::COOKIE_TAG_IOI_ITEM_DISCOUNT] != ''){
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * Check for the og_coupon_code cookie. If it is set
	 * then there is some intial order incentive data to 
	 * process.
	 * 
	 * @return boolean
	 */
	public function hasIoiOrderDiscountData(){
		if(isset($_COOKIE[self::COOKIE_TAG_IOI_ORDER_DISCOUNT])){
			if(!is_null($_COOKIE[self::COOKIE_TAG_IOI_ORDER_DISCOUNT])){
				if($_COOKIE[self::COOKIE_TAG_IOI_ORDER_DISCOUNT] != ''){
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * Return the og_cart_autoship IOI data.
	 * 
	 * @return Pixafy_Ordergroove_Model_Ioi_Discountdata
	 */
	public function getIoiDiscountData(){
		$discountData	=	Mage::getModel('ordergroove/ioi_discountdata');
		if($this->hasIoiItemDiscountData()){
			$discountData->setDiscountAsItemLevel();
			$data	=	json_decode($_COOKIE[self::COOKIE_TAG_IOI_ITEM_DISCOUNT], true);
			$discountData->setIncentiveItems($data);
		}
		
		if($this->hasIoiOrderDiscountData()){
			$discountData->setDiscountAsOrderLevel();
			$discountData->setOrderIncentiveAmount($_COOKIE[self::COOKIE_TAG_IOI_ORDER_DISCOUNT]);
		}
		return $discountData;
	}
	
	/**
	 * Return whether the session data exists to
	 * apply free shipping.
	 * 
	 * @return boolean
	 */
	public function shouldApplyIoiFreeShipping(){
		return (($this->getConfig()->isIoiFreeshippingEnabled() && $this->hasAutoshipItems()) ? TRUE : FALSE);
	}
	
	/**
	 * Return the configuration class
	 * 
	 * @return Pixafy_Ordergroove_Helper_Config
	 */
	public function getConfig(){
		return Mage::helper('ordergroove/config');
	}
}
?>
