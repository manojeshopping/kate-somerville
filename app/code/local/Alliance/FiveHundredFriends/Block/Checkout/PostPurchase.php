<?php

/**
* Class Alliance_FiveHundredFriends_Block_Checkout_PostPurchase
*
* Block used for displaying 500 Friends Post-Purchase Block
*
* This block uses 3 possibles scenarios:
*   - Post purchase page for customers who have not yet enrolled in the loyalty program. Template: alliance_fivehundredfriends/checkout/notenrolled.phtml
*   - Post purchase page for customers who previously enrolled in the loyalty program. Template: alliance_fivehundredfriends/checkout/enrolled.phtml
*/
class Alliance_FiveHundredFriends_Block_Checkout_PostPurchase extends Mage_Checkout_Block_Onepage_Success
{
	protected $_customerData;
	
	/**
	* Contruct to set the template.
	*
	*/
	public function __construct()
	{
		parent::__construct();
		
		if(! $this->_getCustomerSession()->isLoggedIn()) {
			$this->setTemplate('alliance_fivehundredfriends/checkout/notenrolled.phtml');
		} else {
			// Check if Customer is not restricted
			if(! Mage::helper('alliance_fivehundredfriends/data')->getCustomerRestriction()){
				// Check if user is enrolled.
				if($this->_checkCustomerEnrolled()) {
					$this->setTemplate('alliance_fivehundredfriends/checkout/enrolled.phtml');
				} else {
					$this->setTemplate('alliance_fivehundredfriends/checkout/notenrolled.phtml');
				}
			}
		}
	}
	
	/**
	* Gets Customer Email from session.
	*
	*/
	public function getCustomerEmail()
	{
		return $this->_getCustomerSession()->getCustomer()->getEmail();
	}
	
	/**
	* Gets Earned Points in current order.
	*
	*/
	public function getEarnedPoints()
	{
		$earnedPoints = Mage::getSingleton('core/session')->getUsedPoints();
		Mage::getSingleton('core/session')->unsUsedPoints();
		return $earnedPoints;
	}
	
	/**
	* Gets Total Points in current order.
	*
	*/
	public function getTotalPoints()
	{
		$customerData = $this->_getCustomerData();
		return $customerData['balance'];
	}
	
	/**
	* Gets customer data from redemption model.
	*
	*/
	protected function _getCustomerData()
	{
		if(! $this->_customerData) {
			$email = $this->_getCustomerSession()->getCustomer()->getEmail();
			$this->_customerData = $this->_getRedemptionModel()->getCustomerDataByEmail($email);
		}
		
		return $this->_customerData;
	}
	
	/**
	* Gets customer session.
	*
	*/
	protected function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}
	/**
	* Checks if customer is enrolled in Loyalty.
	*
	*/
	protected function _checkCustomerEnrolled()
	{
		$customerData = $this->_getCustomerData();
		return ($customerData && $customerData['status'] == 'active');
	}
	
	/**
	* Gets Redemption model.
	*
	*/
	protected function _getRedemptionModel()
	{
		return Mage::getModel('alliance_fivehundredfriends/redemption');
	}
}