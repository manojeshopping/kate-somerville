<?php
/**
 * Event observer model
 *
 *
 */
class Alliance_Ordermode_Model_Observer
{
    /**
     * Adds virtual grid column to order grid records generation
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function addColumnToResource(Varien_Event_Observer $observer)
    {
        /* @var $resource Mage_Sales_Model_Mysql4_Order */
   /*     $resource = $observer->getEvent()->getResource();
        $resource->addVirtualGridColumn(
            'order_mode',
            'sales/order',
            array('order_mode' => 'entity_id'),
            'order_mode'
        );*/
    }
	
	public function setOrderModeValues(Varien_Event_Observer $observer){
		
		$order = $observer->getEvent()->getOrder();
		Mage::log("----------------------------------------", null, "order_data_name.log");
	    Mage::log("----------------------------------------", null, "order_data_name.log");


		if($order) {
			Mage::log("Entity ID : " . $order->getEntityId(), null, "order_data_name.log");

			if($order->getEntityId()) {
				Mage::log("CustomerEmail : " . $order->getCustomerEmail(), null, "order_data_name.log");

				if($order->getCustomerEmail()) {
					list($emailUsername, $domain) = explode("@",$order->getCustomerEmail());
					
					Mage::log("DOMAIN : " . $domain, null, "order_data_name.log");
					
					if($domain == 'marketplace.amazon.com') {
							Mage::log("Amazon Order", null, "order_data_name.log");

						$order->setData('order_mode','Amazon Order'); 
						$order->save();
					} else {
							Mage::log("Sales Order Create: " . Mage::app()->getRequest()->getControllerName()  , null, "order_data_name.log");

						if(Mage::app()->getRequest()->getControllerName() == 'sales_order_create') {
								Mage::log("Phone Order", null, "order_data_name.log");

								$order->setData('order_mode','Phone Order'); 
								$order->save();
						} else
						    Mage::log("---------------- RETURN 1  ------------------", null, "order_data_name.log");

							return;

					}
				} else {
						  Mage::log("Sales Order Create2: " . Mage::app()->getRequest()->getControllerName()  , null, "order_data_name.log");

						if(Mage::app()->getRequest()->getControllerName() == 'sales_order_create') {
								Mage::log("Phone Order", null, "test_data.log");

								$order->setData('order_mode','Phone Order'); 
								$order->save();
						} else
					    Mage::log("---------------- RETURN 2 ------------------", null, "order_data_name.log");

							return;
				}
			}
			
			           Mage::log("---------------- END function  ------------------", null, "order_data_name.log");

		}
	}
}
