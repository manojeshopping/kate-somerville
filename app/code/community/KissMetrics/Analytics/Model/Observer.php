<?php
class KissMetrics_Analytics_Model_Observer
{
    const CONTAINER_BLOCKNAME = 'kissmetrics_analytics_before_body_end';

	public function test($observer) {
		//Mage::log(array_keys($observer->getData()),null,'kiss.log');
		
		//Mage::log(array_keys($observer->getData('product')->getData()),null,'kiss.log');
		
		foreach($observer->getItems() as $item) {
		////Mage::log(array_keys($item->getData()),null,'kiss.log');
		//	//Mage::log(array_keys($item->getData('qty_options')),null,'kiss.log');

		foreach ( $item->getOptions() as $option) {
			//Mage::log($option->getData('code').' '.$option->getData('value'),null,'kiss.log');
		}


		}
		//Mage::log('--',null,'kiss.log');
	}

    
    public function addContainerBlock($observer)
    {
	    
	    if (Mage::registry('kiss_ran')) return;
	    Mage::register('kiss_ran',true);
	    
        $layout = Mage::getSingleton('core/layout');
        if(!$layout)
        {
            //Mage::log("No Layout Object in " . __METHOD__);
            return;
        }
        
        $before_body_end = $layout->getBlock('before_body_end');
        if(!$before_body_end)
        {
            //Mage::log("No before body end in " . __METHOD__);
            return;
        }
        
        if(!Mage::helper('kissmetrics_analytics')->isEnabled())
        {
            return;
        }
        
        $container = $layout->createBlock('kissmetrics_analytics/js', self::CONTAINER_BLOCKNAME);
        $before_body_end->append($container);

	}


	public function setItemNew($observer) {
		$item = $observer->getQuoteItem();
		$item->setIsNew(true);
		return $this;
	}


    public function addToCart($observer)
    {

        $products_added = ( Mage::getSingleton('core/session')->getData('products_added') ) ? Mage::getSingleton('core/session')->getData('products_added') : array();

	    
	    foreach (Mage::getSingleton('checkout/session')->getQuote()->getAllItems() as $item) {
		    
		    if($item->getIsNew()) {
		    	//Mage::log(array_keys($item->getData()),null,'kiss.log');
		    	//Mage::log($item->getData('parent_item_id').' - '.$item->getData('product_type'),null,'kiss.log');
		    	
		    	$add_item = ($item->getParentItem()) ? $item->getParentItem() : $item;
		    	
				$products_added[] = Mage::helper('kissmetrics_analytics')->getKissItemData($add_item);
		    	
		    }
	    }
        Mage::getSingleton('core/session')->setData('products_added',$products_added);
        return $this;
	    
    }

    public function customerRegistered($observer)
    {
        //$customer = $observer->getCustomer();
        Mage::getSingleton('core/session')->setData('customer_register', true);
        
    }


	public function sendOrder($observer) {
		
		$order = $observer->getEvent()->getOrder();
		$api_key = Mage::helper('kissmetrics_analytics')->getWriteKey();
		$method_url = 'https://trk.kissmetrics.com/e';
		$helper  = Mage::helper('kissmetrics_analytics');
		
		try {

			$data = array(
				'_k' => $api_key,
				'_p' => $order->getCustomerEmail(),
				'_n' => 'Completed Order',
				'Order ID' => $order->getIncrementId(),
	/*
				'Order Total' => $order->getGrandTotal(),
				'Order Subtotal' => $order->getSubtotal(),
				'Order Shipping' => $order->getShippingAmount(),
				'Order Discount' => $order->getDiscountAmount(),
				'Order Tax' => $order->getTaxAmount(),
	*/
				'Revenue Total' => $order->getGrandTotal(),
				'Revenue Subtotal' => $order->getSubtotal(),
				'Revenue Shipping' => $order->getShippingAmount(),
				'Revenue Discount' => $order->getDiscountAmount(),
				'Revenue Tax' => $order->getTaxAmount(),
				'Placed From' => $order->getOrderMode(),//($order->getStoreId()) ? 'Frontend' : 'Admin',
			);
	
			$query_str = http_build_query($data);
			$event_url = $method_url.'?'.$query_str;
			$response = file_get_contents($event_url);
		} catch(Exception $e) {
		}
		
		$keys = array(
			'_k' => $api_key,
			'_p' => $order->getCustomerEmail(),
			'_n' => 'Purchased Order Item Final',
			'_t' => time(),
			'_d' => 1,
			'Order ID' => $order->getIncrementId(),
		);
			
		foreach ($order->getAllItems() as $item ) {
			if($item->getParentItemId()) continue;
			try {
				$data = $helper->getKissPurchaseItemData($item, 'order');
				$item_data = array_merge($keys,$data);
				$keys['_t']++;
	
				$query_str = http_build_query($item_data);
				$event_url = $method_url.'?'.$query_str;
				$response = file_get_contents($event_url);
			} catch(Exception $e) {
			}
			
		}
		
	}

}