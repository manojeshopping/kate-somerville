<?php
/**
 * Recurring subscriptions block. Hook this page up to OrderGroove via page tagging
 * to view all of a customers current and former subscriptions.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Pagetag_Subscriptions extends Mage_Core_Block_Template{
	const QUERY_STRING_PARAM_OG_USER_ID			=	'og_user_id';
	const QUERY_STRING_PARAM_OG_USER_ID_HASH	=	'og_user_hash';
	const QUERY_STRING_PARAM_OG_USER_EMAIL_HASH	=	'og_user_email_hash';
	
	/**
	 * A dummy customer object to be used for rendering
	 * guest MSI pages. Will have the id field set to it.
	 * 
	 * @var Varien_Object
	 */
	protected $_dummyCustomer;
	
	/**
	 * The OG user id as pulled in from the query string
	 * 
	 * @var string
	 */
	protected $_ogUserId;
	
	/**
	 * The OG user id hash as pulled in from the query string
	 * 
	 * @var string
	 */
	protected $_ogUserIdHash;
	
	/**
	 * Indicates whether the subscription request is
	 * in guest mode.
	 * 
	 * @var boolean
	 */
	protected $isGuestMode = FALSE;
	
	/**
	 * Constructor. Determine
	 * if the user is in guest mode
	 */
	public function _construct(){
		if($this->isGuestMode()){
			
		}
	}
	
	/**
	 * Prepare layout function, set template
	 */
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('ordergroove/pagetag/subscriptions.phtml');
	}
	
	
	/**
	 * Return the current customer. This could be a real 
	 * Magento customer model or a dummy customer object
	 * depending on the URL of the page. If specific parameters
	 * are found, it will be determined to be a guest MSI page
	 * and will therefore return data from the URL.
	 * 
	 * @return mixed
	 */
	protected function _getCustomer(){
		if($this->isGuestMode()){
			return $this->_getDummyCustomer();
		}
		else{
			return $this->_getSessionCustomer();
		}
	}
	
	/**
	 * Return whether or not that the MSI page
	 * is currently in guest mode
	 * 
	 * @return boolean
	 */
	public function isGuestMode(){
		$this->_ogUserId		=	$this->_fetchParamFromGet(self::QUERY_STRING_PARAM_OG_USER_ID);
		$this->_ogUserIdHash	=	$this->_fetchParamFromGet(self::QUERY_STRING_PARAM_OG_USER_ID_HASH);
		if($this->_ogUserId && $this->_ogUserIdHash){
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Return the customer model from the session
	 * 
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _getSessionCustomer(){
		return Mage::getSingleton('customer/session')->getCustomer();
	}
	
	/**
	 * Return the dummy customer object for guest msi pages
	 * 
	 * @return Varien_Object
	 */
	protected function _getDummyCustomer(){
		if(!$this->_dummyCustomer instanceof Varien_Object){
			$this->_dummyCustomer	=	new Varien_Object();
			$this->_dummyCustomer->setId($this->_ogUserId);
			$this->_dummyCustomer->setHashedOgId($this->_ogUserIdHash);
		}
		return $this->_dummyCustomer;
	}
	
	/**
	 * Fetch a parameter from the $_GET supervariable
	 * based on a specific key.
	 * 
	 * @param mixed $key
	 * @return mixed
	 */
	protected function _fetchParamFromGet($key){
		$value	=	'';
		if(array_key_exists($key, $_GET)){
			$value = urldecode($_GET[$key]);
		}
		return $value;
	}
	
	/**
	 * Check and see if the encrypted version of the id
	 * is equal to the hash id pulled from the query string
	 * 
	 * @return boolean
	 */
	protected function _ogEncryptionMatch(){
		return ($this->_ogUserIdHash == Mage::helper('ordergroove/rc4')->encrypt($this->_ogUserId) ? TRUE : FALSE);
	}
}
