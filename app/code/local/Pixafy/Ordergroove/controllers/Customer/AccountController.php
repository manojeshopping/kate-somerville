<?php
/**
 * OrderGroove customer account controller. Handles any functionality
 * that takes places on the customers "my account" pages.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Customer_AccountController extends Mage_Core_Controller_Front_Action{
	public function subscriptionsAction(){
		$this->loadLayout();
		$block	=	$this->getLayout()->createBlock('ordergroove/pagetag_subscriptions');
		
		/**
		 * If it is guest mode then unset the left navigation.
		 */
		$isLoggedIn	=	Mage::getSingleton('customer/session')->isLoggedIn();
		if($block->isGuestMode()){
		//if($block->isGuestMode() && !$isLoggedIn){ //uncomment this line to only disable left nav is user is logged out
			$this->getLayout()->getBlock('left')->unsetChild('customer_account_navigation');
		}
		else{
			if(!$isLoggedIn){
				Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('recurring/subscriptions/view'));
				$this->_redirect('customer/account/login');
				return;
			}
		}
		
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Subscriptions'));
		$this->renderLayout();
	}
}