<?php

/**
 * Class Alliance_FiveHundredFriends_Model_Redemption
 *
 * Redemption model
 */
class Alliance_FiveHundredFriends_Model_Redemption extends Mage_Core_Model_Abstract
{
	protected $_rewardsData;

	/**
	* Init resource model
	*/
	protected function _construct()
	{
		$this->_init('alliance_fivehundredfriends/redemption');
	}
	
	
	public function loadCurrentQuote()
	{
		$this->load($this->_getQuote()->getId(), 'quote_id');
	}
	
	public function getReward($rewardId)
	{
		$rewards = $this->getRewards();
		if(! $rewards || ! isset($rewards[$rewardId])) return false;
		
		return $rewards[$rewardId];
	}
	
	/**
	* Gets Rewards availables for customer.
	*
	* @param $customerEmail
    * @return array
	*/
	public function getRewards($customerEmail = null)
	{
		if(! $this->_rewardsData) {
			$apiHelper = $this->_getFivehundredApi();
			$rewardsResponse = $apiHelper->getRewards();
			
			if(! $rewardsResponse['success']) return false;
			
			// Get configuration.
			$relations = $this->_getFivehundredHelper()->getRelationsConfiguration();
			
			// Generate a rewards array ordered by reward id and with the configuration data.
			$rewards = array();
			$customerRewards = array();
			foreach($rewardsResponse['data'] as $_rewardKey => $_rewardData) {
				// Ignore disabled by configuration and by API rewards.
				if(
					isset($relations['type']) && isset($relations['type'][$_rewardData['id']]) && $relations['type'][$_rewardData['id']] == 'disabled'
				) {
					continue;
				}
				
				// If type is birthday, check availability.
				if($relations['type'][$_rewardData['id']] == "birthday" && ! is_null($customerEmail)) {
					if(empty($customerRewards)) {
						$customerRewards = $this->getCustomerRewards($customerEmail);
					}
					
					if(! $customerRewards || ! isset($customerRewards[$_rewardData['id']])) continue;
				}
				
				// Merge API data with configuration.
				$rewards[$_rewardData['id']] = array_merge($_rewardData, array(
					'configuration' => array(
						'type' => $relations['type'][$_rewardData['id']],
						'value' => (isset($relations['value'][$_rewardData['id']])) ? $relations['value'][$_rewardData['id']] : 0,
					),
				));
			}
			
			$this->_rewardsData = $rewards;
		}
		
		return $this->_rewardsData;
	}
	
	/**
	* Gets customer data by email. Also, calculates the quote balance with reedem points of current quote.
	*
	* @param $customerEmail
    * @return array
	*/
	public function getCustomerDataByEmail($customerEmail)
	{
		$apiHelper = $this->_getFivehundredApi();
		
		$request_parameters = array('email' => $customerEmail);
		$customerData = $apiHelper->customerShow($request_parameters);
		
		if(! $customerData['success']) return false;
		
		// Get used redeem points.
		$this->loadCurrentQuote();
		$redeemPoints = $this->getRedeemPoints();
		
		// Set quote_balance.
		$customerData['data']['quote_balance'] = $customerData['data']['balance'] - $redeemPoints;
		$customerData['data']['quote_used_points'] = $redeemPoints;
		
		return $customerData['data'];
	}
	
	/**
	* Gets customer rewards by email.
	*
	* @param $customerEmail
    * @return array
	*/
	public function getCustomerRewards($customerEmail)
	{
		$apiHelper = $this->_getFivehundredApi();
		
		$request_parameters = array('email' => $customerEmail);
		$customerRewards = $apiHelper->customerRewards($request_parameters);
		
		if(! $customerRewards['success']) return false;
		
		$rewards = array();
		foreach($customerRewards['data'] as $_reward) {
			$rewards[$_reward['id']] = $_reward;
		}
		
		return $rewards;
	}
	
	/**
	* Gets a collection with all rewards applied to quote id.
	*
    * @return collection
	*/
	public function getAppliedRewards()
	{
		// Check if quote is loaded.
		if(! $this->getId()) {
			return false;
		}
		
		$itemModel = $this->_getRedemptionItemModel();
		$itemCollection = $itemModel->getCollection()->addFieldToFilter('redeem_id', $this->getId());
		
		return $itemCollection;
	}
	
	/**
	* Gets sum of points added to quote.
	*
    * @return integer
	*/
	public function getRedeemPoints()
	{
		$collection = $this->getAppliedRewards();
		if(! $collection || $collection->getSize() == 0) return 0;
		
		$collection->getSelect()
			->reset(Zend_Db_Select::COLUMNS)
			->columns('SUM(redeem_points) as total')
		;
		
		return $collection->getFirstItem()->getTotal();
	}
	
	/**
	* Gets sum of discount added to quote.
	*
    * @return decimal
	*/
	public function getTotalDiscount()
	{
		$collection = $this->getAppliedRewards();
		if(! $collection) return false;
		
		// Only discount type.
		$collection->addFieldToFilter('reward_type', 'discount');
		if($collection->getSize() == 0) return false;
		
		$collection->getSelect()
			->reset(Zend_Db_Select::COLUMNS)
			->columns('SUM(discount_amount) as total')
		;
		
		return $collection->getFirstItem()->getTotal();
	}
	
	/**
	* Check if the offers type was already applied in quote.
	*
    * @return bool
	*/
	public function checkOffersApplied()
	{
		$collection = $this->getAppliedRewards();
		if(! $collection) return false;
		
		$collection->addFieldToFilter('reward_type', 'offers');
		if($collection->getSize() == 0) return false;
		
		return true;
	}
	
	/**
	* Check if the birthday type was already applied in quote.
	*
    * @return bool
	*/
	public function checkBirthdayApplied()
	{
		$collection = $this->getAppliedRewards();
		if(! $collection) return false;
		
		$collection->addFieldToFilter('reward_type', 'birthday');
		if($collection->getSize() == 0) return false;
		
		return true;
	}
	
	/**
	* Remove all redeem items for loaded quote.
	*
    * @return bool
	*/
	public function removeRedeemItems()
	{
		// Check if quote is loaded.
		if(! $this->getId()) {
			return false;
		}
		
		$collection = $this->getAppliedRewards();
		if(! $collection) return false;
		foreach($collection as $_item) {
			$_item->delete();
		}
		
		// Update redeem total.
		$this->setRedeemPoints(0)->save();
		
		return true;
	}
	
	/**
	* Trigger a reward redemption in 500F API.
	*
    * @return array
	*/
	public function rewardRedemption()
	{
		$apiHelper = $this->_getFivehundredApi();
		
		$collection = $this->getAppliedRewards();
		if(! $collection) return false;
		
		foreach($collection as $_item) {
			$request_parameters = array(
				'email' => $this->getCustomerEmail(),
				'reward_id' => $_item->getRewardId(),
				'event_id' => $_item->getId(),
			);
			$response = $apiHelper->rewardRedemption($request_parameters);
			if(! $response['success']) {
				Mage::log("rewardRedemption - error code: ".$response['data']['code']." - error message: ".$response['data']['message']." - qute id: ".$this->getQuoteId().".", null, 'redemption.log');
				return false;
			}
			
			// Save status.
			$_item->setStatus('redeemed')->save();
		}
		
		return true;
	}
	
	/**
	* Change event status to rejected in 500F API.
	*
    * @return array
	*/
	public function rejectRedeemedPoints()
	{
		$apiHelper = $this->_getFivehundredApi();
		
		$collection = $this->getAppliedRewards();
		if(! $collection) return false;
		
		foreach($collection as $_item) {
			$request_parameters = array(
				'email' => $this->getCustomerEmail(),
				'event_type' => "reward",
				'event_id' => $_item->getId(),
			);
			$response = $apiHelper->reject($request_parameters);
			if(! $response['success']) {
				Mage::log("rejectRedeemedPoints - error code: ".$response['data']['code']." - error message: ".$response['data']['message']." - event_id: ".$_item->getId().".", null, 'redemption.log');
				return false;
			}
			
			// Save status.
			$_item->setStatus('rejected')->save();
		}
		
		return true;
	}
	
	/**
	* Load the free product for birthday reward.
	*
	* @param $sku
    * @return Mage_Catalog_Model_Product
	*/
	public function loadBirthdayProduct($sku)
	{
		// Get Birthday Product.
		if(empty($sku)) return false;
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		// Mage::log("loadBirthdayProduct - getId: ".$product->getId(), null, 'redemption.log');
		
		// Check product availability.
		if(! $product) return false;
		if(! $product->isSaleable()) return false;
		
		// Check product stock.
		$stockData = $product->getStockData();
		if(! $stockData) {
			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
			
			$product = $product->load($product->getId());
			$stockData = array(
				'manage_stock' => $stock->getManageStock(),
				'is_in_stock' => $stock->getIsInStock(),
				'qty' => $stock->getQty(),
			);
			$product->setStockData($stockData);
		}
		
		return $product;
	}
	
	protected function _getQuote()
	{
		return Mage::getSingleton('checkout/cart')->getQuote();
	}
	
	protected function _getFivehundredApi()
	{
		return Mage::helper('alliance_fivehundredfriends/api');
	}
	protected function _getFivehundredHelper()
	{
		return Mage::helper('alliance_fivehundredfriends');
	}
	protected function _getRedemptionItemModel()
	{
		return Mage::getModel('alliance_fivehundredfriends/redemption_item');
	}
}
