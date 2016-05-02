<?php
class KissMetrics_Analytics_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getWriteKey()
    {     
        return Mage::getStoreConfig('kissmetrics_analytics/options/write_key');
    }


	public function getKissItemData($item) {
		
		$all_options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
		$attributes = isset($all_options['attributes_info']) ? $all_options['attributes_info'] : array();
		$options = array();
		foreach ($attributes as $option) {
			$options[] = $option['label'].': '.$option['value'];
		}
		
        $data = array(
        	'sku' => $item->getSku(),
			'name' => $item->getName(),
			'price' => $item->getPrice(),
			//'options' => $options,
			'qty' => $item->getQty(),
        );

		if(count($options)) $data['options'] = implode('; ',$options);

		return $data;

	}



	public function getKissPurchaseItemData($item, $type = null) {
		
		$all_options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
		$attributes = isset($all_options['attributes_info']) ? $all_options['attributes_info'] : array();
		$options = array();
		foreach ($attributes as $option) {
			$options[] = $option['label'].': '.$option['value'];
		}
		
		$qty = ($type == 'order') ? $item->getQtyOrdered() : $item->getQty();
		
        $data = array(
        	'product_sku' => $item->getSku(),
			'product_name' => $item->getName(),
			'product_price' => $item->getPrice(),
			//'options' => $options,
			'product_qty' => $qty,
			'product_subtotal' => ( $item->getPrice() * $qty ),
        );

		if(count($options)) $data['options'] = implode('; ',$options);

		return $data;

	}



////////////////////////////

    
    public function isAdmin()
    {
        return Mage::app()->getStore()->isAdmin();
    }
    
    public function isEnabled()
    {
        return !$this->isAdmin() && $this->getWriteKey();
    }
    
}