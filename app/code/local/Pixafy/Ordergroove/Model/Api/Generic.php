<?php
/**
 * Parent class of the order API class. Contains constants and 
 * functions that will be used by all children classes during
 * the order creation process.
 * 
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method addError(string $errorString)
 * @method hasError()
 * @method getErrors()
 * @method getResult()
 * @method addErrorAndExit(string $error)
 */
class Pixafy_Ordergroove_Model_Api_Generic extends Varien_Object{
	
	/**
	 * Log activity key
	 */
	const LOG_ACTIVITY	=	'API Order Placement';
	
	
	/**
	 * Quote address types
	 */
	const ADDRESS_TYPE_BILLING		=	'billing';
	const ADDRESS_TYPE_SHIPPING		=	'shipping';
	
	/**
	 * Error Messages
	 */
	 
	/**
	 * No data received message
	 */
	const ERROR_NO_DATA_RECEIVED	=	'No data was received.';
	
	/**
	 * Discount description message
	 */
	const DISCOUNT_TYPE_DESCRIPTION	=	'Subscription Discount';
	
	/**
	 * Success response code
	 */
	const SUCCESS_CODE_SUCCESSFUL	=	'SUCCESS';
	
	/** 
	 * Error response code
	 */
	const ERROR_CODE_ERROR			=	'ERROR';
	
	/**
	 * An array of error messages that have occurred.
	 * 
	 * @var array
	 */
	protected 	$_errors	=	array();
	
	/**
	 * The RC4 helper class encryptor
	 * 
	 * @var Pixafy_Ordergroove_Helper_Rc4
	 */
	protected $_rc4Encryptor;
	
	/**
	 * The OrderGroove module configuration class
	 * 
	 * @var Pixafy_Ordergroove_Helper_Config
	 */
	protected $_configClass;
	
	/**
	 * A random string used to represent a unique
	 * key for all log entries for a specific
	 * action
	 * 
	 * @var string
	 */
	protected $_logKey;
	
	/**
	 * Add an error message to the array of errors.
	 *
	 * @param string $errorString
	 */
	public function addError($errorString){
		$this->_errors[]	=	$errorString;
	}
	
	/**
	 * Return whether or not an error has occurred
	 * 
	 * @return boolean
	 */
	public function hasError(){
		$result	=	count($this->getErrors());
		return ($result ? TRUE : FALSE);
	}
	
	/**
	 * Return the current array of errors
	 * 
	 * @return array
	 */
	public function getErrors(){
		return $this->_errors;
	}
	
	/**
	 * Get the result of our operation. It will either return successful 
	 * string or an imploded string of error messages
	 * 
	 * @return string
	 */
	public function getResult(){
		if($this->hasError()){
			return $this->getErrorResponse(implode("|", $this->getErrors()));
		}
		return $this->getSuccessResponse();
	}
	
	/**
	 * Add an error to the array and immediately 
	 * return and exit. This should be called
	 * when an error is detected the prevents
	 * further processing by the order process
	 * 
	 * @param string $error
	 */
	public function addErrorAndExit($error){
		$this->addError($error);
		echo $this->getResult();
		exit;
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

		Mage::getModel('ordergroove/log')->createLog(self::LOG_ACTIVITY.' - '.$this->getOrdergrooveOrderId(), $type, $message);
	}
	
	/**
	 * Get the current ordergroove id
	 * 
	 * @return string
	 */
	public function getOrdergrooveOrderId(){
		return $this->getData('og_order_id');
	}
	
	/**
	 * Set the current ordergroove id
	 * 
	 * @param string
	 */
	public function setOrdergrooveOrderId($id){
		$this->setData('og_order_id', $id);
	}
	
	/**
	 * Return the RC4 encryptor
	 * 
	 * @return Pixafy_Ordergroove_Helper_Rc4
	 */
	protected function _getRc4Encryptor(){
		if(!$this->_rc4Encryptor){
			$this->_rc4Encryptor	=	Mage::helper('ordergroove/rc4');
		}
		return $this->_rc4Encryptor;
	}
	
	/**
	 * Return the configuration class
	 * 
	 * @return Pixafy_Ordergroove_Helper_Config
	 */
	protected function _getConfig(){
		if(!$this->_configClass){
			$this->_configClass	=	Mage::helper('ordergroove/config');
		}
		return $this->_configClass;
	}
	
	/**
	 * Return the log key
	 * 
	 * @return string
	 */
	protected function _getLogKey(){
		if(!$this->_logKey){
			$this->_logKey	=	time() . Mage::helper('core')->getRandomString(5);
		}
		return $this->_logKey;
	}
	 
}
?>
