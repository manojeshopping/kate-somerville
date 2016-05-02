<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * Returns a dropdown of all the available 
 * shipping methods to choose from and assign
 * as the shipping method for OrderGroove
 * subscription orders.
 * 
 * @method toOptionArray()
 */
class Pixafy_Ordergroove_Model_Source_Order_Shippingmethod{
	
	/**
	 * Magento store config path values for usps, ups, and fedex
	 * available shipping methods
	 */
	const CONFIG_FLAG_USPS_METHODS	=	'carriers/usps/allowed_methods';
	const CONFIG_FLAG_UPS_METHODS	=	'carriers/ups/allowed_methods';
	const CONFIG_FLAG_FEDEX_METHODS	=	'carriers/fedex/allowed_methods';
	const CONFIG_FLAG_FLATRATE		=	'carriers/flatrate/active';
	const CONFIG_FLAG_TABLERATE		=	'carriers/tablerate/active';
	const CONFIG_FLAG_FREESHIP		=	'carriers/freeshipping/active';
	
	/**
	 * Carrier labels
	 */
	const LABEL_PREFIX_FEDEX		=	'FEDEX';
	const LABEL_PREFIX_USPS			=	'USPS';
	const LABEL_PREFIX_UPS			=	'UPS';
	
	/**
	 * Array of shipping methods to display
	 * 
	 * @var array Varien_Object[]
	 */
	protected $_methods;
	
	/**
	 * The current store of the configuration scope
	 * 
	 * @var Mage_Core_Model_Store
	 */
	protected $_store;
	
	/**
	 * Array of USPS available methods
	 * 
	 * @var array
	 */
	protected $_availUspsMethods	=	array();
	
	/**
	 * Array of FEDEX available methods
	 * 
	 * @var array
	 */
	protected $_availFedexMethods	=	array();
	
	/**
	 * Array of UPS available methods
	 * 
	 * @var array
	 */
	protected $_availUpsMethods		=	array();
	
	/**
	 * Return the calculated values that will
	 * be displayed in the system config dropdown.
	 * Magento will automatically call this function
	 * based on the model defined for the value
	 * 
	 * @return array
	 */
	public function toOptionArray()
	{
		$this->_loadStore();
		$this->_loadAvailableMethods();
		$this->_loadFlatrate();
		$this->_loadTablerate();
		$this->_loadFreeshipping();
		$this->_loadCarrierMethods();
		
		$options = array();
		foreach($this->_methods as $method){
			$options[] = array('value' => $method->getValue(), 'label' => $method->getLabel());
		}
		
		/* -- Begin : Code to Include Alliance Custom Shipping Allowed Methods to Order Groove Allowed Shipping Methods -- */
		$allianceCustomShippingMethods = Mage::helper('alliance_shipping')->getAllowedMethods();
		foreach ($allianceCustomShippingMethods as $key => $value) {
			$options[] =  array('value' => $key, 'label' => 'Alliance - '.$value);
		}
		/* -- End : Code to Include Alliance Custom Shipping Allowed Methods to Order Groove Allowed Shipping Methods -- */
		
		return $options;
	}
	
	/**
	 * Load the current store of the configuration scope. If no
	 * store is found, always use the first store of the loaded
	 * website
	 */
	protected function _loadStore(){
		if($storeCode = $this->getRequest()->getParam('store')){
			$this->_store	=	$this->getStoreModel()->load($storeCode);
			return;
		}
		else if($websiteCode	=	$this->getRequest()->getParam('website')){
			$website		=	$this->getWebsiteModel()->load($websiteCode, 'code');
			foreach($website->getStores() as $store){
				$this->_store	=	$this->getStoreModel()->load($store->getId());
				break;
			}
		}
		else{
			$helper	=	Mage::helper('ordergroove/installer');
			$this->_store	=	Mage::getModel('core/store')->load($helper->STORE_VIEW_CODE_ORDERGROOVE, 'code');
		}
	}
	
	/**
	 * Load the available methods for fedex, ups, and usps
	 */
	 
	protected function _loadAvailableMethods(){
		$this->_availFedexMethods	=	$this->_getAvailableMethodsByCarrier(self::CONFIG_FLAG_FEDEX_METHODS);
		$this->_availUpsMethods		=	$this->_getAvailableMethodsByCarrier(self::CONFIG_FLAG_UPS_METHODS);
		$this->_availUspsMethods	=	$this->_getAvailableMethodsByCarrier(self::CONFIG_FLAG_USPS_METHODS);
	}
	
	
	/**
	 * Load all available shipping methods for the OG website
	 * and present them as choices to assign as the shipping
	 * method for all OrderGroove subscription orders
	 */
	protected function _loadCarrierMethods(){
		$data['upsMethods']		=	$this->_getAvailableUpsMethods();
		$data['fedexMethods']	=	$this->_getAvailableFedexMethods();
		$data['uspsMethods']	=	$this->_getAvailableUspsMethods();
		
		foreach($data as $methodTypes => $methods){
			foreach($methods as $method){
				$this->_addMethod($method);
			}
		}
	}
	
	/**
	 * Add a shipping methods to the list of
	 * methods that will be displayed
	 * 
	 * @param Varien_Object
	 */
	protected function _addMethod($obj){
		$this->_methods[]	=	$obj;
	}
	
	/**
	 * Return the FEDEX methods that will be displayed
	 * 
	 * @return array
	 */
	protected function _getAvailableFedexMethods(){
		return $this->_getAvailableMethods(Mage::getSingleton('usa/shipping_carrier_fedex')->getCode('method'), $this->_availFedexMethods, self::LABEL_PREFIX_FEDEX);
	}
	
	/**
	 * Return the UPS methods that will be displayed
	 * 
	 * @return array
	 */
	protected function _getAvailableUpsMethods(){
		return $this->_getAvailableMethods(Mage::getSingleton('usa/shipping_carrier_ups')->getCode('method'), $this->_availUpsMethods, self::LABEL_PREFIX_UPS);
	}
	
	/**
	 * Return the USPS methods that will be displayed. USPS
	 * stores the values for its service levels in the value
	 * instead of key of the array, so we need to pass in the
	 * reverse lookup boolean of true
	 * 
	 * @return array
	 */
	protected function _getAvailableUspsMethods(){
		return $this->_getAvailableMethods(Mage::getSingleton('usa/shipping_carrier_usps')->getCode('method'), $this->_availUspsMethods, self::LABEL_PREFIX_USPS, TRUE);
	}
	
	/**
	 * Given an array of methods for a carrier, the available methods for that carrier,
	 * and the label for the carrier, create an array of available methods and return
	 * 
	 * @param array $methodsArray (array of all shipping methods for a carrier)
	 * @param array $availableMethodsArray (array of available methods for that carrier)
	 * @param string $labelPrefix
	 * @param boolean $reverseLookup (optional, defaults to FALSE)
	 * @return array
	 */
	protected function _getAvailableMethods($methodsArray, $availableMethodsArray, $labelPrefix, $reverseLookup=FALSE){
		$objects		=	array();
		foreach ($methodsArray as $k=>$v) {
			/**
			 * USPS store its values in the $v field
			 * instead of the $k field. So we need
			 * to check if the reverse lookup
			 * flag is set.
			 */
			if(in_array(($reverseLookup ? $v : $k), $availableMethodsArray)){
				if($labelPrefix == self::LABEL_PREFIX_UPS){
					$k = 'ups_'.$k;
				}
				$objects[]	=	$this->_createMethodObject(($reverseLookup ? $v : $k), $labelPrefix.' - '.$v);
			}
		}
		return $objects;
	}
	
	/**
	 * Create the individual method object based
	 * on value and label.
	 * 
	 * @param string $value
	 * @param string $label
	 * 
	 * @return Varien_Object
	 */
	protected function _createMethodObject($value, $label){
		return new Varien_Object(array('value' => $value, 'label' => $label));
	}
	
	/**
	 * Return the available shipping methods
	 * based on a store configuration path 
	 * 
	 * @param string $configCode
	 * @return array
	 */
	protected function _getAvailableMethodsByCarrier($configCode){
		return explode(',', Mage::getStoreConfig($configCode, $this->_store));
	}
	
	/**
	 * Return Magento request object
	 * 
	 * @return Mage_Core_Controller_Request_Http
	 */
	public function getRequest(){
		return Mage::app()->getRequest();
	}
	
	/**
	 * Return store model
	 * 
	 * @return Mage_Core_Model_Store
	 */
	public function getStoreModel(){
		return Mage::getModel('core/store');
	}
	
	/**
	 * Return website model
	 * 
	 * @return Mage_Core_Model_Website
	 */
	public function getWebsiteModel(){
		return Mage::getModel('core/website');
	}
	
	/**
	 * Check if flat rate is enabled, if 
	 * so add to array of methods
	 */
	protected function _loadFlatrate(){
		if(Mage::getStoreConfig(self::CONFIG_FLAG_FLATRATE, $this->_store)){
			$this->_addMethod($this->_createMethodObject('flatrate_flatrate', 'Flat Rate'));
		}
	}
	
	/**
	 * Check if table rate is enabled,
	 * if so add to array of methods
	 */
	protected function _loadTablerate(){
		if(Mage::getStoreConfig(self::CONFIG_FLAG_TABLERATE, $this->_store)){
			$this->_addMethod($this->_createMethodObject('tablerate_bestway', 'Table Rate'));
		}
	}
	
	/**
	 * Load the free shipping method if enabled
	 */
	protected function _loadFreeshipping(){
		if(Mage::getStoreConfig(self::CONFIG_FLAG_FREESHIP, $this->_store)){
			$this->_addMethod($this->_createMethodObject('freeshipping_freeshipping', 'Free Shipping'));
		}
	}
}
