<?php
require_once(dirname(dirname(__FILE__)).'/lib/MailChimp.php');


class Alliance_Giveawayfb_Model_Giveawayfb extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('giveawayfb/giveawayfb');
	}
	
	// Check if customer exists.
	public function customerExists($email)
	{
		$customer = Mage::getModel('customer/customer');
		
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($email);
		if ($customer->getId()) {
			return $customer;
		}
		
		return false;
	}
	
	// Check if customer address exists.
	public function addressExists($data)
	{
		$addressCollection = Mage::getModel("customer/address")->getCollection();
		$addressCollection
			->addAttributeToFilter('street', $data['address1']."\n".$data['address2'])
			->addAttributeToFilter('postcode', $data['zip'])
			->addAttributeToFilter('city', $data['city'])
			->addAttributeToFilter('region_id', $data['state'])
		;
		
		
		$addressData = $addressCollection->getData();
		if(empty($addressData)) return false;
		return true;
	}
	
	// Check if user is loaded in custom table.
	public function emailSended($email)
	{
		$users = $this->getCollection()
			->addFieldToSelect(array('confirmid', 'name'))
			->addFieldToFilter('email', $email)
			->setOrder('giveawayfb_id', 'DESC')
		;
		if($users->count() == 0) return false;
		
		$lastEntry = $users->getFirstItem();
		$data = $lastEntry->getData();
		if(empty($data['confirmid'])) return false;
		
		
		return $data;
	}
	
	// Save data in temp table.
	public function insertData($data)
	{
		// Get cocnerns.
		$concernsCount = count($data['concerns']);
		if($concernsCount > 0) $data['skin_concern1'] = $data['concerns'][0];
		if($concernsCount > 1) $data['skin_concern2'] = $data['concerns'][1];
		
		$data['email'] = Mage::helper('giveawayfb')->getSessionData('email');
		if(empty($data['email'])  || ! Zend_Validate::is($data['email'], 'EmailAddress')) {
			Mage::log("Empty email to insert data.", null, 'giveawayfb.log');
			return false;
		}
		
		$this->setData($data);
		try {
			$insertId = $this->save()->getId();
			return $insertId;
		} catch (Exception $e) {
			Mage::log("MySQL Insert error: ".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}
	}
	
	// Update data in temp table.
	public function updateData($id, $data, $field = null)
	{
		if(is_null($field)) $this->load($id);
		else {
			$this->load($id, $field);
			$id = $this->getGiveawayfbId();
		}
		
		$this->setData($data);
		try {
			$this->setId($id)->save();
			
			$this->cleanModelCache();
			$this->clearInstance();
			return true;
		} catch (Exception $e) {
			Mage::log("MySQL Insert error: ".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}
	}

	// Check confirmid.
	public function checkConfirmid($confirmid)
	{
		$collection = $this->getCollection()->addFieldToFilter('confirmid', $confirmid);
		if(count($collection) > 0) return $collection->getFirstItem();
		
		return false;
	}
	
	// Get clients loaded in table, but just the confirmed customers.
	public function getConfirmedCustomers($limit = null)
	{
		$collection = $this->getCollection()
			->addFieldToFilter('customer_id', array('notnull' => true))
			->addFieldToFilter('order_id', array('null' => true))
		;
		
		if(! is_null($limit)) $collection->getSelect()->limit($limit);
		
		return $collection;
	}
	
	
	// Create new customer in Magento.
	public function createMangentoCustomer($data, $newPassword)
	{
		// Validate data.
		if(empty($data) || empty($data['email']) || ! Zend_Validate::is($data['email'], 'EmailAddress')) {
			Mage::log("Creation Magento customer error: email not found.", null, 'giveawayfb.log');
			return false;
		}
		
		// Check if customer exists by email.
		$exists = self::customerExists($data['email']);
		if($exists) {
			Mage::log("Creation Magento customer error: customer already exists (email: ".$data['email']." - ID: #".$exists->getId().").", null, 'giveawayfb.log');
			return false;
		}
		
		// Check if customer exists by address.
		$addressExists = $this->addressExists($data);
		if($addressExists) {
			Mage::log("Creation Magento customer error: customer address already exists (ID: #".$addressExists->getId().").", null, 'giveawayfb.log');
			return false;
		}
		
		
		// Initalize helper.
		$helper = Mage::helper('giveawayfb');
		
		// Get website id and store.
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		
		// Create customer.
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId = $websiteId;
		$customer->setStore($store);
		
		$customer->setFirstname($data['name']);
		$customer->setLastname($data['lastname']);
		$customer->setEmail($data['email']);
		$customer->setData('dob', $helper->generateDob($data['birthdate_month'], $data['birthdate_day']));
		$customer->setPassword($newPassword);
		$customer->setGroupId($helper->getCustomerGroupId());
		$customer->setData('primary_skin_concern', $data['skin_concern1']); // Save "Primary Skin Concern" attribute
		$customer->setData('secondary_skin_concern', $helper->getConcernId2($data['skin_concern2'])); // Save "Secondary Skin Concern" attribute
		
		try {
			$customerId = $customer->save()->getId();
		} catch (Exception $e) {
			Mage::log("Creation customer error: ".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}
		
		$addressId = $this->createCustomerAddress($customerId, $data);
		
		// Subscribe to Newsletter.
		$data['newsletter'] = 1; // Force to subscribe.
		if(isset($data['newsletter']) && $data['newsletter'] == 1) {
			/*
			try {
				Mage::getModel('newsletter/subscriber')->subscribe($data['email']);
			} catch (Exception $e) {
				Mage::log("Subscribe email error: (customer #".$customerId.")".$e->getMessage(), null, 'giveawayfb.log');
			}
			*/
			
			// Subscribe to mailchimp.
			try {
				$mailchimpData = array(
					'id' => $helper->getMailchimpListId(),
					'email' => array('email' => $data['email']),
					'merge_vars' => array(
						'EMAIL' => $data['email'],
						'FNAME' => $data['name'], 
						'LNAME' => $data['lastname'], 
						'ZIP' => $data['zip'],
						'BIRTHDATE' => $data['birthdate_month']."/".$data['birthdate_day'],
						'SKIN1' => $helper->getConcernLabel($data['skin_concern1']),
						'SKIN2' => $helper->getConcernLabel($data['skin_concern2']),
						'KIT' => Mage::getModel('catalog/product')->load($data['samplekit'])->getTitle(),
					),
					'double_optin' => false,
					'update_existing' => false,
					'replace_interests' => false,
					'send_welcome' => false,
				);
				
				$mailchimp = new MailChimp($helper->getMailchimpApiKey());
				$result = $mailchimp->call('lists/subscribe', $mailchimpData);
				
				// Mage::log("mailchimp: ".print_r($mailchimpData, 1), null, 'giveawayfb.log');
			} catch (Exception $e) {
				Mage::log("Mailchimp error: (customer #".$customerId.")".$e->getMessage(), null, 'giveawayfb.log');
			}
		}
		
		
		return $customerId;
	}
	
	// Create customer address.
	public function createCustomerAddress($customerId, $data)
	{
		// Create address for customer.
		$address = Mage::getModel("customer/address");
		$address->setCustomerId($customerId);
		$address->setFirstname($data['name']);
		$address->setLastname($data['lastname']);
		$address->setTelephone($data['telephone']);
		$address->setStreet(array($data['address1'], $data['address2']));
		$address->setPostcode($data['zip']);
		$address->setCity($data['city']);
		$address->setRegion($data['state']);
		$address->setCountryId('US');
		$address->setIsDefaultBilling(true);
		$address->setIsDefaultShipping(true);
		
		try {
			$addressId = $address->save()->getId();
			return $addressId;
		} catch (Exception $e) {
			Mage::log("Creation address error: (customer #".$customerId.")".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}
	}
	
	
	// Create new order.
	public function createNewOrder($customerId, $productId)
	{
		if(empty($customerId) || empty($productId)) {
			Mage::log("Creation order error: customer or product not found (#".$customerId." - #".$productId.").", null, 'giveawayfb.log');
			return false;
		}
		// Initalize helper.
		$helper = Mage::helper('giveawayfb');
		
		// *** Collect all necesary data. *** //
		// Load storeid.
		$storeId = Mage::app()->getStore()->getStoreId();
		
		// Load customer.
		$customer = Mage::getModel('customer/customer');
		$customer->load($customerId);
		$id = $customer->getId();
		if(empty($id)) {
			Mage::log("Creation order error: customer not found (#".$customerId.").", null, 'giveawayfb.log');
			return false;
		}
		
		// Load address.
		$customerAddressId = $customer->getDefaultBilling();
		if(! $customerAddressId) {
			Mage::log("Creation order error: addressid not found (#".$customerId.").", null, 'giveawayfb.log');
			return false;
		}
		$addressData = Mage::getModel('customer/address')->load($customerAddressId)->getData();
		
		// Load product.
		$product = Mage::getModel('catalog/product');
		$product->load($productId);
		$id = $product->getId();
		if(empty($id)) {
			Mage::log("Creation order error: product not found (#".$productId.").", null, 'giveawayfb.log');
			return false;
		}
		
		$shippingMethod = $helper->getShippingMethod();
		$paymentMethod = $helper->getPaymentMethod();
		$comment = $helper->getOrderComment();
		// *** Collect all necesary data. *** //
		
		
		// *** Create quote *** //
		// Get the Quote to save the order.
		$quote = Mage::getModel('sales/quote')->setStoreId($storeId);

		// Set the customer.
		$quote->setCustomer($customer);

		// Add the product with the product options.
		try {
			$quote->addProduct($product);
		} catch (Exception $e) {
			// Empty Cart
			$quote->setIsActive(false);
			$quote->delete();
			
			Mage::log("Creation order error: ".$e->getMessage()." - ProductId: ".$product->getId()." - Name: ".$product->getName(), null, 'giveawayfb.log');
			return false;
		}

		// Add the address data to the billing address.
		$billingAddress  = $quote->getBillingAddress()->addData($addressData);

		// Add the adress data to the shipping address
		$shippingAddress = $quote->getShippingAddress()->addData($addressData);

		// Collect the shipping rates.
		$shippingAddress->setCollectShippingRates(true)->collectShippingRates();

		// Set the shipping method.
		$shippingAddress->setShippingMethod($shippingMethod);

		// Set the payment method.
		try {
			$shippingAddress->setPaymentMethod($paymentMethod);

			// Set the payment method.
			$quote->getPayment()->importData(array('method' => $paymentMethod));

			// Collect the prices.
			$quote->collectTotals()->save();
		} catch (Exception $e) {
			// Empty Cart
			$quote->setIsActive(false);
			$quote->delete();
			
			Mage::log("Creation order error: ".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}
		// *** Create quote *** //
		
		
		// *** Submit Order *** //
		$newOrder = $this->submitOrder($quote);
		if(! $newOrder) {
			// Empty Cart
			$quote->setIsActive(false);
			$quote->delete();
			
			return false;
		}
		// *** Submit Order *** //
		
		
		// Empty Cart
		$quote->setIsActive(false);
        $quote->delete();
		
		
		Mage::log("newOrder: ".$newOrder->getId(), null, 'giveawayfb.log');
		return $newOrder;
	}
	
	// Submit Order.
	public function submitOrder($quote)
	{
		// Initalize helper.
		$helper = Mage::helper('giveawayfb');
		
		// Get the service to submit the order
		$service = Mage::getModel('sales/service_quote', $quote);

		// Submit the order.
		try {
			$service->submitAll();
		} catch (Exception $e) {
			Mage::log("Creation order error (service - Customer ID: #".$quote->getCustomerId()."): ".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}

		// Get the new order
		$newOrder = $service->getOrder();
		$orderId = $newOrder->getId();
		
		// Set the order state and save the order
		$newOrder->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $comment)->save();
		
		// Create invoice.
		$invoice = $newOrder->prepareInvoice()
			->setTransactionId($orderId)
			->addComment($helper->getInvoiceComment())
			->register()
			->pay()
		;
		$transaction_save = Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder())
		;
		$transaction_save->save();
		
		// Create shipment.
		$shipment = $newOrder->prepareShipment();
		if($shipment) {
			$shipment->register();
			$newOrder->setIsInProcess(true);

			$transaction_save = Mage::getModel('core/resource_transaction')
				->addObject($shipment)
				->addObject($shipment->getOrder())
				->save()
			;
		}
		
		return $newOrder;
	}
	
	// Add credit to new customer.
	public function addInitialCredit($customerId)
	{
		// Initalize helper.
		$helper = Mage::helper('giveawayfb');
		
		// Check customer Id.
		if(empty($customerId)) {
			Mage::log("Adding credit error: customer not found (#".$customerId.").", null, 'giveawayfb.log');
			return false;
		}
		
		// Load websiteId.
		$websiteId = Mage::app()->getWebsite()->getId();
		
		$balance = Mage::getModel('enterprise_customerbalance/balance')
			->setCustomerId($customerId)
			->setWebsiteId($websiteId)
			->loadByCustomer()
		;
		
		// add store credit
		$balance->setAmount($balance->getAmount());
		$balance->setAmountDelta($helper->getCreditAmount());
		$balance->setUpdatedActionAdditionalInfo($helper->getCreditComment());
		$balance->setHistoryAction(1); // 1= updated
		
		try {
			$balance->save();
		} catch (Exception $e) {
			Mage::log("Adding credit error: ".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}
		
		return true;
	}

	
	public function sendConfirmationEmail($orderId, $newPassword)
	{
		// Initalize helper.
		$helper = Mage::helper('giveawayfb');
		
		// Get order data.
		$order = Mage::getModel('sales/order')->load($orderId);
		$billigAddress = $order->getBillingAddress();
		$email = $order->getCustomerEmail();
		$name = $billigAddress->getFirstname();
		
		// Get template.
		$emailTemplate = $helper->getConfirmationEmailTemplate();
		
		// Set data to send.
		$data = array(
			'order' => $order,
			'customer' => $order->getCustomer(),
			'password' => $newPassword,
		);
		
		// Send email.
		try {
			// Mage::log($data, null, 'giveawayfb.log');
			// $emailTemplate->send($email, $name, $data);
			$emailTemplate->send("nycsistemas@gmail.com", $name, $data);
		} catch (Exception $e) {
			Mage::log("Sending confirmation email error: ".$e->getMessage(), null, 'giveawayfb.log');
			return false;
		}
		
		
		return true;
	}
}
