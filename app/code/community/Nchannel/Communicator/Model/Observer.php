<?php

class Nchannel_Communicator_Model_Observer
{
    /**
     * Magento passes a Varien_Event_Observer object as
     * the first parameter of dispatched events.
     */
	public function orderSaved(Varien_Event_Observer $observer)
	{
		Mage::log("orderSaved",null,'nChannel_Communicator.log');
		$order = $observer->getEvent()->getOrder();
		$origData = $order->getOrigData();
		Mage::log("Order: " . $order->getIncrementId() . " Status: " . $order->getStatus(),null,'nChannel_Communicator.log');
		Mage::log("is object new" . $order->isObjectNew(),null,'nChannel_Communicator.log');
		Mage::log("status: " . $order->getStatus(),null,'nChannel_Communicator.log');
		Mage::log("old status: " . $origData['status'],null,'nChannel_Communicator.log');
		if(($order->getStatus() == "pending" || $order->getStatus() == "processing")  && $origData)
		{
			if($origData['status'] == "holded")
			{
				Mage::log("Order will be sent to nChannel",null,'nChannel_Communicator.log');	
				$this->sendToAPI($observer);
			}else{
				Mage::log("Order will not be sent to nChannel",null,'nChannel_Communicator.log');			
					}
			
		}
		else
			{
			Mage::log("New Order will not be sent to nChannel.",null,'nChannel_Communicator.log');		
				}
	}
	public function sendToAPI(Varien_Event_Observer $observer)
    {
        // Retrieve the product being updated from the event observer
		//$info = phpinfo();
		Mage::log("sendToAPI",null,'nChannel_Communicator.log');
		$enabled = Mage::getStoreConfig('Communicator/Credentials/ENABLED');
		if($enabled)
		{
			$order = $observer->getEvent()->getOrder();
			$origData = $order->getOrigData();
			Mage::log("Order: " . $order->getIncrementId() . " Status: " . $order->getStatus(),null,'nChannel_Communicator.log');

				Mage::log("Processing order to send to nChannel.",null,'nChannel_Communicator.log');
				
				$token = Mage::getStoreConfig('Communicator/Credentials/TOKEN');
				$locationID = Mage::getStoreConfig('Communicator/Credentials/LOCATION');
				$now = new DateTime;
				$time = gettimeofday();
				// Write a new line to var/log/product-updates.log
				$orderID =  $order->getIncrementId();
				$orderXML = "<EventNotification><EventDate>{$now->format('m-d-Y')}</EventDate><EventTime>{$now->format('h:i:s A')}</EventTime><EventSite></EventSite><Event>NewOrder</Event><EventData><OrderNumber>$orderID</OrderNumber></EventData></EventNotification>";
				Mage::log("Order: # {$orderID} created, enabled: {$enabled}", null, 'nChannel_Communicator.log');
				Mage::log("Outbound XML = {$orderXML}", null, 'nChannel_Communicator.log');
				//Create config for URL -> Production/Dev/Local
				// send XML to api
				$url = Mage::getStoreConfig('Communicator/Credentials/URL');
				
				$url.= $locationID . "?token=" . $token;
				Mage::log("Sending XML to nChannel API url: " . $url, null, 'nChannel_Communicator.log');
				//$r = new HttpRequest($url, HttpRequest::METH_POST);
				//$r->setContentType("text/xml charset=utf-8");
				//$r->addRawPostData($orderXML);
				Mage::log("Prepared HttpRequest",null,'nChannel_Communicator.log');
				
				try{
					Mage::log("sending",null,'nChannel_Communicator.log');
					if  (in_array  ('curl', get_loaded_extensions())) {
						Mage::log("curl loaded",null,'nChannel_Communicator.log');
					}
					else{
						Mage::log("curl is not loaded",null,'nChannel_Communicator.log');
					}
					$ch = curl_init($url);
					//curl_setopt($ch, CURLOPT_MUTE, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($ch, CURLOPT_POST, 1);
					//curl_setopt($ch, CURLOPT_HEADER,1);//Include Header In Output
					//curl_setopt($ch, CURLOPT_NOBODY,1);//Set to HEAD & Exclude body
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
					curl_setopt($ch, CURLOPT_POSTFIELDS, "$orderXML");
					//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					Mage::log('sending it now',null,'nChannel_Communicator.log');
					$output = curl_exec($ch);
					if(!curl_errno($ch))
					{
						//Mage::log('Error in sending Order to nChannel',null,'nChannel_Communicator.log');
						$info = curl_getinfo($ch);
						Mage::log('Status Code: ' . $info['http_code'],null,'nChannel_Communicator.log');
						if($info['http_code'] != '200')
						{
							Mage::log('Sending Email to nChannel Support',null,'nChannel_Communicator.log');
							$templateId = Mage::getStoreConfig('Communicator/Support/EMAILTEMPLATE');
							$mailSubject = Mage::getStoreConfig('Communicator/Support/SUBJECT');
							//$email = Mage::getStoreConfig('Communicator/Support/EMAILTO');
							$name = Mage::getStoreConfig('Communicator/Support/EMAILTO');
							$storeId = Mage::app()->getStore()->getId(); 
							$sender = Array('name'  => Mage::getStoreConfig('Communicator/Support/EMAILSENDER'),
								'email' => Mage::getStoreConfig('Communicator/Support/EMAILSENDER'));
							$vars = Array('order' => $order,'payment_html' => $order->getPayment()->getMethodInstance()->getTitle());
							
							$recipients = explode(",",Mage::getStoreConfig('Communicator/Support/EMAILTO'));
							foreach($recipients as $recipient){							
								$translate  = Mage::getSingleton('core/translate');
								Mage::getModel('core/email_template')
									->setTemplateSubject($mailSubject)
									->sendTransactional($templateId, $sender, $recipient, $name, $vars, $storeId);
								$translate->setTranslateInline(true);
							}

						}
					}else
					{
						
						Mage::log("Order sent to nChannel",null,'nChannel_Communicator.log');
					}
					curl_close($ch);
					
					
					
				}catch(HttpException $ex){
					Mage::log("There was an error with communicating with nChannel API. error: " . $ex,null,'nChannel_Communicator.log');
				}
		}else{
			Mage::log("nChannel Communicator is disabled: {$enabled}", null, 'nChannel_Communicator.log');
		}
    }
}
?>
