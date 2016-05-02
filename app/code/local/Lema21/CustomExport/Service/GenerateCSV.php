<?php

class Lema21_CustomExport_Service_GenerateCSV extends Lema21_CustomExport_Service_Abstract
{
	private $_orderIds;
    private $_collectionOrders;
    private $_contentCSV;
	const FILENAME = 'custom_orders.csv';
	const ENCLOSURE = '"';
    const DELIMITER = ',';

    public function __construct($ordersId) {
        $this->_orderIds = $ordersId;
    }
	
	public function call()
    {
        $this->_loadOrderObjects();
        $this->_prepareData();
		return self::FILENAME;
    }

    private function _loadOrderObjects()
    {
        $this->_collectionOrders = array();

        foreach($this->_orderIds as $id) {
            $instance = Mage::getModel("sales/order")->load($id);
            array_push($this->_collectionOrders, $instance);
        }
		Mage::log($instance,null,'$orderids.log');
    }

    private function _prepareData($templateLine)
    {
        $fp = fopen(Mage::getBaseDir('export').'/'.self::FILENAME, 'w');
		$record = array();
		$headerData = $this->_prepareHeaderData();
		fputcsv($fp, $headerData, self::DELIMITER, self::ENCLOSURE);
		
        foreach($this->_collectionOrders as $order) {
			$orderContent = $this->getOrderContent($order);
			$orderItems = $order->getItemsCollection();
			$itemInc = 0;
			foreach ($orderItems as $item) {
				if (!$item->isDummy()) {
					$orderItemContent = $this->getOrderItemContent($item, $order, ++$itemInc);
					$record = array_merge($orderContent, $orderItemContent);
					fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
				}
			}
		}
		fclose($fp);
	}
	
	public function getOrderContent($order)
	{
		$shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
		$CustomerId = $order->getCustomerId();								
		$customerGroupId = $order->getCustomerGroupId();					
		$group = Mage::getModel('customer/group')->load($customerGroupId);	
		$CustomerGroupname = $group->getCode();								
		
        $orderContent = array(
            $order->getRealOrderId(),
            $order->getCreatedAt(),
            $order->getData('order_mode'),									
			$order->getStatus(),										
            $this->getStoreName($order),
            $this->getPaymentMethod($order),
            $this->getShippingMethod($order),
            $this->formatPrice($order->getData('subtotal'), $order),
            $this->formatPrice($order->getData('tax_amount'), $order),
            $this->formatPrice($order->getData('shipping_amount'), $order),
            $this->formatPrice($order->getData('discount_amount'), $order),
            $this->formatPrice($order->getData('grand_total'), $order),
            $this->formatPrice($order->getData('total_paid'), $order),
            $this->formatPrice($order->getData('total_refunded'), $order),
            $this->formatPrice($order->getData('total_due'), $order),
            $this->getTotalQtyItemsOrdered($order),
            $order->getCustomerName(),
            $CustomerGroupname,												
            $order->getCustomerEmail(),
            $shippingAddress ? $shippingAddress->getName() : '',
            $shippingAddress ? $shippingAddress->getData("company") : '',
            $shippingAddress ? $shippingAddress->getData("street") : '',
            $shippingAddress ? $shippingAddress->getData("postcode") : '',
            $shippingAddress ? $shippingAddress->getData("city") : '',
            $shippingAddress ? $shippingAddress->getRegionCode() : '',
            $shippingAddress ? $shippingAddress->getRegion() : '',
            $shippingAddress ? $shippingAddress->getCountry() : '',
            $shippingAddress ? $shippingAddress->getCountryModel()->getName() : '',
            $shippingAddress ? $shippingAddress->getData("telephone") : '',
            $billingAddress->getName(),
            $billingAddress->getData("company"),
            $billingAddress->getData("street"),
            $billingAddress->getData("postcode"),
            $billingAddress->getData("city"),
            $billingAddress->getRegionCode(),
            $billingAddress->getRegion(),
            $billingAddress->getCountry(),
            $billingAddress->getCountryModel()->getName(),
            $billingAddress->getData("telephone"),
        );
		return $orderContent;
	}
	public function getOrderItemContent($item, $order, $itemInc)
	{
		$orderItemContent =  array(
            $itemInc,
            $item->getName(),
            $item->getStatus(),
            $this->getItemSku($item),
            $this->getItemOptions($item),
            $this->formatPrice($item->getOriginalPrice(), $order),
            $this->formatPrice($item->getData('price'), $order),
            (int)$item->getQtyOrdered(),
            (int)$item->getQtyInvoiced(),
            (int)$item->getQtyShipped(),
            (int)$item->getQtyCanceled(),
        	(int)$item->getQtyRefunded(),
            $this->formatPrice($item->getTaxAmount(), $order),
            $this->formatPrice($item->getDiscountAmount(), $order),
            $this->formatPrice($this->getItemTotal($item), $order)
        );
		return $orderItemContent;
	}

	public function _prepareHeaderData()
	{
		$headerData = array(
            'Order Number',
            'Order Date',
            'Order Mode',
            'Order Status',
            'Order Purchased From',
            'Order Payment Method',
            'Order Shipping Method',
            'Order Subtotal',
            'Order Tax',
            'Order Shipping',
            'Order Discount',
            'Order Grand Total',
            'Order Paid',
            'Order Refunded',
            'Order Due',
            'Total Qty Items Ordered',
            'Customer Name',
            'Customer Group Name',                 				
            'Customer Email',
            'Shipping Name',
            'Shipping Company',
            'Shipping Street',
            'Shipping Zip',
            'Shipping City',
        	'Shipping State',
            'Shipping State Name',
            'Shipping Country',
            'Shipping Country Name',
            'Shipping Phone Number',
    		'Billing Name',
            'Billing Company',
            'Billing Street',
            'Billing Zip',
            'Billing City',
        	'Billing State',
            'Billing State Name',
            'Billing Country',
            'Billing Country Name',
            'Billing Phone Number',
            'Order Item Increment',
    		'Item Name',
            'Item Status',
            'Item SKU',
            'Item Options',
            'Item Original Price',
    		'Item Price',
            'Item Qty Ordered',
        	'Item Qty Invoiced',
        	'Item Qty Shipped',
        	'Item Qty Canceled',
            'Item Qty Refunded',
            'Item Tax',
            'Item Discount',
    		'Item Total'
    	);
		return $headerData;
	}
}