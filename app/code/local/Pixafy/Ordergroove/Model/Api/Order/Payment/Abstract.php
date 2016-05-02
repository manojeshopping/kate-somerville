<?php
/**
 * Abstract payment method class. This is the parent class for Payment Gateway
 * specific logic. Child classes handle logic dealing with error codes, etc.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method abstract getErrorCodeMaps()
 * @method getErrorCode(string $errorMessage)
 * 
 */
abstract class Pixafy_Ordergroove_Model_Api_Order_Payment_Abstract extends Mage_Core_Model_Abstract{
	
	/**
	 * Ordergroove payment error codes
	 */
	const ERROR_CODE_INVALID_CARD_TYPE			=	100;
	const ERROR_CODE_INVALID_CARD_NUMBER		=	110;
	const ERROR_CODE_CARD_HAS_EXPIRED			=	120;
	const ERROR_CODE_INVALID_BILLING_ADDRESS	=	130;
	const ERROR_CODE_PAYMENT_DECLINED			=	140;

	
	/**
	 * Abstract function all children must implement
	 * that contains the array of error codes to
	 * error messages. 
	 * 	'code' => 'message' format
	 */
	abstract public function getErrorCodeMaps();
	
	/**
	 * Check if the error message exists the in pre-defined
	 * error message array for the specified payment class
	 * 
	 * @param string $errorMessage
	 * @return string
	 */
	public function getErrorCode($errorMessage){
		/**
		 * Control the string by forcing it to be lowercase
		 */
		$errorMessage	=	strtolower($errorMessage);
		$errorMaps		=	$this->getErrorCodeMaps();
		if(array_key_exists($errorMessage, $errorMaps)){
			return $errorMaps[$errorMessage];
		}
		
	}
}
?>
