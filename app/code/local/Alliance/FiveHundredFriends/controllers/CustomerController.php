<?php

/**
 * Class Alliance_FiveHundredFriends_CustomerController
 *
 * Customer controller, handles all things Customer for Magento/FiveHundredFriends API integration
 */
class Alliance_FiveHundredFriends_CustomerController extends Mage_Core_Controller_Front_Action
{


    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }


    /**
     * Mage::getUrl('/katerewards/customer')
     *
     * Main FiveHundredFriends_Customer page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Mage::getUrl('/katerewards/customer/rewards')
     *
     * FiveHundredFriends dashboard widget
     */
    public function rewardsAction()
    {
		$newcustomer = $this->getRequest()->getParam('newcustomer');
		if($newcustomer) {
			$registered = $this->_registerNewCustomer();
			if($registered) {
				if(Mage::app()->getStore()->getCode() == 'm_kate')  $this->_redirect('customer/account/?section=6'); //Mobile Redirect
				else $this->_redirect('rewards-program/customer/rewards');
			} else {
				Mage::getSingleton('checkout/session')->addError($this->__('Error to register customer on reward program.'));
				if(Mage::app()->getStore()->getCode() == 'm_kate')  $this->_redirect('customer/account/?section=6'); //Mobile Redirect
				else $this->_redirect('rewards-program/customer/rewards');
			}
		}
		//Mobile Redirect
		if(Mage::app()->getStore()->getCode() == 'm_kate')  $this->_redirect('customer/account/?section=6');
	
        $this->loadLayout();
        $this->renderLayout();
    }

	
	/**
	* Enrolls customer to Loyalty and adds points of the latest order.
	*/
	protected function _registerNewCustomer()
	{
		// Enroll new customer.
		$customer = $this->_getSession();
		if(! $customer->getId()) return false;
		$customer = $customer->getCustomer();
		
		$api = $this->_getFivehundredApi();
		$request_parameters = array(
			'email'      => $customer->getEmail(),
			'first_name' => $customer->getFirstname(),
			'last_name'  => $customer->getLastname(),
		);
		$response = $api->enroll($request_parameters);
		if(! $response['success']) {
			return false;
		}
		
		// Adds points of the latest order.
		$order = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_id', $customer->getId())
			->setOrder('entity_id', 'desc')
			->getFirstItem()
		;
		if(! $order->getId()) {
			return false;
		}
		
		$customer_email = $order->getCustomerEmail();
        $customer_id    = $order->getCustomerId();
        $type           = 'purchase';
        $value          = round(($order->getGrandTotal() - $order->getTaxAmount() - $order->getShippingAmount()), 2);
        $event_id       = $order->getIncrementId();
		
		$items         = $order->getAllItems();
        $product_names = '';
        foreach ($items as $item) {
            $product_names .= $item->getName() . ', ';
        }
        $product_names = rtrim($product_names, ', ');

        $api = Mage::helper('alliance_fivehundredfriends/api');

        $request_parameters = array(
            'email'             => $customer_email,
            'type'              => $type,
            'value'             => $value,
            'event_id'          => $event_id,
            'detail'            => $product_names,
            'referral_tracking' => true,
        );
        foreach ($items as $i => $item) {
            $product           = Mage::getModel('catalog/product')->load($item->getProductId());
            $categories_string = '';
            $first_category    = true;
            foreach ($product->getCategoryIds() as $category_id) {
                if (!$first_category) $categories_string .= ',';
                $categories_string .= $category_id;
                $first_category = false;
            }
            $request_parameters["products[$i][name]"]       = $product->getName();
            $request_parameters["products[$i][url]"]        = $product->getProductUrl();
            $request_parameters["products[$i][product_id]"] = $product->getId();
            $request_parameters["products[$i][price]"]      = $product->getPrice();
            $request_parameters["products[$i][categories]"] = $categories_string;
            $request_parameters["products[$i][quantity]"]   = floatval($item['qty_ordered']);
        }

		$response = $api->record($request_parameters);
		if(! $response['success']) {
			return false;
		}
		
		
		return true;
	}
	
	/**
	* Gest helper to connect with 500F API.
	*/
	protected function _getFivehundredApi()
	{
		return Mage::helper('alliance_fivehundredfriends/api');
	}
}