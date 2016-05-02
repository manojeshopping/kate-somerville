<?php

/**
 * Class Alliance_FiveHundredFriends_Model_Redemption_Observer
 *
 * A collection of methods for events observed by FiveHundredFriends module by redeem.
 */
class Alliance_FiveHundredFriends_Model_Redemption_Observer
{
	/**
	* Executes on dispatched event "sales_quote_collect_totals_after". Adds a custom discount to quote, taking into account
	* the points used by the customer.
	*
	* @param $observer
	*/
	public function setRedemptionDiscount($observer)
	{
		// Mage::log("setRedemptionDiscount: ".$observer->getEvent()->getName().".", null, 'redemption.log');
		
		// Get quote.
		$quote = $observer->getEvent()->getQuote();
		$quoteId = $quote->getId();
		// Mage::log("setRedemptionDiscount - quoteId: ".$quoteId.".", null, 'redemption.log');
		if(! $quoteId) return;
		
		// Check customer session.
		$customer = $this->_getCustomerSession();
		if(! $customer->isLoggedIn()) return;
		
		// Get redemption model.
		$redemptionModel = $this->_getRedemptionModel();
		// Load by quoteid.
		$redemptionModel->loadCurrentQuote();
		
		// Get applied points in quote.
		$redeemPoints = $redemptionModel->getRedeemPoints();
		if(! $redeemPoints) return;
		// Mage::log("setRedemptionDiscount - redeemPoints: ".$redeemPoints.".", null, 'redemption.log');
		
		// Get total discount in quote.
		$discountAmount = $redemptionModel->getTotalDiscount();
		if(! $discountAmount) return;
		// Mage::log("setRedemptionDiscount - discountAmount: ".$discountAmount.".", null, 'redemption.log');
		
		// Get customer data.
		$email = $customer->getCustomer()->getEmail();
		$customerData = $redemptionModel->getCustomerDataByEmail($email);
		if(! $customerData || $customerData['status'] != 'active') return;
		// Mage::log("setRedemptionDiscount - customerData email: ".$customerData['email'].".", null, 'redemption.log');
		
		// Check redeemPoints.
		if($redeemPoints > $customerData['balance']) {
			Mage::log("setRedemptionDiscount - redeemPoints exceeded: ".$redeemPoints.' > '.$customerData['balance']." - quoteId: ".$quoteId.".", null, 'redemption.log');
			return;
		}
		
		// Get current total.
		$quoteSubTotal = $quote->getBaseSubtotal();
		$quoteGrandTotal = $quote->getGrandTotal();
		// Mage::log("setRedemptionDiscount - setRedemptionDiscount - quoteSubTotal: ".$quoteSubTotal." - quoteGrandTotal: ".$quoteGrandTotal.".", null, 'redemption.log');
		
		// Check total discount.
		if($discountAmount > $quoteGrandTotal) {
			// Mage::log("setRedemptionDiscount - discountAmount exceeded: ".$discountAmount.' > '.$quoteGrandTotal." - quoteId: ".$quoteId.".", null, 'redemption.log');
			$this->_getCheckoutSession()->addError(Mage::helper('core')->__('The order total must be greater than the reward. The Ultimate Kate Rewards was removed.'));
			$redemptionModel->removeRedeemItems();
			return;
		}
		
		// Set all totals to 0.
		$quote->setSubtotal(0);
		$quote->setBaseSubtotal(0);
		$quote->setSubtotalWithDiscount(0);
		$quote->setBaseSubtotalWithDiscount(0);
		$quote->setGrandTotal(0);
		$quote->setBaseGrandTotal(0);
		
		$canAddItems = $quote->isVirtual() ? ('billing') : ('shipping'); 
		foreach($quote->getAllAddresses() as $address) {
			// $address->setSubtotal(0);
			// $address->setBaseSubtotal(0);
			// $address->setGrandTotal(0);
			// $address->setBaseGrandTotal(0);
			
			// $address->collectTotals();
			
			if($address->getDiscountAmount() < 0) {
				$newAddressDiscount = -($address->getDiscountAmount() - $discountAmount);
				// Mage::log("setRedemptionDiscount: newAddressDiscount: ".$address->getDiscountAmount().' - '.$discountAmount." = ".$newAddressDiscount.".", null, 'redemption.log');
			} else {
				$newAddressDiscount = $discountAmount;
				// Mage::log("setRedemptionDiscount: newAddressDiscount: ".$newAddressDiscount.".", null, 'redemption.log');
			}

			$quote->setSubtotal((float)$quote->getSubtotal() + $address->getSubtotal());
			$quote->setBaseSubtotal((float)$quote->getBaseSubtotal() + $address->getBaseSubtotal());

			$quote->setSubtotalWithDiscount(
				(float)$quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
			);
			$quote->setBaseSubtotalWithDiscount(
				(float)$quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
			);

			$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
			$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

			$quote->save(); 

			// Mage::log("setRedemptionDiscount: setGrandTotal: ".$quote->getBaseSubtotal().' - '.$newAddressDiscount.".", null, 'redemption.log');
			$newSubTotal = $quote->getBaseSubtotal() - $newAddressDiscount;
			$quote
				->setGrandTotal($newSubTotal)
				->setBaseGrandTotal($newSubTotal)
				->setSubtotalWithDiscount($newSubTotal)
				->setBaseSubtotalWithDiscount($newSubTotal)
				->save()
			;


			if($address->getAddressType() == $canAddItems) {
				$address->setSubtotalWithDiscount((float)$address->getSubtotalWithDiscount() - $discountAmount);
				$address->setGrandTotal((float)$address->getGrandTotal() - $discountAmount);
				$address->setBaseSubtotalWithDiscount((float)$address->getBaseSubtotalWithDiscount() - $discountAmount);
				$address->setBaseGrandTotal((float)$address->getBaseGrandTotal() - $discountAmount);
				if($address->getDiscountDescription()) {
					// Mage::log("setRedemptionDiscount: setDiscountAmount: ".$address->getDiscountAmount().' - '.$discountAmount.".", null, 'redemption.log');
					$address->setDiscountAmount($address->getDiscountAmount() - $discountAmount);
					// $address->setDiscountDescription($address->getDiscountDescription().', Reward');
					$address->setBaseDiscountAmount($address->getBaseDiscountAmount() - $discountAmount);
				} else {
					$address->setDiscountAmount(-($discountAmount));
					// $address->setDiscountDescription('Reward');
					$address->setBaseDiscountAmount(-($discountAmount));
				}
				$address->save();
			}
		}

		foreach($quote->getAllItems() as $item) {
			$rat = $item->getPrice() / $quoteSubTotal;
			$ratdisc = $discountAmount * $rat;
			
			// Mage::log("setRedemptionDiscount - rat: ".$item->getPrice()." / ".$quoteSubTotal." = ".$rat.".", null, 'redemption.log');
			// Mage::log("setRedemptionDiscount - ratdisc: ".$discountAmount." * ".$rat." = ".$ratdisc.".", null, 'redemption.log');
			// Mage::log("setRedemptionDiscount - setDiscountAmount: ".$item->getDiscountAmount()." + (".$ratdisc." * ".$item->getQty().") = ".($item->getDiscountAmount() + ($ratdisc * $item->getQty())).".", null, 'redemption.log');
			
			$item->setDiscountAmount($item->getDiscountAmount() + ($ratdisc * $item->getQty()));
			$item->setBaseDiscountAmount($item->getBaseDiscountAmount() + ($ratdisc * $item->getQty()))->save();
		}
		
		// Mage::log("setRedemptionDiscount: new grand total: ".$quote->getGrandTotal().".", null, 'redemption.log');
	}
	
	/**
	* Executes on dispatched event "sales_order_place_before". Checks redeem points before to place order.
	* If the redeem points can't be applied, redirect to cart with an error message.
	*
	* @param $observer
	*/
	public function checkRewardBefore($observer)
	{
		// Mage::log("checkRewardBefore: ".$observer->getEvent()->getName().".", null, 'redemption.log');
		
		// Get order.
		$order = $observer->getEvent()->getOrder();
		// Mage::log("checkRewardBefore - orderId: ".$order->getId().".", null, 'redemption.log');
		
		// Get quote.
		$quote = $this->_getQuote();
		$quoteId = $quote->getId();
		// Mage::log("checkRewardBefore - quoteId: ".$quoteId.".", null, 'redemption.log');
		if(! $quoteId) return;
		
		// Get redemption model.
		$redemptionModel = $this->_getRedemptionModel();
		// Load by quoteid.
		$redemptionModel->loadCurrentQuote();
		if(! $redemptionModel->getId()) return true;
		// Mage::log("checkRewardBefore- redemptionModel id: ".$redemptionModel->getId().".", null, 'redemption.log');
		
		// Get total discount in quote.
		$discountAmount = $redemptionModel->getTotalDiscount();
		// Mage::log("checkRewardBefore- discountAmount: ".$discountAmount.".", null, 'redemption.log');
		
		// Check applied points in quote.
		$redeemPoints = $redemptionModel->getRedeemPoints();
		if(! $redeemPoints) {
			// Mage::log("checkRewardBefore - No redeem points - quoteId: ".$quoteId.".", null, 'redemption.log');
			return true;
		}
		// Mage::log("checkRewardBefore - redeemPoints: ".$redeemPoints.".", null, 'redemption.log');
		
		// Check customer session.
		$customer = $this->_getCustomerSession();
		if(! $customer->isLoggedIn()) {
			// Mage::log("checkRewardBefore - Customer no logged in - quoteId: ".$quoteId.".", null, 'redemption.log');
			return true;
		}
		
		// Get customer data.
		$email = $customer->getCustomer()->getEmail();
		$customerData = $redemptionModel->getCustomerDataByEmail($email);
		if(! $customerData || $customerData['status'] != 'active') {
			Mage::log("checkRewardBefore - Customer no active in API - email: ".$email." - quoteId: ".$quoteId.".", null, 'redemption.log');
			return true;
		}
		// Mage::log("customerData email: ".$customerData['email'].".", null, 'redemption.log');
		
		// Check redeemPoints.
		if($redeemPoints > $customerData['balance']) {
			Mage::log("checkRewardBefore - redeemPoints exceeded: ".$redeemPoints.' > '.$customerData['balance']." - quoteId: ".$quoteId.".", null, 'redemption.log');
			$this->_redirectToCart('The rewards can\'t be applied.');
			exit;
		}
		
		// Get current total.
		$quoteTotal = $quote->getBaseSubtotal();
		// Mage::log("checkRewardBefore - setRedemptionDiscount - quoteTotal: ".$quoteTotal.".", null, 'redemption.log');
		
		// Check total discount.
		if($discountAmount > $quoteTotal) {
			Mage::log("checkRewardBefore - discountAmount exceeded: ".$discountAmount.' > '.$quoteTotal." - quoteId: ".$quoteId.".", null, 'redemption.log');
			$this->_redirectToCart('The rewards can\'t be applied.');
			exit;
		}
		
		// Save new status and order data.
		try {
			$redemptionModel->addData(array(
				'status' => "place_before",
				'order_id' => $order->getId(),
				'order_status' => $order->getStatus(),
				'order_date' => $order->getCreatedAt(),
			));
			$redeemId = $redemptionModel->save()->getId();
			// Mage::log("checkRewardBefore - redeem updated - redeemId: ".$redeemId.".", null, 'redemption.log');
		} catch (Exception $e) {
			Mage::log("checkRewardBefore - redeem updated error: ".$e->getMessage().".", null, 'redemption.log');
			$this->_redirectToCart('The rewards can\'t be applied.');
			exit;
		}
		
		
		// Mage::log("checkRewardBefore - validation OK.", null, 'redemption.log');
		return true;
	}
	
	/**
	* Executes on dispatched event "sales_order_place_after". Updates redeem data and trigger the reward redemption on API.
	*
	* @param $observer
	*/
	public function applyRewardToOrder($observer)
	{
		// Mage::log("applyRewardToOrder: ".$observer->getEvent()->getName().".", null, 'redemption.log');
		
		// Get order.
		$order = $observer->getEvent()->getOrder();
		// Mage::log("applyRewardToOrder - orderId: ".$order->getId().".", null, 'redemption.log');
		
		// Get redemption model.
		$redemptionModel = $this->_getRedemptionModel();
		// Load by quoteid.
		$redemptionModel->loadCurrentQuote();
		if(! $redemptionModel->getId()) return true;
		// Mage::log("applyRewardToOrder- redemptionModel id: ".$redemptionModel->getId().".", null, 'redemption.log');
		
		// Check applied points in quote.
		$rewards = $redemptionModel->getRewards();
		if(empty($rewards)) return true;
		// Mage::log("applyRewardToOrder - rewards count: ".count($rewards).".", null, 'redemption.log');
		
		try {
			// Trigger the reward redemption.
			$redeemed = $redemptionModel->rewardRedemption();
			// Mage::log("applyRewardToOrder - redeemed: ".$redeemed.".", null, 'redemption.log');
			
			// Save new status and order data.
			$redemptionModel->addData(array(
				'status' => "redeemed",
				'order_status' => $order->getStatus(),
			));
			$redeemId = $redemptionModel->save()->getId();
			// Mage::log("applyRewardToOrder - redeem updated - redeemId: ".$redeemId.".", null, 'redemption.log');
			return true;
		} catch (Exception $e) {
			Mage::log("applyRewardToOrder - redeem updated error: ".$e->getMessage().".", null, 'redemption.log');
			return false;
		}
		
		// Mage::log("applyRewardToOrder - OK.", null, 'redemption.log');
		return true;
	}
	
	/**
	* Executes on dispatched event "sales_order_payment_cancel". Return the reemed.
	*
	* @param $observer
	*/
	public function returnRedeemedPoints($observer)
	{
		// Mage::log("returnRedeemedPoints: ".$observer->getEvent()->getName().".", null, 'redemption.log');
		
		// Get order.
		$order = $observer->getOrder();
		// Mage::log("returnRedeemedPoints - order ".$order->getId().".", null, 'redemption.log');
		
		$stateClosed = $order::STATE_CLOSED;
		$stateCanceled = $order::STATE_CANCELED;
		// Mage::log("returnRedeemedPoints - getState ".$order->getState()." - getOrigData: ".$order->getOrigData('state').".", null, 'redemption.log');
		if(
			$order->getOrigData('state') == $order->getState() ||
			($order->getState() != $stateClosed && $order->getState() != $stateCanceled)
		) return true;
		// Mage::log("returnRedeemedPoints - processing.", null, 'redemption.log');
		
		// Load Redeem by order_id.
		$redemptionModel = $this->_getRedemptionModel();
		$redemptionModel->load($order->getId(), 'order_id');
		
		// If the order has redemption, return to the API.
		// Mage::log("returnRedeemedPoints - redemption id: ".$redemptionModel->getId().".", null, 'redemption.log');
		if($redemptionModel->getId()) {
			try {
				$returned = $redemptionModel->rejectRedeemedPoints();
				// Mage::log("returnRedeemedPoints - returned: ".$returned.".", null, 'redemption.log');
				
				// Save new status and order data.
				$redemptionModel->addData(array(
					'status' => "retured",
					'order_status' => 'canceled',
				));
				$redeemId = $redemptionModel->save()->getId();
			} catch (Exception $e) {
				Mage::log("returnRedeemedPoints - redeem return error: ".$e->getMessage().".", null, 'redemption.log');
				return false;
			}
		}
		
		// Mage::log("returnRedeemedPoints - OK.", null, 'redemption.log');
		return true;
	}
	
	/**
	* Executes on dispatched event "sales_order_grid_collection_load_before". Add the redeem points to order grid.
	*
	* @param $observer
	*/
	public function addRedeemedPointsToOrderGrid($observer)
	{
		// Mage::log("addReemedPointsToOrderGrid: ".$observer->getEvent()->getName().".", null, 'redemption.log');
		
		$collection = $observer->getOrderGridCollection();
		$select = $collection->getSelect();
		
		// Check to avoid correlation duplicated.
		if(strpos($select, 'ON redemption.') === false && strpos($select, 'main_table.customer_id = ') === false) {
			// Mage::log("addReemedPointsToOrderGrid - select: ".$select.".", null, 'redemption.log');
			$select->joinLeft(
				array('redemption' => $collection->getTable('alliance_fivehundredfriends/redemption')), 
				'redemption.order_id = main_table.entity_id',
				array('redeem_points' => 'redeem_points')
			);
			
			// If is filtered by customer_email or status, change to main_table.customer_email
			if(strpos($select, '`customer_email` LIKE') !== false || strpos($select, '`status` =') !== false) {
				$where = $select->getPart(Zend_Db_Select::WHERE);
				foreach($where as $_key => $_where) {
					if(strpos($_where, '`customer_email`') !== false) {
						$where[$_key] = str_replace('`customer_email`', '`main_table`.`customer_email`', $_where);
					} elseif(strpos($_where, '`status`') !== false) {
						$where[$_key] = str_replace('`status`', '`main_table`.`status`', $_where);
					}
				}
				$select->setPart(Zend_Db_Select::WHERE, $where);
			}
		}
		// Mage::log("addReemedPointsToOrderGrid - select: ".$select.".", null, 'redemption.log');
		
		// Mage::log("addReemedPointsToOrderGrid - OK.", null, 'redemption.log');
		return true;
	}
	
	/**
	* Executes on dispatched event "checkout_cart_save_after". Remove the freeitems and used points if the cart subtotal is $0.
	*
	* @param $observer
	*/
	public function checkFreeItems($observer)
	{
		// Mage::log("checkFreeItems: ".$observer->getEvent()->getName().".", null, 'redemption.log');
		
		// Get quote.
		$quote = $this->_getQuote();
		// Mage::log("checkFreeItems - getSubtotal: ".$quote->getSubtotal().".", null, 'redemption.log');
		
		if($quote->getSubtotal() == 0) {
			// Load redemption module.
			$redemptionModel = $this->_getRedemptionModel();
			$redemptionModel->loadCurrentQuote();
			
			// Check if birthday is applied.
			if($redemptionModel->checkBirthdayApplied()) {
				// Mage::log("checkFreeItems - checkBirthdayApplied: true.", null, 'redemption.log');
				$appliedRewards = $redemptionModel->getAppliedRewards();
				$birthdaySku = '';
				foreach($appliedRewards as $_reward) {
					if($_reward->getRewardType() == 'birthday') {
						$rewardData = $redemptionModel->getReward($_reward->getRewardId());
						// Mage::log("checkFreeItems - rewardData: ".print_r($rewardData, 1).".", null, 'redemption.log');
						$birthdaySku = $rewardData['configuration']['value'];
						// Mage::log("checkFreeItems - birthdaySku: ".$birthdaySku.".", null, 'redemption.log');
					}
				}
				
				// Mage::log("checkFreeItems - loadBirthdayProduct: before.", null, 'redemption.log');
				$product = $redemptionModel->loadBirthdayProduct($birthdaySku);
				// Mage::log("checkFreeItems - loadBirthdayProduct: after.", null, 'redemption.log');
				
				if($product) {
					// Mage::log("checkFreeItems - product: ".$product->getId().".", null, 'redemption.log');
					$quote = $this->_getQuote();
					$item = $quote->getItemByProduct($product);
					if($item) {
						// Mage::log("checkFreeItems - item: ".$item->getId().".", null, 'redemption.log');
						$this->_getCart()->removeItem($item->getId())->save();
						// Mage::log("checkFreeItems - removeItem - after.", null, 'redemption.log');
					} else {
						Mage::log("checkFreeItems - no item id - productId: ".$product->getId().".", null, 'redemption.log');
					}
				} else {
						Mage::log("checkFreeItems - no birthday product.", null, 'redemption.log');
				}
			}
			
			// Remove all items.
			$removed = $redemptionModel->removeRedeemItems();
		}
		
		// Mage::log("checkFreeItems - OK.", null, 'redemption.log');
		return true;
	}
	
	
	protected function _redirectToCart($msg)
	{
		$this->_getCheckoutSession()->addError(Mage::helper('core')->__($msg));
		Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
		Mage::app()->getResponse()->sendResponse();
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
}

