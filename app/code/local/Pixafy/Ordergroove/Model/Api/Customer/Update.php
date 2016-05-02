<?php
/**
 * API class to handle customer information updates.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Api_Customer_Update extends Pixafy_Ordergroove_Model_Api_Generic{

	/**
	 * Error messages
	 */
	const ERROR_MISSING_FIELDS	=	"There are some missing fields: |Customer id: %s| Old email: %s | New Email: %s";
	const API_RESPONSE_SUCCESS	=	'SUCCESS';
	const LOG_ACTIVITY			=	'Customer API Update (ID)';

	/**
	 * The current customer that is being updated
	 * 
	 * @var Mage_Customer_Model_Customer
	 */
	protected $_customer;
	
	/**
	 * The old email address of the customer
	 * 
	 * @var string
	 */
	protected $_oldEmail;
	
	/**
	 * The new email of the customer
	 * 
	 * @var string
	 */
	protected $_newEmail;
	
	/**
	 * The json request string that will
	 * be sent to OrderGroove
	 * 
	 * @var string
	 */
	protected $_request;
	
	/**
	 * The response that is received after
	 * posting the JSON
	 * 
	 * @var string
	 */
	protected $_response;
	
	/**
	 * Set the current customer
	 * 
	 * @param Mage_Customer_Model_Customer $customer
	 */
	public function setCustomer($customer){
		$this->_customer	=	$customer;
	}
	
	/**
	 * Return the current customer
	 * 
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer(){
		return $this->_customer;
	}
	
	/**
	 * Send the updated information over to OrderGroove
	 */
	public function sendCustomerUpdate(){
		
		/**
		 * Check and see if the update is enabled in configuration panel
		 */
		if($this->_getConfig()->isCustomerUpdateApiEnabled()){
			/**
			 * Ensure the required fields are present
			 */
			if($this->getCustomer()->getId() && $this->_getOldEmail() && $this->_getNewEmail()){
				$this->_buildJsonRequest();
				$this->_postRequest();
			}
			else{
				//$this->addError($this->__("There are some missing fields: Customer id: ".$this->getCustomer()->getId()." | Old email: ".$this->_getOldEmail()." | New Email: ".$this->_getNewEmail()));
				$this->addError(Mage::helper('ordergroove')->__(self::ERROR_MISSING_FIELDS, $this->getCustomer()->getId(), $this->_getOldEmail(), $this->_getNewEmail()));
			}
		}
	}
	
	/**
	 * Return the customers old email
	 * 
	 * @return string
	 */
	protected function _getOldEmail(){
		if(!$this->_oldEmail){
			$this->_oldEmail = trim($this->getCustomer()->getOrigData('email'));
		}
		return $this->_oldEmail;
	}
	
	/**
	 * Return the customer's new email
	 * 
	 * @return string
	 */
	protected function _getNewEmail(){
		if(!$this->_newEmail){
			$this->_newEmail	=	trim($this->getCustomer()->getData('email'));
		}
		return $this->_newEmail;
	}

	/**
	 * Build the JSON request that contains the
	 * customer change data.
	 */
	protected function _buildJsonRequest(){
		$this->_request = 'update_request={"merchant_id":"'.Mage::helper('ordergroove/config')->getMerchantId().'","user":{"user_id":"'.$this->getCustomer()->getId().'","user_id_hash":"'.$this->_getRc4Encryptor()->encrypt($this->getCustomer()->getId()).'","old_email":"'.$this->_getOldEmail().'","new_email":"'.$this->_getNewEmail().'"}}';
	}	
	/**
	 * Post the request to OrderGroove
	 */
	protected function _postRequest(){
		try{
			
			/**
			 * Log the request.
			 */
			$this->log($this->_request, Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_REQUEST);
			/**
			 * Always set the key to HTTP,
			 * even is posting to https
			 */
			$opts = array(
				'http' => array(
					'header'	=> "Content-type: application/x-www-form-urlencoded\r\n",
					'method'	=> 'POST',
					'content'	=> $this->_request,
				)
			);
			$context	=	stream_context_create($opts);
			$result		=	file_get_contents($this->_getConfig()->getCustomerUpdateApiPostUrl(), false, $context);
			
			/**
			 * Log response
			 */
			$this->log($result, ($result == self::API_RESPONSE_SUCCESS ? Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_SUCCESS : '')); 
		}
		catch(Exception $e){
			$this->log($e->getMessage());
		}
		//Mage::getModel('adminnotification/inbox')->add(Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR, "OrderGroove errors", "There are ordergroove errors", Mage::getUrl('customer/account/login'), true);
	}
	
	/**
	 * Log function
	 * 
	 * @param mixed $message
	 * @param string $type
	 */
	public function log($message, $type=''){
		if(!$type){
			$type	=	Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_ERROR;
		}
		Mage::getModel('ordergroove/log')->createLog(self::LOG_ACTIVITY.' - '.$this->getCustomer()->getId(), $type, "Key: ".$this->_getLogKey()."<br>".$message);
	}
	
}
?>