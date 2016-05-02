<?php
class Alliance_Salesgrid_Block_Adminhtml_Sales_Order_Filter_CustomerGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
		$groups = Mage::getResourceModel('customer/group_collection')
                    ->addFieldToFilter('customer_group_id', array('gteq' => 0))
                    ->load()
					->toOptionHash();
				
		$options = array(array('label' => '', 'value' => null));
	
		foreach( $groups as $key => $elem){
			$options[] = array('label'  => $elem, 'value' => $key  ); 
		}
		return $options;
	}
}