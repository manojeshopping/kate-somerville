<?php

class Alliance_Gp_Model_Gp extends Mage_Core_Model_Abstract
{
	
	
	public function _construct()
	{
		parent::_construct();
		$this->_init('gp/gp');
	}
	
	
	public function getBatchOrders()
	{
		// Initalize Helper.
		$helper = Mage::helper('gp');
				
		// Get non exporter orders.
		$collection = Mage::getModel('sales/order')
			->getCollection()
			->addAttributeToFilter('created_at', array('from' => $helper->getStartExportDate(),))
			->addFieldToFilter('entity_id', 
				array('nin' => new Zend_Db_Expr("SELECT order_id FROM gp_exported WHERE status = 'exported'"))
			)
		;
		//echo $collection->getSelect()->__toString(); //print Query collection
		
		$limit = $helper->getLimitPerExport();
		if($limit > 0) $collection->getSelect()->limit($limit);
		

		if($collection->getSize() == 0) {
			Mage::log("No Order Found for export", null, 'gpexport.log');
			echo "NO Order Found\n";
			return false;
		}
		
		// Create XML.
		$xml = new SimpleXMLElement("<ORDERS></ORDERS>");
		$orderIds = array();
		foreach($collection as $i => $order) {
			// *** Order Data *** //
			$oXML = $xml->addChild("ORDER");
			$oXML->addChild('ORDER_NUMBER', $order->getIncrementId());
			$oXML->addChild('ORDER_DATE', date('m/d/Y', strtotime($order->getCreatedAt())));
			// *** Order Data *** //


			// *** Payment Data *** //
			$cctype = $this->getCcType($order);
			
			if($cctype != '' && $cctype != 'N/A')  $oXML->addChild("CreditCardName", $cctype);
			// *** Payment Data *** //


			// *** Shipping Address ***//
			$ship = $helper->getShippingAddress($order);

			$shipXML = $oXML->addChild("CUSTOMER_SHIP");
			$shipXML->addChild("SHIP_FIRST", $ship->getFirstname());
			$shipXML->addChild("SHIP_LAST", $ship->getLastname());
			$shipXML->addChild("SHIP_COMP", $ship->getCompany());
			
			$ship_country = $helper->getCountry($ship['country_id']);
			$shipXML->addChild("SHIP_ADDR", $helper->getStreet($ship));
			$shipXML->addChild("SHIP_CITY", $ship->getCity());
			$shipXML->addChild("SHIP_STATE", $helper->getRegion($ship['region'], $ship['country_id']));
			$shipXML->addChild("SHIP_ZIP", $ship->getPostcode());
			$shipXML->addChild("SHIP_CNTRY", $ship_country);
			$shipXML->addChild("SHIP_EMAIL", $order->getCustomerEmail());
			$shipXML->addChild("SHIP_PHONE", $ship->getTelephone());
			$shipXML->addChild("SHIP_FAX", $ship->getFax());
			// *** Shipping Address ***//



			// *** Billing Address ***//
			$bill = $order->getBillingAddress();

			$billXML = $oXML->addChild("CUSTOMER_BILL");
			$billXML->addChild("BILL_FIRST", $bill->getFirstname());
			$billXML->addChild("BILL_LAST", $bill->getLastname());
			$billXML->addChild("BILL_COMP", $bill->getCompany());
			
			$billXML->addChild("BILL_ADDR", $helper->getBillAddress($bill));
			$billXML->addChild("BILL_CITY", $bill->getCity());
			$billXML->addChild("BILL_STATE", $helper->getRegion($bill['region'], $bill['country_id']));
			$billXML->addChild("BILL_ZIP", $bill->getPostcode());
			$billXML->addChild("BILL_CNTRY", $helper->getCountry($bill['country_id']));
			$billXML->addChild("BILL_EMAIL", $order->getCustomerEmail());
			$billXML->addChild("BILL_PHONE", $bill->getTelephone());
			$billXML->addChild("BILL_FAX", $bill->getFax());
			// *** Billing Address ***//

			// *** Items *** //
			$lineXML = $oXML->addChild("LINEITEMS");
			$items = $order->getAllItems();
			
			foreach ($items as $i => $item) {
	
				$productType = $item->getData('product_type');
				
				if ($productType == 'giftcard' || $productType == 'simple' || $productType == 'subscription_simple') {
					$itemXML = $lineXML->addChild('LINEITEM');
					$itemXML->addChild('NAME', $item->getName());
					$itemXML->addChild('SKU', $item->getSku());

					if ($productType == 'simple' || $productType == 'subscription_simple') {
						if ($item->getParentItemId() == '') {
							$loadProduct = Mage::getSingleton('catalog/product')->load($item->getProductId());
							$itemXML->addChild('PRICE', $item->getPrice());
							$itemXML->addChild('COST', $loadProduct->getCost());
						} else {
							$prod = Mage::getSingleton('sales/order_item')->load($item->getParentItemId());
							$loadProduct = Mage::getSingleton('catalog/product')->load($item->getProductId());
							$itemXML->addChild('PRICE', $prod->getPrice());
							$itemXML->addChild('COST', $loadProduct->getCost());
						}
					} else {
						$loadProduct = Mage::getSingleton('catalog/product')->load($item->getProductId());
						$itemXML->addChild('PRICE', $item->getPrice());
						$itemXML->addChild('COST', $loadProduct->getCost());
					}
					$itemXML->addChild('QUANTITY', $item->getQtyOrdered());
				}
			}
			
			$giftCardsAmount = $order->getData('gift_cards_amount');
			if($giftCardsAmount > 0) {
				$loadgiftProduct = $helper->getGiftProduct();
				$itemXML = $lineXML->addChild('LINEITEM');
				$itemXML->addChild('NAME', $loadgiftProduct->getName());
				$itemXML->addChild('SKU', $loadgiftProduct->getSku());
				$itemXML->addChild('PRICE', $giftCardsAmount);
				$itemXML->addChild('COST', $loadgiftProduct->getCost());
				$itemXML->addChild('QUANTITY', -1);
			}
			// *** Items *** //

			
			// *** Shipping Method *** //
			if (is_object($order->getShippingAddress())) {
				$shipMethodXML = $oXML->addChild("SHIPPING");
				
				$shipMethodXML->addChild('CHARGE', $helper->getShippingDescription($order, $ship_country));
				$shipMethodXML->addChild('AMOUNT', Mage::helper('core')->currency($order->getPayment()->getShippingAmount(), true, false));

				$baseDiscountAmount = $order->getData('base_discount_amount');
				if($baseDiscountAmount != '') {
					//$discountAmt =  (abs($order->getData('base_discount_amount')) + $order->getData('gift_cards_amount')) ;
					$discountAmt =  (abs($baseDiscountAmount));
					$shipMethodXML->addChild('DISCOUNT', Mage::helper('core')->currency($discountAmt, true, false));
				} else {
					$discountAmt =  '0.00';
					$shipMethodXML->addChild('DISCOUNT', Mage::helper('core')->currency($discountAmt, true, false));	
				}
				// Added on 14-Sept- 2011
			} else {
				$shipMethodXML = $oXML->addChild("SHIPPING");
				// Added on 14-Sept- 2011
				if($baseDiscountAmount != '') {
					//$discountAmt =  (abs($order->getData('base_discount_amount')) + $order->getData('gift_cards_amount')) ;
					$discountAmt =  (abs($baseDiscountAmount));
					$shipMethodXML->addChild('DISCOUNT', Mage::helper('core')->currency($discountAmt, true, false));
				} else { 
					$discountAmt =  '0.00';
					$shipMethodXML->addChild('DISCOUNT', Mage::helper('core')->currency($discountAmt, true, false));	
				}
			}
			// *** Shipping Method *** //


			// *** Taxes *** //
			$rates = Mage::getModel('sales/order_tax')->getCollection()->loadByOrder($order);
			foreach ($rates as $value) {
				if (!is_null($rates->getData('percent'))) {
					$taxXML = $oXML->addChild("SALESTAX");
					$taxXML->addChild('CHARGE', $value->getData('percent'));
					$taxXML->addChild('AMOUNT', Mage::helper('core')->currency($order->getTaxAmount(), true, false));
				}
			}
			// *** Taxes *** //

			// total 
			$total = $oXML->addChild("TOTAL", Mage::helper('core')->currency($order->getGrandTotal(), true, false));

			// Save order id for update when process finished.
			$orderIds[] = $order->getId();
		}

		// *** Save file in local folder *** //
		$savedFile = $this->saveFile($xml);
		if(! $savedFile) {
			echo "The file could not be saved.\n";
			return false;
		}
		// *** Save file in local folder *** //


		// *** Send file via FTP *** //
		try {
			$uploaded = $this->uploadtoFTP($savedFile);
			if(! $uploaded) {
				echo "The file could not be uploaded.\n";
			    //return false;
			}
			
			echo "File uploaded.\n";
		} catch (Exception $e) {
			$msg = "File put contents error: ".$e->getMessage();
			Mage::log($msg, null, 'gpexport.log');
			echo $msg."\n";
			return false;
		}
		// *** Send file via FTP *** //


		// *** Save orders *** //
		$fileName = basename($savedFile);
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$orderCount = count($orderIds);
		for($i = 0; $i < $orderCount; $i++) {
			$orderId = $orderIds[$i];
			
			// Get order.
			$order = Mage::getModel('sales/order')->load($orderId);
			
			// Save Order.
			$comment = 'Order successfully exported to GP.';
			$order->addStatusHistoryComment($comment);
			$order->save();

			// Save in custom table.
			$values = array(
				'order_id' => $orderId,
				'increment_id' => $order->getIncrementId(),
				'status' => 'exported',
				'customer_email' => $order->getCustomerEmail(),
				'file' => $fileName,
			);
			
			$gpModel = Mage::getModel('gp/gp')->load($orderId);
			$modelData = $gpModel->getData();
			if(empty($modelData)) {
				$connection->beginTransaction();
				$connection->insert('gp_exported', $values);
				$connection->commit();
			} else {
				$gpModel->setData($values)->save();
			}
		}
		Mage::log("Orders saved.", null, 'gpexport.log');
		// *** Save orders *** //
		
		
		// *** Save  File in DB *** //
		$data = array(
			'file_name' => $fileName,
			'file_size' => filesize($savedFile),
			'orders' => $orderCount,
		);
		
		$gpModelFile = Mage::getModel('gp/gpfiles');
		try {
			$gpModelFile->setData($data)->save();
		} catch (Exception $e) {
			$msg = "Save File in DB error: ".$e->getMessage();
			Mage::log($msg, null, 'gpexport.log');
			echo $msg."\n";
			
			return false;
		}
		// *** Save  File in DB *** //
		
		echo "END. Count: ".$orderCount."\n";
		return true;
	}
	
	
	protected function saveFile($xml)
	{	
		$helper = Mage::helper('gp');
		
		try {
			$filename = $helper->getFileNamePrefix() . date('ymdHis') . ".xml";
			$folderName = $helper->getLocalFolder();
			
			if(is_dir($folderName)) {
				$saveFile = $folderName . $filename;
				file_put_contents($saveFile, $xml->asXML());
				Mage::log($saveFile . " written successfully", null, 'gpexport.log');
			
				return $saveFile;
			} else {
				Mage::log($folderName . " folder does not exist", null, 'gpexport.log');
				return false;
			}
		} catch (Exception $e) {
			$msg = "File put contents error: ".$folderName . $filename." - ".$e->getMessage();
			Mage::log($msg, null, 'gpexport.log');
			
			return false;
		}
	}
	
    protected function uploadtoFTP($sourceFile)
	{
		$helper = Mage::helper('gp');
		$ftpData = $helper->getFptConnection();
		
		$conn_id = ftp_connect($ftpData['host']);

		if (!$conn_id) {
			Mage::log("Unable to connect to Host.");
			return false;
		} else {
			$login_result = ftp_login($conn_id, $ftpData['username'], $ftpData['password']);
			if (!$login_result) {
				Mage::log("Invalid Username or Password.");
				return false;
			} else {
				$file = basename($sourceFile);

				$destinationFile = $helper->getRemoteFilePath() . $file; // Specify Destination File Path.
				$upload = ftp_put($conn_id, $destinationFile, $sourceFile, FTP_ASCII);
				$upload = true;
				if($upload) $msg = $file . " moved to FTP successfully.";
				else $msg = "Failed to move ".$file . " to FTP.";
				
				Mage::log($msg, null, 'gpexport.log');
				
				ftp_close($conn_id);
				return $upload;
			}
		}
    }
	

	private function getCcType($order)
	{
		// Initalize Helper.
		$helper = Mage::helper('gp');
			
		$payment = $order->getPayment();
		$paymentInfoBlock = Mage::helper('payment')->getInfoBlock($payment);
		$ccTypeArray = $helper->getCcTypeArray();

		/////////////////// Alliance update //////////////////////////////////////
		$ccTypeArray_auth = $helper->getCcTypeAuthArray();

		$ccTypeName = $paymentInfoBlock->getCcTypeName();
		if(isset($ccTypeArray[$ccTypeName])) {
			$cctype = $ccTypeArray[$ccTypeName];
		} else {
			if($ccTypeName != '' && $ccTypeName != 'N/A' ) {
				$c_getcc_id = $ccTypeName;
				$cc_authcim = Mage::getModel('authcim/authcim')->getCollection()->addFieldToFilter('authcim_id',  $c_getcc_id);								
				if($cc_authcim != "" ) {
					foreach ($cc_authcim as $i => $order_cc_authcim) {
						$cctype = $ccTypeArray_auth[$order_cc_authcim->getCcType()];
					}
				} else {
					$cctype = $ccTypeName;
				}

			} else {
				$cctype = $ccTypeName;
			}
		}

		if($ccTypeName == 'N/A') $cctype = 'SavedCC';   ///Temporary Fix Alliance-Global.

		
		$x_authorize_cards 	= $order->getPayment()->getAdditionalInformation('authorize_cards');  // ARRAY

		if($order->getPayment()->getData('cc_type') == '') {
			if($order->getPayment()->getMethod() == 'authorizenet') {
				foreach($x_authorize_cards as $key => $value) {
					$cctype = $ccTypeArray_auth[$value['cc_type']];
				}
			}
		}
		///////////////////End   Alliance update //////////////////////////////////////
		
		
		return $cctype;
	}

}