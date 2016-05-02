<?php
/**
 * Helper class that hooks into the configuration values set in the Magento admin.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Config extends Mage_Core_Helper_Abstract{
	
	/*
		Configuration key constants. These keys are used to
		pull the values from the system configuration
	*/
	 
	/**
	 * Initial order incentive keys
	 */
	const CONFIG_KEY_IOI_DISCOUNT_TYPE				=	'ordergroove/ioi/discount_type';
	const CONFIG_KEY_IOI_DISCOUNT_LABEL				=	'ordergroove/ioi/total_label';
	const CONFIG_KEY_IOI_FREESHIPPING_THRESHOLD		=	'ordergroove/ioi/free_shipping_threshold';
	const CONFIG_KEY_IOI_FREESHIPPING_METHODS		=	'ordergroove/ioi/free_shipping_methods';
	const CONFIG_KEY_IOI_FREESHIPPING_ACTIVE		=	'ordergroove/ioi/free_shipping_active';
	
	/**
	 * Keys to disable certain functionality when og_autoship is set
	 */
	const CONFIG_KEY_FUNC_CHECK_CHECKMO				=	'ordergroove/functionality_to_disable/checkmo';
	const CONFIG_KEY_FUNC_CHECK_CCSAVE				=	'ordergroove/functionality_to_disable/ccsave';
	const CONFIG_KEY_FUNC_CHECK_CASH_ON_DELIVERY	=	'ordergroove/functionality_to_disable/cod';
	const CONFIG_KEY_FUNC_CHECK_BANK_TRANSFER		=	'ordergroove/functionality_to_disable/bank_transfer';
	const CONFIG_KEY_FUNC_CHECK_ZERO_SUBTOTAL		=	'ordergroove/functionality_to_disable/free';
	const CONFIG_KEY_FUNC_CHECK_ZERO_PURCHASE_ORDER	=	'ordergroove/functionality_to_disable/purchase_order';
	const CONFIG_KEY_FUNC_CHECK_GUEST_CHECKOUT		=	'ordergroove/functionality_to_disable/guest_checkout';
	const CONFIG_KEY_FUNC_CHECK_INT_BILLING_ADDRESS =	'ordergroove/functionality_to_disable/international_billing_addresses';
	const CONFIG_KEY_FUNC_CHECK_INT_SHIPPING_ADDRESS=	'ordergroove/functionality_to_disable/international_shipping_addresses';
	const CONFIG_KEY_FUNC_CHECK_PAYPAL_EXPRESS		=	'ordergroove/functionality_to_disable/paypal_express';
	const CONFIG_KEY_FUNC_CHECK_PAYPAL_STANDARD		=	'ordergroove/functionality_to_disable/paypal_standard';
	const CONFIG_KEY_FUNC_CHECK_PAYPAL_PAYFLOW_PRO	=	'ordergroove/functionality_to_disable/paypal_payflowpro';
	const CONFIG_KEY_FUNC_CHECK_PAYPAL_PAYFLOW_LINK	=	'ordergroove/functionality_to_disable/paypal_payflowlink';
	const CONFIG_KEY_FUNC_CHECK_SHIP_MULTI_ADDRESS	=	'ordergroove/functionality_to_disable/ship_to_multiple_addresses';
	const CONFIG_KEY_FUNC_CHECK_REMOVE_COUNTRIES	=	'ordergroove/functionality_to_disable/remove_countries_from_dropdown';
	
	/**
	 * Message to display when specific address types are disabled.
	 */
	const CONFIG_KEY_INT_BILL_DISABLED_MESSAGE		=	'ordergroove/functionality_to_disable/international_billing_addresses_message';
	const CONFIG_KEY_INT_SHIP_DISABLED_MESSAGE		=	'ordergroove/functionality_to_disable/international_shipping_addresses_message';
	
	/**
	 * Constants relating to the zero total cc payment method
	 */
	const CONFIG_KEY_ZERO_TOTAL_CC_TITLE			=	'payment/og_zerototalcc/title';
	const CONFIG_KEY_ZERO_TOTAL_CC_ENABLED			=	'payment/og_zerototalcc/active';
	
	/**
	 * Configuration keys for the customer update API
	 */
	const CONFIG_KEY_CUSTOMER_UPDATE_API_ENAILED	=	'ordergroove/customer_update_api/enabled';
	const CONFIG_KEY_CUSTOMER_UPDATE_API_POST_URL	=	'ordergroove/customer_update_api/post_url';
	
	/**
	 * Configuration keys for page tagging
	 */
	const CONFIG_KEY_OG_PAGETAGE_PRODUCTS			=	'ordergroove/pagetagging/product_page';
	const CONFIG_KEY_OG_PAGETAGE_CART				=	'ordergroove/pagetagging/cart_page';
	const CONFIG_KEY_OG_PAGETAGE_CHECKOUT_REVIEW	=	'ordergroove/pagetagging/checkout_review_page';
	const CONFIG_KEY_OG_PAGETAGE_CHECKOUT_SUCCESS	=	'ordergroove/pagetagging/checkout_success_page';
	
	/**
	 * Return the image type to be sent during product feed
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getProductFeedImageType($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig('ordergroove/product_feed/image_type', $store);
	}
	
	/**
	 * Return the default image dimensions for a specific image type
	 * 
	 * @param string $imageType (small_image | thumbnail | image)
	 * @return int
	 */
	public function getDefaultImageDimensions($imageType){
		switch($imageType){
			case 'small_image':
				return 135;
				break;
			case 'thumbnail':
				return 75;
				break;
			case 'image':
				return 265;
				break;
			default:
				return 265;
		}
	}
	
	/**
	 * Return the OrderGroove merchant id
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getMerchantId($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig('ordergroove/configuration/merchant_id', $store);
	}
	
	/**
	 * Return the OrderGroove hash key
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getHashKey($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig('ordergroove/configuration/hash_key', $store);
	}
	
	/**
	 * Return the specified FTP server that
	 * the product feed will upload to.
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getFtpServer($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig('ordergroove/product_feed/ftp_server', $store);
	}
	
	/**
	 * Return the specified FTP username
	 * that will be used to upload the 
	 * product feed
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getFtpUsername($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig('ordergroove/product_feed/ftp_username', $store);
	}
	
	/**
	 * Return the FTP password that will be used
	 * to upload the product feed
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getFtpPassword(){
		return Mage::getStoreConfig('ordergroove/product_feed/ftp_password');
	}
	
	public function getForceError(){
		return Mage::getStoreConfig('ordergroove/order_api/force_error');
	}
	
	public function getForceErrorAmount(){
		return Mage::getStoreConfig('ordergroove/order_api/error_amount');
	}
	
	/**
	 * Return the URL that subscription
	 * creation will POST to
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string | url
	 */
	public function getSubscriptionPostUrl($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig('ordergroove/subscriptions/post_url', $store);
	}
	
	/**
	 * Return the page tagging base url
	 * 
	 * @return string
	 */
	public function getPageTaggingUrl(){
		return Mage::getStoreConfig('ordergroove/pagetagging/base_url');
	}
	
	/**
	 * Return the IOI discount type
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getIoiDiscountType($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_IOI_DISCOUNT_TYPE, $store);
	}
	
	/**
	 * Return the IOI discount label
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getIoiDiscountLabel($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_IOI_DISCOUNT_LABEL, $store);
	}
	
	/**
	 * Return the free shipping threshold
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return float
	 */
	public function getIoiFreeshippingThreshold($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		$threshold = Mage::getStoreConfig(self::CONFIG_KEY_IOI_FREESHIPPING_THRESHOLD, $store);
		if(!$threshold || !is_numeric($threshold)){
			$threshold	=	0.00;
		}
		return $threshold;
	}
	
	/**
	 * Return the shipping methods for IOI free shipping
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return array
	 */
	public function getIoiFreeshippingMethods($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		$methods	=	explode(',', Mage::getStoreConfig(self::CONFIG_KEY_IOI_FREESHIPPING_METHODS, $store));
		foreach($methods as $k => $v){
			$methods[$k] = trim($v);
		}
		return $methods;
	}
	
	/**
	 * Return whether or not free shipping is enabled
	 * when the og_autoship cookie is present
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isIoiFreeshippingEnabled($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_IOI_FREESHIPPING_ACTIVE, $store);
	}
	
	/**
	 * Return whether check/money order is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckCheckMoneyOrder($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_CHECKMO, $store);
	}
	
	/**
	 * Return whether saved cc is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckSavedCc($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_CCSAVE, $store);
	}
	
	/**
	 * Return whether cash on delivery is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckCashOnDelivery($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_CASH_ON_DELIVERY, $store);
	}
	
	/**
	 * Return whether bank transfer is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckBankTransfer($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_BANK_TRANSFER, $store);
	}
	
	/**
	 * Return whether zero subtotal checkout is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckZeroSubtotal($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_ZERO_SUBTOTAL, $store);
	}
	
	/**
	 * Return whether purchase order is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckPurchaseOrder($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_ZERO_PURCHASE_ORDER, $store);
	}
	
	/**
	 * Return whether guest checkout is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckGuestCheckout($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_GUEST_CHECKOUT, $store);
	}
	
	/**
	 * Return whether international billing addresses are allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckInternationalBillingAddresses($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_INT_BILLING_ADDRESS, $store);
	}
	
	/**
	 * Return whether international shipping addresses are allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckInternationalShippingAddresses($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_INT_SHIPPING_ADDRESS, $store);
	}
	
	/**
	 * Return whether PayPal Express is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckPayPalExpress($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_PAYPAL_EXPRESS, $store);
	}
	
	/**
	 * Return whether PayPal Standard is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckPayPalStandard($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_PAYPAL_STANDARD, $store);
	}
	
	/**
	 * Return whether PayPal Payflow Pro is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckPayPalPayflowPro($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_PAYPAL_PAYFLOW_PRO, $store);
	}
	
	/**
	 * Return whether PayPal Payflow Link is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckPayPalPayflowLink($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_PAYPAL_PAYFLOW_LINK, $store);
	}
	
	/**
	 * Return whether PayPal Payflow Link is allowed when og_autoship is set
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function functionalityCheckShipToMultipleAddresses($store=''){
		return $this->_functionalityCheck(self::CONFIG_KEY_FUNC_CHECK_SHIP_MULTI_ADDRESS, $store);
	}
	
	/**
	 * Return whether certain functionality is disabled based on
	 * the values set in the system configuration when og_autoship
	 * is set.
	 * 
	 * @param string $configKey
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	protected function _functionalityCheck($configKey, $store=''){
		
		/**
		 * If no og_autoship cookie, always return true.
		 */
		if(!Mage::getSingleton('ordergroove/session')->hasAutoshipItems()){
			return TRUE;
		}
		
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		$check	=	Mage::getStoreConfig($configKey, $store);
		if($check == Pixafy_Ordergroove_Helper_Constants::FUNCTIONALITY_CHECK_DISABLED){
			return FALSE;
		}
		else if($check == Pixafy_Ordergroove_Helper_Constants::FUNCTIONALITY_CHECK_ENABLED){
			return TRUE;
		}
		return TRUE;
	}
	
	/**
	 * Return the message for when an international billing address is disabled
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getInternationalBillingAddressDisabledMessage($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_INT_BILL_DISABLED_MESSAGE, $store);
	}
	
	/**
	 * Return the message for when an international shipping address is disabled
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getInternationalShippingAddressDisabledMessage($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_INT_SHIP_DISABLED_MESSAGE, $store);
	}
	
	/**
	 * Return the zero total cc payment method title
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getZerototalccTitle($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_ZERO_TOTAL_CC_TITLE, $store);
	}
	
	/**
	 * Return whether or not the custom zero total credit card
	 * method is active.
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function getZerototalccEnabled($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_ZERO_TOTAL_CC_ENABLED, $store);
	}
	
	/**
	 * Return whether or not to remove countries from the dropdown
	 * 
	 * @return boolean
	 */
	public function removeCountriesFromDropdown(){
		if(Mage::getSingleton('ordergroove/session')->hasAutoshipItems() && Mage::getStoreConfig(self::CONFIG_KEY_FUNC_CHECK_REMOVE_COUNTRIES)){
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Return whether or not the customer update API is enabled
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isCustomerUpdateApiEnabled($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_CUSTOMER_UPDATE_API_ENAILED, $store);
	}
	
	/**
	 * Return the customer update API post URL
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getCustomerUpdateApiPostUrl($store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig(self::CONFIG_KEY_CUSTOMER_UPDATE_API_POST_URL, $store);
	}
	
	/**
	 * Return whether page tagging is enabled on the product page
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isProductPagePagetagEnabled($store=''){
		return $this->getConfigValue(self::CONFIG_KEY_OG_PAGETAGE_PRODUCTS, $store);
	}
	
	/**
	 * Return whether page tagging is enabled on the cart
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isCartPagetagEnabled($store=''){
		return $this->getConfigValue(self::CONFIG_KEY_OG_PAGETAGE_CART, $store);
	}
	
	/**
	 * Return whether page tagging is enabled on the checkout review page
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isCheckoutReviewPagetagEnabled($store=''){
		return $this->getConfigValue(self::CONFIG_KEY_OG_PAGETAGE_CHECKOUT_REVIEW, $store);
	}
	
	/**
	 * Return whether page tagging is enabled on the checkout success page
	 * 
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isCheckoutSuccessPagetagEnabled($store=''){
		return $this->getConfigValue(self::CONFIG_KEY_OG_PAGETAGE_CHECKOUT_SUCCESS, $store);
	}
	
	/**
	 * Return a store configuration value based on key and store
	 * 
	 * @param string $key
	 * @param Mage_Core_Model_Store $store | optional
	 * @return mixed
	 */
	public function getConfigValue($key, $store=''){
		if(!$store){
			$store	=	Mage::app()->getStore();
		}
		return Mage::getStoreConfig($key, $store);
	}
}
