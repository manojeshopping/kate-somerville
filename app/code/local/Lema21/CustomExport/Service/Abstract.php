<?php 

abstract class Lema21_CustomExport_Service_Abstract
{
	public function getTotalQtyItemsOrdered($order) {
        $qty = 0;
        $orderedItems = $order->getItemsCollection();
        foreach ($orderedItems as $item)
        {
            if (!$item->isDummy()) {
                $qty += (int)$item->getQtyOrdered();
            }
        }
        return $qty;
    }
	
	public function getCustomerGroupName($order) {
		$customerGroupId = $order->getCustomerGroupId();					// alliance-global  Export To Csv file
		$group = Mage::getModel('customer/group')->load($customerGroupId);	// alliance-global  Export To Csv file
		return $group->getCode();	
	}
	
	public function getBillingAddressCountryModelName($order) {
		$billingAddress = $order->getBillingAddress();
		return $billingAddress->getCountryModel()->getName();
	}
	
	public function getShippingAddressCountryModelName($order) {
		$shippingAddress = $order->getShippingAddress();
		return $shippingAddress->getCountryModel()->getName();
	}
	
	public function getStoreName($order) 
    {
        $storeId = $order->getStoreId();
        if (is_null($storeId)) {
            return $this->getOrder()->getStoreName();
        }
        $store = Mage::app()->getStore($storeId);
        $name = array(
        $store->getWebsite()->getName(),
        $store->getGroup()->getName(),
        $store->getName()
        );
        
		return implode(",", $name);
	}
	
	public function getPaymentMethod($order)
    {
        return $order->getPayment()->getMethod();
    }
	
	public function getShippingMethod($order)
    {
        if (!$order->getIsVirtual() && $order->getShippingMethod()) {
            return $order->getShippingMethod();
        }
        return '';
    }
		
	public function formatPrice($price, $formatter) 
    {
        return $formatter->formatPriceTxt($price);
    }
	
	public function getItemSku($item)
    {
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $item->getProductOptionByCode('simple_sku');
        }
        return $item->getSku();
    }
	
	public function getItemOptions($item)
    {
        $options = '';
        if ($orderOptions = $this->getItemOrderOptions($item)) {
            foreach ($orderOptions as $_option) {
                if (strlen($options) > 0) {
                    $options .= ', ';
                }
                $options .= $_option['label'].': '.$_option['value'];
            }
        }
        return $options;
    }
	
	public function getItemOrderOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }
	
	public function getItemTotal($item) 
    {
        return $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount();
    }
}
