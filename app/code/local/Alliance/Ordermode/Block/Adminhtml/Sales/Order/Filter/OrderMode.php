<?php
	  
class Alliance_Ordermode_Block_Adminhtml_Sales_Order_Filter_OrderMode extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
	    $options = array(array('label' => '', 'value' => null));
		$options[] = array('label' => "Website", 'value' => "Website");
		$options[] = array('label' => "Amazon Order", 'value' => "Amazon Order");
		$options[] = array('label' => "Phone Order", 'value' => "Phone Order");		
		return $options;
	}
}