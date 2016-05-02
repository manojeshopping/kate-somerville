<?php
/**
 * Subscription creation parent class. Contains functions and variables used
 * by both the POST and GET subscription classes.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
abstract class Pixafy_Ordergroove_Helper_Subscription_Abstract extends Mage_Core_Helper_Abstract{
	
	const LOG_ACTIVITY	=	'Subscription Creation';
	
	/**
	 * The magento order that is used
	 * to gather the data sent in the 
	 * subscription request
	 * 
	 * @var Mage_Sales_Model_Order
	 */
	protected $_order;
	
	/**
	 * The JSON string that is
	 * sent to OrderGroove to 
	 * create the subscription
	 * 
	 * @var string
	 */
	protected $_json='';
	
	/**
	 * Tab count used for formatting
	 * of the JSON string
	 * 
	 * @var int
	 */
	private	$_tabCount	=	0;
	
	/**
	 * Encryptor class used to hash 
	 * sensitive data.
	 * 
	 * @var Pixafy_Ordergroove_Helper_Rc4
	 */
	protected $_encryptor;
	
	/**
	 * Configuration class
	 * 
	 * @var Pixafy_Ordergroove_Helper_Config
	 */
	protected $_config;
	
	/**
	 * Abstract function that must be
	 * implented by child classes
	 * to send the subscription data,
	 * either via POST or GET methods
	 */
	abstract public function process();
	
	/**
	 * Set the order that will be used for 
	 * the subscription. Also inits the
	 * encryptor class
	 * 
	 * @param Mage_Sales_Model_Order
	 */
	public function setOrder($order){
		$this->_encryptor	=	Mage::helper('ordergroove/rc4');
		$this->_order	=	$order;
	}
	
	/**
	 * Return the current order being processed
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder(){
		return $this->_order;
	}
	
	/**
	 * Append some value to the json string.
	 * 
	 * @param string $val
	 * @param boolean $newLine
	 */
	protected function jsonAppend($val, $newLine=TRUE){
		if($this->getJson() && $newLine){
			$this->_json.="\n";
		}
		
		for($x=0; $x<$this->_tabCount; $x++){
			$this->_json.="\t";
		}
		$this->_json.=$val;
	}
	
	/**
	 * Return the current json string
	 * 
	 * @return string
	 */
	protected function getJson(){
		return $this->_json;
	}
	
	/**
	 * Check to see if the order is a subscription order.
	 * Iterate over each item in the order, loading
	 * the product model to get all product data. If
	 * the subscription enabled attribute is set to Yes,
	 * then the order is a subscription order
	 * 
	 * @return boolean
	 */
	protected function isSubscriptionOrder(){
		return $this->getSession()->hasAutoshipItems();
	}
	
	/**
	 * Create the JSON header. Contains merchant
	 * and session id.
	 */
	protected function _createJsonHeader(){
		$this->jsonAppend('create_request={');
		$this->_tabCount++;
		$this->jsonAppend('"merchant_id": "'.$this->getConfig()->getMerchantId().'",');
		$this->jsonAppend('"session_id" : "'.$this->getSession()->getOrdergrooveSessionId().'"');
		
	}
	
	/**
	 * Append the footer value to the json
	 */
	protected function _createJsonFooter(){
		$this->_tabCount--;
		$this->jsonAppend('}');
	}
	
	/**
	 * Add the order increment id to the json string
	 */
	protected function _addOrderId(){
		$this->_tabCount=0;
		$this->jsonAppend(",", FALSE);
		$this->_tabCount++;
		$this->jsonAppend('"merchant_order_id": "'.$this->getOrder()->getIncrementId().'",');
	}
	
	/**
	 * Add all user data to the json string. Adds
	 * customer, shipping, and billing data
	 */
	protected function _addUserInformation(){
		$this->jsonAppend('"user": {');
		$this->_tabCount++;
		$this->jsonAppend('"user_id": "'.$this->getOrder()->getCustomerId().'",');
		$this->jsonAppend('"first_name": "'.urlencode($this->getOrder()->getCustomerFirstname()).'",');
		$this->jsonAppend('"last_name": "'.urlencode($this->getOrder()->getCustomerLastname()).'",');
		$this->jsonAppend('"email": "'.urlencode($this->getOrder()->getCustomerEmail()).'",');
		$this->jsonAppend('"billing_address":{ ');
		$this->_tabCount++;
		$this->jsonAppend('"first_name": "'.urlencode($this->getOrder()->getBillingAddress()->getFirstname()).'",');
		$this->jsonAppend('"last_name": "'.urlencode($this->getOrder()->getBillingAddress()->getLastname()).'",');
		$this->jsonAppend('"company_name": "'.urlencode($this->getOrder()->getBillingAddress()->getCompany()).'",');
		$street	=	$this->getOrder()->getBillingAddress()->getStreet();
		if(is_array($street)){
			$addressOne	=	$street[0];
			$addressTwo	=	$street[1];
		}
		else{
			$addressOne	=	$street;
			$addressTwo	=	'';
		}
		$this->jsonAppend('"address": "'.urlencode($addressOne).'",');
		$this->jsonAppend('"address2": "'.urlencode($addressTwo).'",');
		$this->jsonAppend('"city": "'.urlencode($this->getOrder()->getBillingAddress()->getCity()).'",');
		$this->jsonAppend('"state_province_code": "'.urlencode($this->getOrder()->getBillingAddress()->getRegionCode()).'",');
		$this->jsonAppend('"zip_postal_code": "'.urlencode($this->getOrder()->getBillingAddress()->getPostcode()).'",');
		$this->jsonAppend('"phone": "'.urlencode($this->getOrder()->getBillingAddress()->getTelephone()).'",');
		$this->jsonAppend('"fax": "'.urlencode($this->getOrder()->getBillingAddress()->getFax()).'",');
		$this->jsonAppend('"country_code": "'.urlencode($this->getOrder()->getBillingAddress()->getCountryId()).'"');
		$this->_tabCount--;
		$this->jsonAppend('},');
		
		$this->jsonAppend('"shipping_address":{ ');
		$this->_tabCount++;
		$this->jsonAppend('"first_name": "'.urlencode($this->getOrder()->getShippingAddress()->getFirstname()).'",');
		$this->jsonAppend('"last_name": "'.urlencode($this->getOrder()->getShippingAddress()->getLastname()).'",');
		$this->jsonAppend('"company_name": "'.urlencode($this->getOrder()->getShippingAddress()->getCompany()).'",');
		$street	=	$this->getOrder()->getShippingAddress()->getStreet();
		if(is_array($street)){
			$addressOne	=	$street[0];
			$addressTwo	=	$street[1];
		}
		else{
			$addressOne	=	$street;
			$addressTwo	=	'';
		}
		$this->jsonAppend('"address": "'.urlencode($addressOne).'",');
		$this->jsonAppend('"address2": "'.urlencode($addressTwo).'",');
		$this->jsonAppend('"city": "'.urlencode($this->getOrder()->getShippingAddress()->getCity()).'",');
		$this->jsonAppend('"state_province_code": "'.urlencode($this->getOrder()->getShippingAddress()->getRegionCode()).'",');
		$this->jsonAppend('"zip_postal_code": "'.urlencode($this->getOrder()->getShippingAddress()->getPostcode()).'",');
		$this->jsonAppend('"phone": "'.urlencode($this->getOrder()->getShippingAddress()->getTelephone()).'",');
		$this->jsonAppend('"fax": "'.urlencode($this->getOrder()->getShippingAddress()->getFax()).'",');
		$this->jsonAppend('"country_code": "'.urlencode($this->getOrder()->getShippingAddress()->getCountryId()).'"');
		$this->_tabCount--;
		$this->jsonAppend('}');
		$this->_tabCount--;
		$this->jsonAppend('},');
		
	}
	
	/**
	 * Add the payment information to the JSON string
	 */
	protected function _addPaymentInformation(){
		$this->jsonAppend('"payment":{');
		$this->_tabCount++;
		$this->jsonAppend('"cc_holder":"'.urlencode($this->_getCcCardHolder()).'",');
		$this->jsonAppend('"cc_type":"'.$this->mapCreditCards($this->_getCcType()).'",');
		$this->jsonAppend('"cc_number":"'.urlencode($this->_getCcNumber()).'",');
		$this->jsonAppend('"cc_exp_date":"'.urlencode($this->_getCcExpDate()).'"');
		
		$this->_tabCount--;
		$this->jsonAppend('}');
	}
	
	/**
	 * Map the credit card type to the 
	 * OrderGroove CC type
	 * 
	 * The credit cart type is 
	 * 1 = Visa
	 * 2 = MasterCard
	 * 3 = American Express
	 * 4 = Discover
	 * 5 = Diners
	 * 
	 * @param string $key
	 * @return string
	 */
	public function mapCreditCards($key){
		$maps	=	array(
			"VI"	=>	1,
			"MC"	=>	2,
			"AE"	=>	3,
			"DI"	=>	4,
			"OT"	=>	5
		);
		return $maps[$key];
	}
	
	/**
	 * Get the credit card type
	 * 
	 * @return string
	 */
	protected function _getCcType(){
		$payment	=	Mage::app()->getRequest()->getPost('payment');
		return $payment['cc_type'];
	}
	
	/**
	 * Return the encrypted credit card number
	 * 
	 * @return string
	 */
	protected function _getCcNumber(){
		$payment	=	Mage::app()->getRequest()->getPost('payment');
		return $this->getEncryptor()->encrypt($payment['cc_number']);
	}
	
	/**
	 * Return the encrypted credit card expiration date
	 * 
	 * @return string
	 */
	protected function _getCcExpDate(){
		$payment	=	Mage::app()->getRequest()->getPost('payment');
		$expMonth	=	(strlen($payment['cc_exp_month'] == 1) ? "0".$payment['cc_exp_month'] : $payment['cc_exp_month']);
		$expYear	=	$payment['cc_exp_year'];
		return $this->getEncryptor()->encrypt($expMonth.'/'.$expYear);
	}
	
	/**
	 * Return the encrypted credit card holder
	 * 
	 * @return string
	 */
	protected function _getCcCardHolder(){
		return $this->getEncryptor()->encrypt($this->getOrder()->getBillingAddress()->getFirstname().' '.$this->getOrder()->getBillingAddress()->getLastname());
	}
	
	/**
	 * Return the encryptor class 
	 * 
	 * @return Pixafy_Ordergroove_Helper_Rc4
	 */
	public function getEncryptor(){
		return $this->_encryptor;
	}
	
	/**
	 * Return the config class
	 * 
	 * @return Pixafy_Ordergroove_Helper_Config
	 */
	public function getConfig(){
		if(!$this->_config){
			$this->_config	=	Mage::helper('ordergroove/config');
		}
		return $this->_config;
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
		Mage::getModel('ordergroove/log')->createLog(self::LOG_ACTIVITY.' - '.$this->getOrder()->getIncrementId(), $type, $message);
	}
	
	/**
	 * Return the Ordergroove session object
	 * 
	 * @return Pixafy_Ordergroove_Model_Session
	 */
	public function getSession(){
		return Mage::getSingleton('ordergroove/session');
	}
}
?>
