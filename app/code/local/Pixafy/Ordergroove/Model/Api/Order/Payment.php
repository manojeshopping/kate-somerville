<?php
/**
 * Order API class that parses and returns the payment method to be used for the order
 * 
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method __construct()
 * @method extract()
 * @method _initPaymentData()
 * @method getPaymentData()
 * @method getPaymentMethod()
 * @method setPaymentMethod(string $method)
 * @method _initEncryptor()
 * @method getEncryptor()
 * @method _extractCcType()
 * @method _extractCcNumber()
 * @method _extractExpirationDate()
 * @method _setPaymentMethodOnQuote()
 * @method getErrorClass()
 */
class Pixafy_Ordergroove_Model_Api_Order_Payment extends Pixafy_Ordergroove_Model_Api_Order_Address{
	
	const KEY_CC_TYPE				=	'orderCcType';
	const KEY_CC_OWNER				=	'orderCcOwner';
	const KEY_CC_NUMBER				=	'orderCcNumber';
	const KEY_CC_EXPIRATION			=	'orderCcExpire';
	const ERROR_CC_TYPE_NOT_FOUND	=	'Credit card type could not be determined';
	
	/**
	 * RC4 Encryption class
	 * 
	 * @var Pixafy_Ordergroove_Helper_Rc4
	 */
	protected $_rc4;
	
	/**
	 * Credit card mappings array. Maps the
	 * OG value => Magento value
	 * 
	 * @var array
	 */
	protected $_ccMappings	=	array(
		'visa'				=>	'VI',
		'discover'			=>	'DI',
		'mastercard'		=>	'MC',
		'americanexpress'	=>	'AE',
		'amex'				=>	'AE'
	);
	
	/**
	 * The current payment method code
	 * 
	 * @var string
	 */
	protected $_currentPaymentMethod;
	
	/**
	 * Authorize.net payment method model code
	 * 
	 * @var string
	 */
	protected $_paymentMethodAuthorizenet;
	
	/**
	 * Payment object containing credit card data
	 * 
	 * @var Varien_Object
	 */
	protected $_paymentData;
	

	/**
	 * Constructor. Init the payment method codes and 
	 * set the current payment method to Authorize.net
	 */
	public function __construct(){
		$this->_paymentMethodAuthorizenet	=	Mage_Paygate_Model_Authorizenet::METHOD_CODE;
		$this->setPaymentMethod($this->_paymentMethodAuthorizenet);
	}
	
	/**
	 * Extract function called from external
	 * classes to parse payment data from XML
	 */
	public function extract(){
		$this->data	=	$this->getFeedData();
		$this->data	=	$this->data->head;
		
		/**
		 * Init encryptor class
		 */
		$this->_initEncryptor();
		
		/**
		 * Create and set the payment data
		 */
		$this->_initPaymentData();
		
		/**
		 * Add the payment method to the quote
		 */
		$this->_setPaymentMethodOnQuote();
		//$this->getPaymentData()->setCcNumber('4111111111111111');
		/**
		 * Assign payment data to quote
		 */
		
		if(Mage::registry(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_NEGATIVE_TOTAL_API_ORDER) == 1){
			$this->getQuote()->getPayment()->setMethod('free');
		}
		else{
			$this->getQuote()->getPayment()->importData($this->getPaymentData()->getData());
		}
	}
	
	/**
	 * Initialize, parse and set the payment data from the XML
	 * Set the payment method, credit card type, credit card number
	 * and credit card expiration data
	 */
	protected function _initPaymentData(){
		$this->_paymentData	=	new Varien_Object();
		
		$this->getPaymentData()->setMethod($this->getPaymentMethod());
		$this->getPaymentData()->setCcType((string)$this->_extractCcType());
		$this->getPaymentData()->setCcNumber($this->_extractCcNumber());
		$this->_extractExpirationDate();
	}
	
	/**
	 * Return the current payment data object
	 * 
	 * @return Varien_Object
	 */
	public function getPaymentData(){
		return $this->_paymentData;
	}
	
	/**
	 * Return the current payment method code
	 * 
	 * @return string
	 */
	public function getPaymentMethod(){
		if(Mage::registry(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_NEGATIVE_TOTAL_API_ORDER) == 1){
			return 'free';
		}
		return $this->_currentPaymentMethod;
	}
	
	/**
	 * Set the current payment method code
	 * 
	 * @param string $method
	 * @return Pixafy_Ordergroove_Model_Api_Order_Payment
	 */
	protected function setPaymentMethod($method){
		$this->_currentPaymentMethod	=	$method;
	}
	
	/**
	 * Instantiate the encryptor class
	 */
	protected function _initEncryptor(){
		$this->_rc4	=	Mage::helper('ordergroove/rc4');
	}
	
	/**
	 * Return the current encryptor class
	 * 
	 * @return Pixafy_Ordergroove_Helper_Rc4
	 */
	protected function getEncryptor(){
		return $this->_rc4;
	}
	
	/**
	 * Decrypt and extract the credit card type
	 * 
	 * @return string
	 */
	protected function _extractCcType(){
		$ccType		=	str_replace(" ", "", strtolower($this->extractField(self::KEY_CC_TYPE)));
		if(array_key_exists($ccType, $this->_ccMappings)){
			return $this->_ccMappings[$ccType];
		}
		$this->addErrorAndExit(self::ERROR_CC_TYPE_NOT_FOUND);
	}
	
	/**
	 * Decrypt and extract the credit card number
	 * 
	 * @return string
	 */
	protected function _extractCcNumber(){
		return $this->getEncryptor()->decrypt($this->extractField(self::KEY_CC_NUMBER));
	}
	
	/**
	 * Decrypt and extract the expiration date into a Magento readable format
	 */
	protected function _extractExpirationDate(){
		$expDate	=	$this->getEncryptor()->decrypt($this->extractField(self::KEY_CC_EXPIRATION));
		$expDate	=	explode("/", $expDate);
		$expYear	=	$expDate[1];
		
		$expMonth	=	$expDate[0];
		if(strlen($expMonth) == 2){
			if($expMonth{0} == "0"){
				$expMonth	=	$expMonth{1};
			}
		}
		$this->getPaymentData()->setCcExpMonth($expMonth);
		$this->getPaymentData()->setCcExpYear($expYear);
	}
	
	/**
	 * Sets the payment method to the billing or shipping address,
	 * depending on whether the quote is virtual or not
	 */
	protected function _setPaymentMethodOnQuote(){
		if ($this->getQuote()->isVirtual()){
			$this->getQuote()->getBillingAddress()->setPaymentMethod($this->getPaymentMethod());
		}
		else{
			$this->getQuote()->getShippingAddress()->setPaymentMethod($this->getPaymentMethod());
		}
	}
	
	/**
	 * Get the error class for the payment method
	 * 
	 * @return Pixafy_Ordergroove_Model_Api_Order_Payment_Authorizenet_Errorcodes
	 */
	public function getErrorClass(){
		return Mage::getModel('ordergroove/api_order_payment_authorizenet_errorcodes');
	}
}
?>
