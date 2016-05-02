<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method subscriptionPost(Varien_Event_Observer $observer)
 * 
 * Ordergroove module observer class. Handles the various
 * event logic that this module must process.
 */
class Pixafy_Ordergroove_Model_Observer extends Varien_Object{
	
	
	/**
	 * Subscription post triggered by the sales_order_place_after event.
	 * This method will trigger the subscription creation class that
	 * will determine whether or not to create a subscription in OG
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function subscriptionPost(Varien_Event_Observer $observer){
		/**
		 * If the registry key to skip is present, do not trigger this when
		 * an order is placed. This registry value should only be set when
		 * an subscription order from OG is being processed, so it will not
		 * try and create a subscription for a subscription order
		 */
		if(!Mage::registry(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_SKIP_SUBSCRIPTION)){
			$order				=	$observer->getEvent()->getOrder();
			if($order->getId()){
				$subscriptionPost	=	Mage::helper('ordergroove/subscription_post');
				try{
					$subscriptionPost->setOrder($order);
					$subscriptionPost->process();
				}
				catch(Exception $e){
					$subscriptionPost->log($subscriptionPost->getSubscriptionFailedMessage($order->getIncrementId(), $e->getMessage()));
				}
			}
		}
	}
	
	/**
	 * Method triggered by the checkout_onepage_controller_success_action event.
	 * Since there is no easy way us for get the current order from the success
	 * pagetagging phtml file, we will register the order before the layout
	 * is loaded so that it can be referenced from the pagetagging block
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function registerOrder(Varien_Event_Observer $observer){
		$orderId	=	$observer->getEvent()->getOrderIds();
		if(is_array($orderId)){
			$orderId	=	$orderId[0];
		}
		if($orderId){
			$order	=	Mage::getModel('sales/order')->load($orderId);
			if($order->getId()){
				Mage::register(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_ORDER_SUCCESS_ORDER, $order);
			}
		}
	}
	
	/**
	 * Function that executes from the cron that runs the product feed
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function runProductFeed(){
		$feed	=	Mage::helper('ordergroove/product_feed');
		$result	=	$feed->generate(TRUE);
	}
	
	/**
	 * Triggered by the checkout_allow_guest
	 * event on the checkout page. This event
	 * will disable guest checkout as specified
	 * by the OrderGroove configuration panel.
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function disableGuestCheckout($observer)
	{
		if (!Mage::helper('ordergroove/config')->functionalityCheckGuestCheckout()){
			$observer->getEvent()->getResult()->setIsAllowed(false);
		}
	}
	
	/**
	 * When a customer is saved, check and see if the e-mail
	 * address was updated. If the customer previously had
	 * an email, and it is not equal to the new email,
	 * then send OrderGroove the customer update json
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function updateCustomerEmail(Varien_Event_Observer $observer){
		$customer		=	$observer->getEvent()->getCustomer();
		
		$originalEmail	=	trim($customer->getOrigData('email'));
		$currentEmail	=	trim($customer->getData('email'));
		
		/**
		 * Check to see if there is an original email
		 * so that when a new customer is created
		 * this logic is not executed.
		 */
		if($originalEmail){
			
			/**
			 * If the email was changed.
			 */
			if($originalEmail != $currentEmail){
				/**
				 * Send json data over to OrderGroove
				 */
				$updateModel	=	Mage::getModel('ordergroove/api_customer_update');
				$updateModel->setCustomer($customer);
				$updateModel->sendCustomerUpdate();
			}
		}
	}
}
?>
