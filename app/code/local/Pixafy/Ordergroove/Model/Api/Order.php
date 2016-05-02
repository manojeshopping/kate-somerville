<?php
/**
 * OrderGroove Order API Class. This class will take XML that is sent by OG,
 * parse it to set the billing address, shipping address, shipping method and 
 * payment method. Each of this is set by an external class that interact
 * with this class.
 *
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method setFeedData(array $data)
 * @method getFeedData()
 * @method _hasNoData()
 * @method process()
 * @method getExtractor()
 * @method setBillingAddress(Mage_Sales_Model_Order_Address $addr)
 * @method _extractBillingAddress()
 * @method getBillingAddressClass()
 * @method setShippingAddress(Mage_Sales_Model_Order_Address $addr)
 * @method _extractShippingAddress()
 * @method getShippingAddressClass()
 * @method getCustomer()
 * @method _extractCustomer()
 * @method isGuestOrder()
 * @method _initQuote()
 * @method _reset()
 * @method _addProductsToQuote()
 * @method _getQuoteClass()
 * @method getQuote()
 * @method _saveQuote()
 * @method _extractTotals()
 * @method _extractPaymentMethod()
 * @method getPaymentMethodClass()
 * @method _extractShippingMethod()
 * @method getShippingMethodClass()
 * @method _setOrder(Mage_Sales_Model_Order $order)
 * @method _getOrder()
 * @method _placeOrder()
 * @method _setOrderCurrencyCodes()
 * @method _invoiceOrder()
 * @method getSuccessResponse()
 * @method getErrorResponse(string $messages)
 */
class Pixafy_Ordergroove_Model_Api_Order extends Pixafy_Ordergroove_Model_Api_Generic{
	
	/**
	 * Registry keys for data, customer, and quote
	 */
	const REGISTRY_KEY_DATA			=	'ordergroove_order_api_data';
	const REGISTRY_KEY_CUSTOMER		=	'ordergroove_order_api_customer';
	const REGISTRY_KEY_QUOTE		=	'ordergroove_order_api_quote';
	const REGISTRY_KEY_RAW_DATA		=	'ordergroove_order_api_raw_data';
	
	/**
	 * Billing Address
	 *
	 * @var Mage_Sales_Model_Quote_Address
	 */
	protected $_billingAddress;
	
	/**
	 * Shipping Address
	 *
	 * @var Mage_Sales_Model_Quote_Address
	 */
	protected $_shippingAddress;
	
	/**
	 * Currently loaded Magento customer
	 *
	 * @var Mage_Customer_Model_Customer
	 */
	protected $_customer;
	
	/**
	 * Billing Address
	 *
	 * @var Mage_Sales_Model_Quote
	 */
	protected $_quote;
	
	/**
	 * Set our data from the API that will handle all processing
	 * 
	 * @param	array $data
	 * @return	Pixafy_Ordergroove_Model_Api_Order
	 */
	public function setFeedData($data){
	    
		Mage::log('setFeedData(): data',null,'ordergroove_debug.log');
		Mage::log($data,null,'ordergroove_debug.log');    
		Mage::log('setFeedData(): REGISTRY_KEY_DATA',null,'ordergroove_debug.log');
		Mage::log(Mage::registry(self::REGISTRY_KEY_DATA),null,'ordergroove_debug.log');    
		Mage::log('setFeedData(): extract(data)',null,'ordergroove_debug.log');
		Mage::log($this->getExtractor()->extract($data),null,'ordergroove_debug.log');
	
		if(!Mage::registry(self::REGISTRY_KEY_DATA)){
			Mage::register(self::REGISTRY_KEY_RAW_DATA, $data['xml']);
			Mage::register(self::REGISTRY_KEY_DATA, $this->getExtractor()->extract($data));
		}
		return $this;
	}
	
	/**
	 * Return top level parsed data
	 * 
	 * @return stdClass Object
	 */
	public function getFeedData(){
		return Mage::registry(self::REGISTRY_KEY_DATA);
	}
	
	/**
	 * Return raw feed data for logging purposes
	 */
	public function getRawFeedData(){
		return Mage::registry(self::REGISTRY_KEY_RAW_DATA);
	}
	
	/**
	 * Checks to see if data has been parsed and loaded
	 * 
	 * @return boolean
	 */
	protected function _hasNoData(){
		if(!$this->getFeedData()){
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * High level function that will parse all data,
	 * create all order objects, add items to quote
	 * and convert quote into an order. This function
	 * can be called from any external class to begin
	 * the order creation process.
	 * 
	 * @throws Exception $e
	 */
	public function process(){
		try{
			//	Ensure that we have received data
			if($this->_hasNoData()){
				$this->addError(parent::ERROR_NO_DATA_RECEIVED);
				return;
			}
			
			/**
			 * Extract customer
			 */
			$this->_extractCustomer();
			
			/**
			 * Extract Ordergroove Order id
			 */
			$this->_extractOrderGrooveOrderId();
			
			/**
			 * Log the raw data that is received from OrderGroove
			 */
			$this->log(Mage::helper('ordergroove')->clearCcDataOrder($this->getRawFeedData()), Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_REQUEST);
			
			/**
			 * Instantiate quote
			 */
			$this->_initQuote();
			
			/**
			 * Add products to quote
			 */
			$this->_addProductsToQuote();
			
			/**
			 * Apply coupon to quote
			 */
			$this->_applyCoupon();
			
			/**
			 * Extract billing address
			 */
			$this->_extractBillingAddress();
			
			/**
			 * Extract shipping address
			 */
			$this->_extractShippingAddress();
			
			/**
			 * Extract shipping method
			 */
			$this->_extractShippingMethod();

			/**
			 * Extract payment method
			 */
			$this->_extractPaymentMethod();
			
			/**
			 * Extract totals
			 */
			$this->_extractTotals();
			
			/**
			 * Extract payment method
			 */
			$this->_extractPaymentMethod();
			
			/**
			 * Place order
			 */
			$this->_placeOrder();
			
			/**
			 * Set order currency codes
			 */
			$this->_setOrderCurrencyCodes();
			
			/**
			 * Invoice order
			 */
			$this->_invoiceOrder();
			
			/**
			 * Reset objects
			 */
			$this->_reset();
		}
		catch(Exception $e){
			echo $this->getErrorResponse($e->getMessage());
			exit;
		}
	}
	
	/**
	 * Return the extractor class that will handle parsing the 
	 * data at the highest level. For this implementation, it
	 * simply extracts the XML from the POST and converts it to
	 * a PHP friendly readable format
	 * 
	 * @return Pixafy_Ordergroove_Model_Api_Order_Extractor
	 */
	protected function getExtractor(){
		return Mage::getModel('ordergroove/api_order_extractor');
	}
	
	
	/**
	 * Set the parsed and created billing address object
	 * 
	 * @param Mage_Sales_Model_Order_Address $addr
	 */
	protected function setBillingAddress($addr){
		$this->_billingAddress	=	$addr;
	}
	
	/**
	 * Extract billing address data from XML. Set the address
	 * to this class as well as to our quote object
	 */
	protected function _extractBillingAddress(){
		$this->setBillingAddress($this->getBillingAddressClass()->extract());
		$this->getQuote()->setBillingAddress($this->_billingAddress);
	}
	
	/**
	 * Return our class that is used to create the billing address.
	 * This can be modified to another class for future implementations
	 * 
	 * @return Pixafy_Ordergroove_Model_Api_Order_Address_Billing
	 */
	protected function getBillingAddressClass(){
		return Mage::getModel('ordergroove/api_order_address_billing');
	}
	
	/**
	 * Set the parsed and created shipping address object
	 * 
	 * @param Mage_Sales_Model_Order_Address $addr
	 */
	protected function setShippingAddress($addr){
		$this->_shippingAddress	=	$addr;
	}
	
	/**
	 * Extract shipping address data from XML. Set the address
	 * to this class as well as to our quote object
	 */
	protected function _extractShippingAddress(){
		$this->setShippingAddress($this->getShippingAddressClass()->extract());
		$this->getQuote()->setShippingAddress($this->_shippingAddress);
	}
	
	/**
	 * Return our class that is used to create the billing address.
	 * This can be modified to another class for future implementations
	 * 
	 * @return Pixafy_Ordergroove_Model_Api_Order_Address_Shipping
	 */
	protected function getShippingAddressClass(){
		return Mage::getModel('ordergroove/api_order_address_shipping');
	}
	
	/**
	 * Return our current, loaded customer. This function
	 * uses the registry for other classes to easily access
	 * this object.
	 * 
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer(){
		return Mage::registry(self::REGISTRY_KEY_CUSTOMER);
	}
	
	/**
	 * Register the customer into the registry based on the customer id 
	 * in the XML. If no customer ID is found (guest order), the object
	 * is still set, however it will not have an entity id
	 */
	protected function _extractCustomer(){
		Mage::register(self::REGISTRY_KEY_CUSTOMER, $this->getExtractor()->extractCustomer($this->getFeedData()));
	}
	
	/**
	 * For guest orders, extract the email address directly
	 * from the XML instead of using the customer's email
	 * address.
	 */
	protected function _extractCustomerEmail(){
		return $this->getExtractor()->extractCustomerEmail($this->getFeedData());
	}
	
	/**
	 * Set the ordergroove order id
	 */
	protected function _extractOrderGrooveOrderId(){
		$this->setOrdergrooveOrderId($this->getExtractor()->extractOgOrderId($this->getFeedData()));
	}
	
	/**
	 * Determine whether the order is a guest order or not.
	 * Based on the _extractCustomer and getCustomer functions,
	 * this will return a boolean based on whether the custoemr
	 * has an entity id or not
	 * 
	 * @return boolean
	 */
	protected function isGuestOrder(){
		if($this->getCustomer()->getId()){
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * Initialize our quote object.
	 * This object will be placed into the registry
	 * and accessible from any class for easier 
	 * processing
	 */
	protected function _initQuote(){
		$quote	=	Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId())->setWebsiteId(Mage::app()->getWebsite()->getId());
		if(!$this->isGuestOrder()){
			$quote->assignCustomer($this->getCustomer());
		}
		else{
			$quote->setCustomerEmail($this->_extractCustomerEmail());
		}
		Mage::register(self::REGISTRY_KEY_QUOTE, $quote);
	}
	
	/**
	 * Reset the quote object. This should
	 * only be called after an order has been 
	 * placed. This will also reset all other 
	 * class objects so we can promise fresh
	 * classes each time data is processed
	 */
	protected function _reset(){
		Mage::unregister(self::REGISTRY_KEY_QUOTE);
		$this->setData('quote_class', NULL);
		//$this->setData('order', NULL);
		$this->setShippingAddress(NULL);
		$this->setBillingAddress(NULL);
		$this->_initQuote();
	}
	
	/**
	 * Call an external class to extract products
	 * and add to quote. This uses an external class
	 * so future implementations can change the way
	 * data is parsed and loaded without modifying
	 * this class
	 */
	protected function _addProductsToQuote(){
		$this->_getQuoteClass()->addProducts();
	}
	
	/**
	 * Calls the external quote class and will
	 * attempt to apply a coupon to the quote
	 * if one exists.
	 */
	protected function _applyCoupon(){
		$this->_getQuoteClass()->applyCoupon();
	}
	
	/**
	 * Return the quote object that is performing support 
	 * for quote functionality. This is NOT Mage_Sales_Model_Quote.
	 * This uses an external class so future implementations can 
	 * change the way data is processed without modifying this class.
	 * 
	 * @return Pixafy_Ordergroove_Model_Api_Order_Quotelite
	 */
	protected function _getQuoteClass(){
		if(!$this->getData('quote_class')){
			$this->setData('quote_class', Mage::getModel('ordergroove/api_order_quotelite'));
		}
		return $this->getQuoteClass();
	}
	
	/**
	 * Return the current quote object from the registry. This is
	 * in the registry so external and child classes can easily access
	 * the object that is currently being processed.
	 * 
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote(){
		return Mage::registry(self::REGISTRY_KEY_QUOTE);
	}
	
	/**
	 * Save the current quote object
	 */
	protected function _saveQuote(){
		$quote	=	$this->getQuote();
		$quote->save();
		Mage::unregister(self::REGISTRY_KEY_QUOTE);
		Mage::register(self::REGISTRY_KEY_QUOTE, $quote);
		
	}
	
	/**
	 * Extract the totals from the XML and apply to the
	 * quote object
	 * This uses an external class so future implementations can 
	 * change the way data is processed without modifying this class.
	 */
	protected function _extractTotals(){
		$this->_getQuoteClass()->extractTotals();
	}
	
	/**
	 * Build the payment method that will be used to process this order
	 */
	protected function _extractPaymentMethod(){
		$this->getPaymentMethodClass()->extract();
		//$this->getQuote()->getPayment()->importData();
	}
	
	/**
	 * Return the external payment class that will handle payment method logic.
	 * This uses an external class so future implementations can 
	 * change the way data is processed without modifying this class.
	 * 
	 * @return Pixafy_Ordergroove_Model_Api_Order_Payment
	 */
	protected function getPaymentMethodClass(){
		return Mage::getModel('ordergroove/api_order_payment');
	}
	
	/**
	 * Load the shipping method that will be used to ship this order (if order is not virtual)
	 */
	protected function _extractShippingMethod(){
		$method	=	$this->getShippingMethodClass()->extract();
		/**
		 * For tablerate, totals need to be collected
		 * because the total helps determine what
		 * rate is chosen
		 */
		if($method == 'tablerate_bestway'){
			$this->_extractTotals();
		}
		$this->getQuote()->getShippingAddress()->setShippingMethod($method)->setCollectShippingRates(true)->collectShippingRates();
	}
	
	/**
	 * Return the external shipping method class that will handle the logic.
	 * This uses an external class so future implementations can 
	 * change the way data is processed without modifying this class.
	 * 
	 * @return Pixafy_Ordergroove_Model_Api_Order_Shipment
	 */
	protected function getShippingMethodClass(){
		return Mage::getModel('ordergroove/api_order_shipment');
	}
	
	/**
	 * Set the order object that was created after
	 * submitting the quote
	 * 
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _setOrder($order){
		$this->setOrder($order);
	}
	
	/**
	 * Get the current order object
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder(){
		return $this->getOrder();
	}
	
	/**
	 * Create the service object based on the quote, and submit
	 * it to create the order
	 */
	protected function _placeOrder(){
		$service	=	Mage::getModel('sales/service_quote', $this->getQuote());
		$service->submitAll();
		$this->_setOrder($service->getOrder());
		
		if ($this->_getOrder()->getCanSendNewEmailFlag()) {
			try {
				$this->_getOrder()->sendNewOrderEmail();
			}
			catch (Exception $e) {
				Mage::logException($e);
				$this->log("Cannot send order email: ".$e->getMessage());
			}
		}
	}
	
	/**
	 * Set the currency codes for the order. The code is 
	 * extracted from the XML received from OrderGroove
	 */
	protected function _setOrderCurrencyCodes(){
		$this->_getOrder()->setBaseCurrencyCode($this->getQuoteClass()->getCurrencyCode());
		$this->_getOrder()->setGlobalCurrencyCode($this->getQuoteClass()->getCurrencyCode());
		$this->_getOrder()->setOrderCurrencyCode($this->getQuoteClass()->getCurrencyCode());
		$this->_getOrder()->setStoreCurrencyCode($this->getQuoteClass()->getCurrencyCode());
		$this->_getOrder()->save();
	}
	
	/**
	 * Create an invoice for the order object
	 */
	protected function _invoiceOrder(){
		if($this->_getOrder()->canInvoice()) {
			/**
			* Create invoice
			* The invoice will be in 'Pending' state
			*/
			$invoiceId	=	Mage::getModel('sales/order_invoice_api')->create($this->_getOrder()->getIncrementId(), array());
			$invoice 	=	Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);
	
			/**
			* Pay invoice
			* i.e. the invoice state is now changed to 'Paid'
			*/
			//$invoice->capture()->save();
			$invoice->save();
		}
	}
	
	/**
	 * Return the successful response. In this case
	 * it is XML with the SUCCESS code and the order id.
	 * The order is is the id that the customer sees, not
	 * the internal Magento entity id
	 * 
	 * @return string
	 */
	public function getSuccessResponse(){
		$xml= '<?xml version="1.0" encoding="UTF-8"?>
<order>
	<code>SUCCESS</code>
	<orderId>'.$this->_getOrder()->getIncrementId().'</orderId>
	<errorMsg />
</order>';
		$this->log($xml, Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_RESPONSE);
		$this->log("Order ".$this->_getOrder()->getIncrementId()." successfully created", Pixafy_Ordergroove_Helper_Constants::LOG_TYPE_SUCCESS);
		return $xml;
	}
	
	/**
	 * Return the error resposne. In this case
	 * it is XML with the ERROR code, and errorCode
	 * numerical value, and some errorMsg.
	 * 
	 * @param string $message
	 * @return string
	 */
	public function getErrorResponse($messages){
		$code	=	$this->getPaymentMethodClass()->getErrorClass()->getErrorCode($messages);
		if(!$code){
			$code	=	'999';
		}
		$xml= '<?xml version="1.0" encoding="UTF-8"?>
<order>
	<code>ERROR</code>
	<errorCode>'.$code.'</errorCode>
	<errorMsg>'.$messages.'</errorMsg>
</order>';
		$this->log($xml);
		return $xml;
	}
}
