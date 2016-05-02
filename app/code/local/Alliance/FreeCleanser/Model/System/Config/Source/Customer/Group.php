<?php


class Alliance_FreeCleanser_Model_System_Config_Source_Customer_Group
{
    protected $_options;
	

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
            array_unshift($this->_options, 
				array('value'=> '', 'label'=> Mage::helper('adminhtml')->__('-- Please Select --')),
				array('value'=> '-1', 'label'=> Mage::helper('adminhtml')->__('Not Logged In Users'))
			);
        }
        return $this->_options;
    }
}
