<?php
/**
 * Subscription POST class. Creates a subscription
 * in OrderGroove via POST
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Subscription_Post extends Pixafy_Ordergroove_Helper_Subscription_Abstract{
	
	const SUCCESS_SUBSCRIPTION_CREATED			=	'Subscription(s) created';
	const SUCCESS_CART_CLEARED					=	'Cart cleared';
	
	const ERROR_SUBSCRIPTION_URL_NOT_FOUND		=	'Could not create subscription for order %s, no subscription post url defined.';
	const ERROR_SUBSCRIPTION_FAILED_TO_CREATE	=	'Could not create subscription for order %s, the subscription failed to create: %s';
	const ERROR_NO_RESPONSE_FROM_OG				=	'No response was received from Ordergroove for order %s with subscription post url %s';
	
	const MESSAGE_NON_SUCCESS_RESPONSE			=	'There was no error response provided for order %s, but no confirmation either. Please check in OrderGroove';
	const MESSAGE_SUBSCRIPTION_CREATED			=	'Subscription created for order %s';
	const MESSAGE_CART_CLEARED					=	'Order %s successfully placed without autoship items';
	
	const PROPERTY_ERROR_MESSAGE				=	'error_message';
	const PROPERTY_RESULT						=	'result';
	/**
	 * Implementation of abstract process function.
	 * Triggers all parent functions to create the data.
	 * Executes a CURL request to submit the data
	 */
	public function process(){
		
		/**
		 * Get the subscription post url and make sure that it is not blank
		 */
		$subscriptionUrl	=	trim($this->getConfig()->getSubscriptionPostUrl());
		if(!$subscriptionUrl){
			$this->log($this->__(self::ERROR_SUBSCRIPTION_URL_NOT_FOUND, $this->getOrder()->getIncrementId()));
			return;
		}
		
		/**
		 * Create JSON header
		 */
		$this->_createJsonHeader();
		
		/**
		 * Create JSON body
		 */
		if($this->isSubscriptionOrder()){
			$this->_addOrderId();
			$this->_addUserInformation();
			$this->_addPaymentInformation();
		}
		
		/**
		 * Create JSON footer
		 */
		$this->_createJsonFooter();
		
		$this->log(Mage::helper('ordergroove')->clearCcDataSubscription($this->getJson()), Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_REQUEST);
		/**
		 * Submit POST request
		 */
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $subscriptionUrl);
		curl_setopt($ch,CURLOPT_POST, strlen($this->getJson()));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $this->getJson());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		
		/**
		 * Process result. If there is an error message, log the 
		 * response with the order number.
		 * 
		 * If it was successful, log that a successful subscription
		 * was created with the order number.
		 * 
		 * Otherwise, assume that the POST failed to properly submit
		 * and submit the error.
		 */
		$result	=	json_decode(json_decode($result));
		$this->log(serialize($result), Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_RESPONSE);
		if(property_exists($result, self::PROPERTY_ERROR_MESSAGE)){
			$this->log($this->__(self::ERROR_SUBSCRIPTION_FAILED_TO_CREATE, $this->getOrder()->getIncrementId(), $result->error_message));
		}
		else if(property_exists($result, self::PROPERTY_RESULT)){
			$resultMessage	=	trim($result->result);
			if($resultMessage	==	self::SUCCESS_SUBSCRIPTION_CREATED){
				$this->log($this->__(self::MESSAGE_SUBSCRIPTION_CREATED, $this->getOrder()->getIncrementId()), Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_SUCCESS);
			}
			else if($resultMessage	==	self::SUCCESS_CART_CLEARED){
				$this->log($this->__(self::MESSAGE_CART_CLEARED, $this->getOrder()->getIncrementId()), Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_SUCCESS);
			}
			else{
				$this->log($this->__(self::MESSAGE_NON_SUCCESS_RESPONSE, $this->getOrder()->getIncrementId()), Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_SUCCESS);
			}
		}
		else{
			$this->log($this->__(self::ERROR_NO_RESPONSE_FROM_OG, $this->getOrder()->getIncrementId(), $subscriptionUrl));
		}
	}
	
	/**
	 * Return the subscription failed message
	 * 
	 * @param string $orderId
	 * @param string $message
	 * @return string
	 */
	public function getSubscriptionFailedMessage($orderId, $message){
		return $this->__(self::ERROR_SUBSCRIPTION_FAILED_TO_CREATE, $orderId, $message);
	}
}
?>
