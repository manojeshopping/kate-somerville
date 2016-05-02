<?php
/**
 * Class Alliance_FiveHundredFriends_Block_Redemption_Cart
 *
 * Redemption block used in cart page.
 */
class Alliance_FiveHundredFriends_Block_Redemption_Cart extends Mage_Checkout_Block_Onepage_Success
{
	protected $_customerData;
	protected $_offersApplied;
	
	public function __construct()
	{
		parent::__construct();
		
		if(! $this->_getCustomerSession()->isLoggedIn()) {
			$this->setTemplate('alliance_fivehundredfriends/redemption/rewards_notloggedin.phtml');
		} else {
			
			// Check if Customer is not restricted
			if(! Mage::helper('alliance_fivehundredfriends/data')->getCustomerRestriction()){
				// Check if user is enrolled.
				if($this->_checkCustomerEnrolled()) {	
					// Check if qualify for first reward.
					$firstReward = $this->getFirstReward();
					$customerData = $this->_getCustomerData();
					if((int)$customerData['balance'] > (int)$firstReward['points']) {
						$this->setTemplate('alliance_fivehundredfriends/redemption/rewards_enrolled.phtml');
					} else {
						$this->setTemplate('alliance_fivehundredfriends/redemption/rewards_not_qualify.phtml');
					}
				} else {
					$this->setTemplate('alliance_fivehundredfriends/redemption/rewards_notenroll.phtml');
				}
			}
		}
	}
	
	/**
	* Gets current points used by customer.
	*
	*/
	public function getCurrentPoints()
	{
		$customerData = $this->_getCustomerData();
		
		return $customerData['quote_balance'];
	}
	
	/**
	* Gets used points in cart.
	*
	*/
	public function getUsedPoints()
	{
		$customerData = $this->_getCustomerData();
		
		if($customerData['quote_used_points'] == 0 && $this->checkBirthdayApplied()) {
			$customerData['quote_used_points'] = 1;
		}
		
		return $customerData['quote_used_points'];
	}
	
	/**
	* Gets all available rewards.
	*
	*/
	public function getRewards()
	{
		$email = $this->_getCustomerSession()->getCustomer()->getEmail();
		$rewards = $this->_getRedemptionModel()->getRewards($email);
		
		return $rewards;
	}
	
	/**
	* Get the reward with minimum points.
	*
	*/
	public function getFirstReward()
	{
		$rewards = $this->getRewards();
		
		foreach($rewards as $_reward) {
			if(! isset($minPoints) || $minPoints > (int)$_reward['points']) {
				$reward = $_reward;
				$minPoints = (int)$_reward['points'];
			}
		}
		
		return $reward;
	}
	
	/**
	* Check if reward apply.
	*
	* @param $reward
	*/
	public function checkReward($reward)
	{
		if($this->isOffers($reward)) {
			if($this->checkOffersApplied()) return false;
		}
		if($this->isBirthday($reward)) {
			if($this->checkBirthdayApplied()) return false;
		}
		
		return ($reward['points'] < $this->getCurrentPoints());
	}
	
	/**
	* Check if reward is offers.
	*
	* @param $reward
	*/
	public function isOffers($reward)
	{	
		return $reward['configuration']['type'] == 'offers';
	}
	/**
	* Check if reward is birthday.
	*
	* @param $reward
	*/
	public function isBirthday($reward)
	{	
		return $reward['configuration']['type'] == 'birthday';
	}
	
	/**
	* Check if the quote has an offer reward applied.
	*
	*/
	public function checkOffersApplied()
	{
		if(! $this->_offersApplied) {
			$redemptionModel = $this->_getRedemptionModel();
			$redemptionModel->loadCurrentQuote();
			$this->_offersApplied = $redemptionModel->checkOffersApplied();
		}
		
		return $this->_offersApplied;
	}
	/**
	* Check if the quote has an birthday reward applied.
	*
	*/
	public function checkBirthdayApplied()
	{
		if(! $this->_birthdayApplied) {
			$redemptionModel = $this->_getRedemptionModel();
			$redemptionModel->loadCurrentQuote();
			$this->_birthdayApplied = $redemptionModel->checkBirthdayApplied();
		}
		
		return $this->_birthdayApplied;
	}
	
	/**
	* Get offers already applied to cart.
	*
	*/
	public function getAppliedRewards()
	{
		$redemptionModel = $this->_getRedemptionModel();
		$redemptionModel->loadCurrentQuote();
		
		$appliedRewards = $redemptionModel->getAppliedRewards();
		if($appliedRewards && $appliedRewards->count() == 0) return false;
		
		return $appliedRewards;
	}
	
	/**
	* Get calculation for how many points that customer could potentially earn.
	*
    * @return int
	*/
	public function getPotentiallyPoints()
	{
		// Get current quote.
		$quote = $this->_getQuote();
		
		// Get tax amount from address.
		$address = $quote->getShippingAddress();
		$taxAmount = $address ? $address->getTaxAmount() : 0;
		$shippingAmount = $address ? $address->getShippingAmount() : 0;
		
		$points = (int)($quote->getGrandTotal() - $taxAmount - $shippingAmount);
		
		
		return $points;
	}
	
	
	/**
	* Get logged in customer data from 500F.
	*
	*/
	protected function _getCustomerData()
	{
		if(! $this->_customerData) {
			// Get customer data from redemption model.
			$email = $this->_getCustomerSession()->getCustomer()->getEmail();
			$this->_customerData = $this->_getRedemptionModel()->getCustomerDataByEmail($email);
		}
		
		return $this->_customerData;
	}
	
	/**
	* Get customer session.
	*
	*/
	protected function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}
	/**
	* Check if logged in customer is enrolled in 500F.
	*
	*/
	protected function _checkCustomerEnrolled()
	{
		$customerData = $this->_getCustomerData();
		return ($customerData && $customerData['status'] == 'active');
	}
	
	/**
	* Get 500F API helper.
	*
	*/
	protected function _getFivehundredApi()
	{
		return Mage::helper('alliance_fivehundredfriends/api');
	}
	/**
	* Get Redemption model.
	*
	*/
	protected function _getRedemptionModel()
	{
		return Mage::getModel('alliance_fivehundredfriends/redemption');
	}
	
	/**
	* Retrieve quote model.
	*
	*/
	protected function _getQuote()
	{
		return Mage::getSingleton('checkout/session')->getQuote();
	}
}
