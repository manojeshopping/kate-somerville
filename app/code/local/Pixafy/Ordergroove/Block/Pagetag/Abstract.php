<?php
/**
 * Parent pagetagging class. Contains several helper functions used
 * by all pagetagging blocks.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Pagetag_Abstract extends Mage_Core_Block_Template{
	
	/**
	 * Encryptor class
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
	 * Constructor. Init encryptor and config classes
	 */
	public function _construct(){
		$this->_encryptor	=	Mage::helper('ordergroove/rc4');
		$this->_config		=	Mage::helper('ordergroove/config');
	}
	
	/**
	 * Return the merchant id from the config class
	 * 
	 * @return string
	 */
	public function getMerchantId(){
		return $this->getConfig()->getMerchantId();
	}
	
	/**
	 * Return the encrypted merchant id
	 * 
	 * @return string
	 */
	public function getHashedMerchantId(){
		return $this->getEncryptor()->encrypt($this->getMerchantId());
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
	 * Return the customers current cart
	 * 
	 * @return Mage_Checkout_Model_Cart
	 */
	public function getCart(){
		return Mage::getSingleton('checkout/cart');
	}
	
	/**
	 * Return the customers current quote
	 * 
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote(){
		return $this->getCart()->getQuote();
	}
	
	/**
	 * Return the current OrderGroove session
	 * 
	 * @return Pixafy_Ordergroove_Model_Session
	 */
	public function getSession(){
		return Mage::getSingleton('ordergroove/session');
	}
	
	/**
	 * Return the current applicable address on the quote
	 * 
	 * @return Mage_Sales_Model_Quote_Address
	 */
	public function getAddress(){
		if($this->getQuote()->isVirtual()){
			return $this->getQuote()->getBillingAddress();
		}
		return $this->getQuote()->getShippingAddress();
	}
	
	/**
	 * Return configuration class
	 * 
	 * @return Pixafy_Ordergroove_Helper_Config
	 */
	public function getConfig(){
		return $this->_config;
	}
	
	/**
	 * Return the page tagging url
	 * 
	 * @return string
	 */
	public function getPageTaggingUrl(){
		return $this->getConfig()->getPageTaggingUrl();
	}
}
?>
