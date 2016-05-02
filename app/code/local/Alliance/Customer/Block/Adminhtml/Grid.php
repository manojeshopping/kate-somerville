<?php

class Alliance_Customer_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Customer_Grid
{

    public function setCollection($collection)
    {
        $collection
            ->addAttributeToSelect('primary_skin_concern')
            ->addAttributeToSelect('secondary_skin_concern');
        parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('primary_skin_concern',
            array(
            'header'    => Mage::helper('customer')->__('Primary Skin Concern'),
            'width'     => '100',
            'index'     => 'primary_skin_concern',
            'type'      => 'options',
            'options'   => $this->getOptions('primary_skin_concern'),
        ), 'billing_region');
        
        $this->addColumnAfter('secondary_skin_concern',
            array(
            'header'    => Mage::helper('customer')->__('Secondary Skin Concern'),
            'width'     => '100',
            'index'     => 'secondary_skin_concern',
            'type'      => 'options',
            'options'   => $this->getOptions('secondary_skin_concern'),
        ), 'primary_skin_concern');

        $this->sortColumnsByOrder();
        return $this;
    }

    public function getOptions($attributeCode) 
    {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('customer', $attributeCode);
        $result = array();
        foreach ($attribute->getSource()->getAllOptions() as $option) {
           if($option['value']!='') {
                $result[$option['value']] = $option['label'];
           }
        }
        return $result;
     }

}
