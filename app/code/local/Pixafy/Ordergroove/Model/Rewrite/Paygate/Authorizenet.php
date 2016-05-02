<?php
/**
 * OrderGroove Rewrite of the Mage_Paygate_Model_Authorizenet class. Allows for
 * custom error message generation. 
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Rewrite_Paygate_Authorizenet extends Mage_Paygate_Model_Authorizenet
{
	/**
	 * Send request with new payment to gateway
	 *
	 * @param Mage_Payment_Model_Info $payment
	 * @param decimal $amount
	 * @param string $requestType
	 * @return Mage_Paygate_Model_Authorizenet
	 * @throws Mage_Core_Exception
	 */
	protected function _place($payment, $amount, $requestType)
	{
		/**
		 * If the order is NOT placed through the OrderGroove website
		 * then simply use the default _place logic.
		 */
		$ogHelper	=	Mage::helper('ordergroove/installer');
		if(Mage::app()->getWebsite()->getCode() != $ogHelper->WEBSITE_CODE_ORDERGROOVE){
			return parent::_place($payment, $amount, $requestType);
		}

		$payment->setAnetTransType($requestType);
		$payment->setAmount($amount);
		
		
		/**
		 * Test values. In the system configuration panel in the 
		 * admin a user can specify to force Auth.net to throw an
		 * error. The amount designated in the admin will force
		 * a specific error code to be thrown. 
		 * 
		 * @url http://www.authorize.net/support/merchant/Transaction_Response/Response_Reason_Codes_and_Response_Reason_Text.htm
		 * 
		 * This url will give you a list of codes, pass in the Response Reason Code as the amount and it will trigger that error.
		 */
		$configHelper	=	Mage::helper('ordergroove/config');
		
		/**
		 * 
		 * Disabling this code for production
		 * 
		 * if($configHelper->getForceError()){
		 * $payment->setAmount($configHelper->getForceErrorAmount());
		 * $payment->setCcNumber('4222222222222');
		 * }
		 */
		if($configHelper->getForceError()){
			$payment->setCcNumber('4222222222222');
		}
		
		$request= $this->_buildRequest($payment);
		$result = $this->_postRequest($request);
		switch ($requestType) {
			case self::REQUEST_TYPE_AUTH_ONLY:
				$newTransactionType = Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH;
				$defaultExceptionMessage = Mage::helper('paygate')->__('Payment authorization error.');
				break;
			case self::REQUEST_TYPE_AUTH_CAPTURE:
				$newTransactionType = Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE;
				$defaultExceptionMessage = Mage::helper('paygate')->__('Payment capturing error.');
				break;
		}

		switch ($result->getResponseCode()) {
			case self::RESPONSE_CODE_APPROVED:
				$this->getCardsStorage($payment)->flushCards();
				$card = $this->_registerCard($result, $payment);
				$this->_addTransaction(
					$payment,
					$card->getLastTransId(),
					$newTransactionType,
					array('is_transaction_closed' => 0),
					array($this->_realTransactionIdKey => $card->getLastTransId()),
					Mage::helper('paygate')->getTransactionMessage(
						$payment, $requestType, $card->getLastTransId(), $card, $amount
					)
				);
				if ($requestType == self::REQUEST_TYPE_AUTH_CAPTURE) {
					$card->setCapturedAmount($card->getProcessedAmount());
					$this->getCardsStorage($payment)->updateCard($card);
				}
				return $this;
			case self::RESPONSE_CODE_HELD:
				if ($result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_PENDING_REVIEW_AUTHORIZED
					|| $result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_PENDING_REVIEW
				) {
					$card = $this->_registerCard($result, $payment);
					$this->_addTransaction(
						$payment,
						$card->getLastTransId(),
						$newTransactionType,
						array('is_transaction_closed' => 0),
						array(
							$this->_realTransactionIdKey => $card->getLastTransId(),
							$this->_isTransactionFraud => true
						),
						Mage::helper('paygate')->getTransactionMessage(
							$payment, $requestType, $card->getLastTransId(), $card, $amount
						)
					);
					if ($requestType == self::REQUEST_TYPE_AUTH_CAPTURE) {
						$card->setCapturedAmount($card->getProcessedAmount());
						$this->getCardsStorage()->updateCard($card);
					}
					$payment
						->setIsTransactionPending(true)
						->setIsFraudDetected(true);
					return $this;
				}
				if ($result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_PARTIAL_APPROVE) {
					$checksum = $this->_generateChecksum($request, $this->_partialAuthorizationChecksumDataKeys);
					$this->_getSession()->setData($this->_partialAuthorizationChecksumSessionKey, $checksum);
					if ($this->_processPartialAuthorizationResponse($result, $payment)) {
						return $this;
					}
				}
				Mage::throwException(strtolower('Auth.net error '.$result->getResponseReasonCode().' - '.str_replace("(TESTMODE) ", "", $result->getResponseReasonText())));
			case self::RESPONSE_CODE_DECLINED:
			case self::RESPONSE_CODE_ERROR:
				/**
				 * Ordergroove Requires the error code as well as the error text.
				 * Add it to the error message that is thrown in the exception.
				 */
				Mage::throwException(strtolower('Auth.net error '.$result->getResponseReasonCode().' - '.str_replace("(TESTMODE) ", "", $result->getResponseReasonText())));
			default:
				Mage::throwException($defaultExceptionMessage);
		}
		return $this;
	}
	
	/**
	 * Return whether the Auth.net payment method is available.
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 * @return boolean
	 */
	public function isAvailable($quote=null){
		/**
		 * If zero total cc is enabled then do not allow Auth.net to be enabled.
		 * This will allow the custom OrderGrove Cc method to be autoselected.
		 */
		if(Mage::getModel('ordergroove/payment_method_zerototalcc')->isAvailable($quote)){
			return false;
		}
		return parent::isAvailable($quote);
	}
}
