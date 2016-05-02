<?php
/**
 * Class Alliance_FiveHundredFriends_RedemptionController
 *
 * Redemption controller, handles all things enrollment for Magento/FiveHundredFriends API integration
 */
class Alliance_FiveHundredFriends_RedemptionController extends Mage_Core_Controller_Front_Action
{
	/**
	* Mage::getUrl('/katerewards/redemption/redeem')
	*
	* Redeem action.
	*/
	public function redeemAction()
	{
		// Get data.
		$rewardId = $this->getRequest()->getParam('id');
		
		// Get model.
		$redemptionModel = $this->_getRedemptionModel();
		$redemptionModel->loadCurrentQuote();
		
		// Get reward data.
		$rewardData = $redemptionModel->getReward($rewardId);
		
		// Check reward data.
		if(! $rewardData) {
			$this->_printMessage('error', "The reward does not exist.");
			$this->_redirect('checkout/cart');
			return;
		}
		
		// Add flag to session to load Reward tab.
		Mage::getSingleton('core/session')->setLoadRewardTab(true);
		
		// Check customer session.
		$customer = $this->_getCustomerSession();
		if(! $customer->isLoggedIn()) {
			$this->_printMessage('error', "You have to be logged in to receive the rewards.");
			$this->_redirect('checkout/cart');
			return;
		}
		
		// Get customer data.
		$email = $customer->getCustomer()->getEmail();
		$customerData = $redemptionModel->getCustomerDataByEmail($email);
		
		// Check customer data.
		if(! $customerData) {
			$this->_printMessage('error', "Customer does not exists in Loyalty.");
			$this->_redirect('checkout/cart');
			return;
		}
		
		// Check quote balance.
		if($customerData['quote_balance'] < $rewardData['points']) {
			$this->_printMessage('error', "You need more points to get the reward.");
			$this->_redirect('checkout/cart');
			return;
		}
		
		// Get cart quote.
		$quote = $this->_getQuote();
		
		// Check cart quote.
		if(! $quote || ! $quote->getId()) {
			$this->_printMessage('error', "The cart is empty.");
			$this->_redirect('checkout/cart');
			return;
		}
		
		// Check cart balance.
		if($rewardData['configuration']['type'] == 'discount' && $quote->getGrandTotal() < $rewardData['configuration']['value']) {
			$this->_printMessage('error', "The order total must be greater than the reward.");
			$this->_redirect('checkout/cart');
			return;
		}
		
		// Check offers.
		if($rewardData['configuration']['type'] == 'offers') {
			if($redemptionModel->checkOffersApplied()) {
				$this->_printMessage('error', "Only one Free Cleanser can be used.");
				$this->_redirect('checkout/cart');
				return;
			}
		}
		
		// Check birthday.
		if($rewardData['configuration']['type'] == 'birthday') {
			if($redemptionModel->checkBirthdayApplied()) {
				$this->_printMessage('error', "Only one Birthday gift can be used.");
				$this->_redirect('checkout/cart');
				return;
			}
			
			// Try to add item to cart.
			$product = $redemptionModel->loadBirthdayProduct($rewardData['configuration']['value']);
			if(! $product) {
				$this->_printMessage('error', "The product could not be added to cart.");
				$this->_redirect('checkout/cart');
				return;
			}
			
			// Add birthday product to cart.
			try {
				$cart = $this->_getCart();
				$cart->addProduct($product, array('qty' => 1));
				$cart->save();
				$this->_getCheckoutSession()->setCartWasUpdated(true);
			} catch (Exception $e) {
				Mage::log("redeemAction - Add birthday error: ".$e->getMessage().".", null, 'redemption.log');
				$this->_printMessage('error', "An unexpected error has occurred.");
				$this->_redirect('checkout/cart');
				return;
			}
		}
		
		// Save Redemption in temp table.
		try {
			// Check redeem by quote.
			if(! $redemptionModel->getId()) {
				$redemptionModel->setData(array(
					'quote_id' => $quote->getId(),
					'customer_id' => $customer->getId(),
					'customer_email' => $email,
					'status' => 'pending',
					'quote_date' => $quote->getCreatedAt(),
				));
			}
			$redemptionModel->addData(array(
				'total_points' => $customerData['balance'],
				'redeem_points' => (int)$redemptionModel->getRedeemPoints() + (int)$rewardData['points'],
			));
			$redeemId = $redemptionModel->save()->getId();
			
			// Save redeem_item.
			$redemptionItemModel = $this->_getRedemptionItemModel();
			$redemptionItemModel->setData(array(
				'redeem_id' => $redeemId,
				'reward_id' => $rewardData['id'],
				'redeem_points' => (int)$rewardData['points'],
				'reward_type' => $rewardData['configuration']['type'],
				'status' => 'pending',
			));
			// Set discount amount if apply.
			if($rewardData['configuration']['type'] == 'discount') {
				$redemptionItemModel->setDiscountAmount($rewardData['configuration']['value']);
			}
			$redeemItemId = $redemptionItemModel->save()->getId();
			
			$this->_printMessage('success', 'The reward "'.$rewardData['name'].'" was applied.');
			$this->_redirect('checkout/cart');
			return;
		} catch (Exception $e) {
			Mage::log("redeemAction - error: ".$e->getMessage().".", null, 'redemption.log');
			$this->_printMessage('error', "An unexpected error has occurred.");
			$this->_redirect('checkout/cart');
			return;
		}
	}
	
	/**
	* Mage::getUrl('/katerewards/redemption/removeall')
	*
	* Remove All Redeem added to cart action.
	*/
	public function removeallAction()
	{
		// Mage::log("removeallAction.", null, 'redemption.log');
		
		// Get model.
		$redemptionModel = $this->_getRedemptionModel();
		$redemptionModel->loadCurrentQuote();
		
		// Mage::log("removeallAction - redemptionModel.", null, 'redemption.log');
		
		// Add flag to session to load Reward tab.
		Mage::getSingleton('core/session')->setLoadRewardTab(true);
		
		if(! $redemptionModel->getId()) {
			$this->_printMessage('error', "Error to remove the rewards.");
			$this->_redirect('checkout/cart');
			return;
		}
		try {
			// Check for birthday gift.
			if($redemptionModel->checkBirthdayApplied()) {
				// Mage::log("removeallAction - checkBirthdayApplied: true.", null, 'redemption.log');
				$appliedRewards = $redemptionModel->getAppliedRewards();
				$birthdaySku = '';
				foreach($appliedRewards as $_reward) {
					if($_reward->getRewardType() == 'birthday') {
						$rewardData = $redemptionModel->getReward($_reward->getRewardId());
						// Mage::log("removeallAction - rewardData: ".print_r($rewardData, 1).".", null, 'redemption.log');
						$birthdaySku = $rewardData['configuration']['value'];
						// Mage::log("removeallAction - birthdaySku: ".$birthdaySku.".", null, 'redemption.log');
					}
				}
				
				
				// Mage::log("removeallAction - loadBirthdayProduct: before.", null, 'redemption.log');
				$product = $redemptionModel->loadBirthdayProduct($birthdaySku);
				// Mage::log("removeallAction - loadBirthdayProduct: after.", null, 'redemption.log');
				
				if($product) {
					// Mage::log("removeallAction - product: ".$product->getId().".", null, 'redemption.log');
					$quote = $this->_getQuote();
					$item = $quote->getItemByProduct($product);
					if($item) {
						// Mage::log("removeallAction - item: ".$item->getId().".", null, 'redemption.log');
						$this->_getCart()->removeItem($item->getId())->save();
						// Mage::log("removeallAction - removeItem - after.", null, 'redemption.log');
					} else {
						Mage::log("removeallAction - no item id - productId: ".$product->getId().".", null, 'redemption.log');
					}
				} else {
						Mage::log("removeallAction - no birthday product.", null, 'redemption.log');
				}
			}
			
			// Remove all items.
			$removed = $redemptionModel->removeRedeemItems();
			
			$this->_printMessage('success', "The Ultimate Kate Rewards was removed.");
			$this->_redirect('checkout/cart');
			return;
		} catch (Exception $e) {
			Mage::log("removeallAction - error: ".$e->getMessage().".", null, 'redemption.log');
			$this->_printMessage('error', "An unexpected error has occurred.");
			$this->_redirect('checkout/cart');
			return;
		}
	}
	
	/**
	* Mage::getUrl('/katerewards/redemption/login')
	*
	* Login action.
	*/
	public function loginAction()
	{
		// Add flag to session to load Reward tab.
		Mage::getSingleton('core/session')->setLoadRewardTab(true);
		
		$customerSession = $this->_getCustomerSession();
		$customerSession->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
		$customerSession->setAfterAuthUrl(Mage::getUrl('checkout/cart'));
		
		$this->_redirect('customer/account/login');
		return;
	}
	/**
	* Mage::getUrl('/katerewards/redemption/loginajax')
	*
	* Login action.
	*/
	public function loginajaxAction()
	{
		// Add flag to session to load Reward tab.
		Mage::getSingleton('core/session')->setLoadRewardTab(true);
		
		// Copied from Idev_OneStepCheckout_AjaxController->loginAction
		$username = $this->getRequest()->getPost('onestepcheckout_username', false);
        $password = $this->getRequest()->getPost('onestepcheckout_password',  false);
        $session = Mage::getSingleton('customer/session');

        $result = array('success' => false);

        if ($username && $password) {
            try {
                $session->login($username, $password);
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            if (! isset($result['error'])) {
                $result['success'] = true;
            }
        } else {
            $result['error'] = $this->__(
            'Please enter a username and password.');
        }

        //session_id($sessionId);
        $this->getResponse()->setBody(Zend_Json::encode($result));
		return;
	}
	/**
	* Mage::getUrl('/katerewards/redemption/register')
	*
	* Register action.
	*/
	public function registerAction()
	{
		// Add flag to session to load Reward tab.
		Mage::getSingleton('core/session')->setLoadRewardTab(true);
		
		$customerSession = $this->_getCustomerSession();
		$customerSession->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
		$customerSession->setAfterAuthUrl(Mage::getUrl('checkout/cart'));
		$this->_redirect('customer/account/create');
		return;
	}
	
	/**
	* Mage::getUrl('/katerewards/redemption/rewardblock')
	*
	* Print the Reward Block.
	*/
	public function rewardblockAction()
	{
		$this->loadLayout();
		$rewardBlock = $this->getLayout()->createBlock('alliance_fivehundredfriends/redemption_cart');
		echo $rewardBlock->toHtml();
		return;
	}
	
	
	/**
	* Put message in session to print in the next screen.
	*
	* @param varchar $type (error, success), varchar $message
	*/
	protected function _printMessage($type, $message)
	{
		if($type == 'error') {
			$this->_getCheckoutSession()->addError($this->__('<span class="reward-session-message"></span>'.$message));
		} else {
			$this->_getCheckoutSession()->addSuccess($this->__('<span class="reward-session-message"></span>'.$message));
		}
	}
	
	/**
	* Retrieve cart.
	*
	*/
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
	protected function _getQuote()
	{
		return $this->_getCart()->getQuote();
	}
	protected function _getCheckoutSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	protected function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}
	protected function _getRedemptionModel()
	{
		return Mage::getModel('alliance_fivehundredfriends/redemption');
	}
	protected function _getRedemptionItemModel()
	{
		return Mage::getModel('alliance_fivehundredfriends/redemption_item');
	}
}