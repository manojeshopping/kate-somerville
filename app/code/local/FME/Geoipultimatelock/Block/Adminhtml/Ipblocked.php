<?php

class FME_Geoipultimatelock_Block_Adminhtml_Ipblocked extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_ipblocked';
        $this->_blockGroup = 'geoipultimatelock';
        $this->_headerText = Mage::helper('geoipultimatelock')->__('Blocked IPs');
        $this->_addButtonLabel = Mage::helper('geoipultimatelock')->__('Block IP');
        parent::__construct();
    }

}