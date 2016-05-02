<?php

/**
 * Class Alliance_FiveHundredFriends_Model_Observer
 *
 * A collection of methods for events observed by FiveHundredFriends module
 */
class Alliance_FiveHundredFriends_Model_Observer
{
    /**
     * Executes on dispatched event "customer_register_success". If the new customer opted in to the rewards program, it fires an API request to
     * 500 Friends enrolling them in the program. If the customer opted out, it doesn't fire the request.
     *
     * @param $observer
     */
    public function customerRegisterSuccess($observer)
    {
		$is_rewards_enrolled = Mage::app()->getRequest()->getParam('is_rewards_enrolled') === null ? false : true;
        $customer            = $observer->getEvent()->getCustomer();
        if ($is_rewards_enrolled) {
            $api                = Mage::helper('alliance_fivehundredfriends/api');
            $request_parameters = array(
                'email'      => $customer->getEmail(),
                'first_name' => $customer->getFirstname(),
                'last_name'  => $customer->getLastname(),
                'birthdate'  => substr($customer->getBirthDate(), 5, 5),
                'external_customer_id' => $customer->getId(),
            );
			
			$api->enroll($request_parameters);
            Mage::register('customer_enrolled_fhf', true);
        }
    }

    /**
     * Executes on dispatched event "alliance_katereviews_approval" and records the event to 500 Friends API
     *
     * @param $observer
     * @return bool
     */
    public function allianceKatereviewsApproval($observer)
    {
		$customer           = $observer->getEvent()->getCustomer();
		$customer_id        = $customer->getId();

		if(! Mage::helper('alliance_fivehundredfriends/data')->getCustomerRestrictionBackend($customer_id))
		{
			$customer           = $observer->getEvent()->getCustomer();
			$product            = $observer->getEvent()->getProduct();
			$customer_email     = $customer->getEmail();
			$customer_id        = $customer->getId();
			$product_name       = $product->getName();
			$product_sku        = $product->getSku();
			$detail             = 'Customer Review for ' . $product_name . ' (' . $product_sku . ')';
			$request_parameters = array(
				'email'  => $customer_email,
				'type'   => 'review',
				'detail' => $detail,
			);
			$api                = Mage::helper('alliance_fivehundredfriends/api');
			$response           = $api->record($request_parameters);
			
			return $response['success'] ? true : false;
		}else{
			return false;
		}
    }

    /**
     * Executes on dispatched event "sales_order_place_after" in the adminhtml area only, and records the purchase to 500 Friends API
     *
     * @param $observer
     * @return bool
     */
    public function adminSalesOrderPlaceAfter($observer)
    {
	    $order = $observer->getEvent()->getOrder();
	
		if(! Mage::helper('alliance_fivehundredfriends/data')->getCustomerRestrictionBackend($order->getCustomerId()))
		{
			$response = $this->_recordSale($order);
			return $response['success'] ? true : false;
		}else{
			return false;
		}
    }

    /**
     * Executes on dispatched event "customer_login", and enrolls the customer if the login is made from the
     * enrollment page's login form (/rewards-program)
     *
     * @param $observer
     */
    public function customerLogin($observer)
    {
        if (Mage::registry('customer_enrolled_fhf') == true) return;

        $customer            = $observer->getEvent()->getCustomer();
        $is_rewards_enrolled = Mage::app()->getRequest()->getParam('is_rewards_enrolled') === null ? false : true;
        if ($is_rewards_enrolled) {
            // enroll customer
            $api                = Mage::helper('alliance_fivehundredfriends/api');
            $request_parameters = array(
                'email'      => $customer->getEmail(),
                'first_name' => $customer->getFirstname(),
                'last_name'  => $customer->getLastname(),
            );
            $api->enroll($request_parameters);
        }
    }

    /**
     * Executes on dispatched event "customer_save_before", and registers the customer's email as it was before
     * the save, to Mage::registry fivehundredfriends_email_change_before
     *
     * @param $observer
     */
    public function customerSaveBefore($observer)
    {
        $customer     = $observer->getEvent()->getCustomer();
		$old_customer = Mage::getModel('customer/customer')->load($customer->getId());
		
        if (Mage::registry('fivehundredfriends_email_change_before') === null && $old_customer->getId()) {
            $old_email = $old_customer->getEmail();
            Mage::register('fivehundredfriends_email_change_before', $old_email);
        }
		
		if (Mage::registry('fivehundredfriends_birthdate_change_before') === null && $old_customer->getId()) {
            Mage::register('fivehundredfriends_birthdate_change_before', $old_customer->getBirthDate());
		}
    }

    /**
     * Executes on dispatched event "customer_save_after", checks to see if the customer's email was changed, and
     * if it was, it changes the corresponding email on 500 Friends. This is transactional, so if the API request to change
     * customer's email gets dropped, the customer's email on Magento gets rolled back and it displays an error message
     * asking them to try again later.
     *
     * @param $observer
     */
    public function customerSaveAfter($observer)
    {
        $old_email     = Mage::registry('fivehundredfriends_email_change_before');
        $fired_already = Mage::registry('fivehundredfriends_email_change_fired');
		
		$customer  = $observer->getEvent()->getCustomer();

        if ($old_email !== null && !$fired_already) {
            Mage::register('fivehundredfriends_email_change_fired', true);
			$new_email = $customer->getEmail();
			
			if ($new_email !== $old_email) {
				$api                     = Mage::helper('alliance_fivehundredfriends/api');
				$request_parameters      = array(
					'email' => $old_email,
				);
				$response                = $api->customerShow($request_parameters);
				$customer_in_500_friends = $response['success'];

				if ($customer_in_500_friends) {
						$request_parameters = array(
							'from_email' => $old_email,
							'to_email'   => $new_email,
						);
						$response           = $api->updateEmail($request_parameters);

						if (!$response['success']) {
							Mage::register('fivehundredfriends_email_change_error', true);
							$rollback = Mage::getModel('customer/customer')->load($customer->getId());
							$rollback->setEmail($old_email);
							$rollback->save();
						}
				}
			}
            $error = Mage::registry('fivehundredfriends_email_change_error');
            if ($error) {
                Mage::getSingleton('customer/session')
                    ->addError('While the rest of your changes may have been saved, there was an error changing your email address. Please try again later.');
                Mage::getSingleton('adminhtml/session')
                    ->addError('While the rest of your changes may have been saved, there was an error changing the email address. This is probably due to a
                    third-party API request being dropped or rejected. Please try again later.');
            }
        }
		
		
		// Check for birthdate.
        $old_birthdate = Mage::registry('fivehundredfriends_birthdate_change_before');
        $fired_already = Mage::registry('fivehundredfriends_birthdate_change_fired');
		if($old_birthdate !== null && ! $fired_already) {
			Mage::register('fivehundredfriends_birthdate_change_fired', true);
			
			// Check if the birthdate changed.
			$new_birthdate = $customer->getBirthDate();
			if($new_birthdate != $old_birthdate) {
				// Check customer in Loyalty
				$api = Mage::helper('alliance_fivehundredfriends/api');
				$request_parameters = array('email' => $customer->getEmail());
				$response = $api->customerShow($request_parameters);
				
				if($response['success']) {
					$request_parameters = array(
						'email' => $customer->getEmail(),
						'first_name' => $customer->getFirstname(),
						'last_name'  => $customer->getLastname(),
						'birthdate' => substr($customer->getBirthDate(), 5, 5),
					);
					$response = $api->updateCustomerInfo($request_parameters);
				}
			}
		}
	}
	
	
	/**
	* Executes on dispatched event "sales_order_place_after" in the frontend area only, and records the purchase to 500 Friends API
	*
	* @param $observer
	* @return bool
	*/
	public function recordSale($observer)
	{
		// Mage::log("recordSale: ".$observer->getEvent()->getName().".", null, 'redemption.log');
		// Get order.
		$order = $observer->getEvent()->getOrder();
		// Mage::log("recordSale - order: ".$order->getId().".", null, 'redemption.log');
		
		// Record purchase.
        $response = $this->_recordSale($order);
		// Mage::log("recordSale - response: ".print_r($response, 1).".", null, 'redemption.log');
		if(! $response['success']) return true;
		
		return true;
	}
	
	
	protected function _recordSale($order) {
		$customer_email = $order->getCustomerEmail();
        $customer_id    = $order->getCustomerId();
        $type           = 'purchase';
        $value          = round(($order->getGrandTotal() - $order->getTaxAmount() - $order->getShippingAmount()), 2);
        $event_id       = $order->getIncrementId();
		
		
		if($value < 0) $value = 0;

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
		
		// Save points in session.
		if($response['success']) {
			$points = $response['data']['points'];
		} else {
			$points = $value;
		}
		
		Mage::getSingleton('core/session')->setUsedPoints(number_format((int)$points, 0));
		
        return $response;
	}
}

