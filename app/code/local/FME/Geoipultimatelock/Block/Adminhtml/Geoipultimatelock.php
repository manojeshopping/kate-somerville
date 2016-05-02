<?php

class FME_Geoipultimatelock_Block_Adminhtml_Geoipultimatelock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_geoipultimatelock';
        $this->_blockGroup = 'geoipultimatelock';
        $this->_headerText = Mage::helper('geoipultimatelock')->__('ACL Manager');
        $this->_addButtonLabel = Mage::helper('geoipultimatelock')->__('Add ACL');
        parent::__construct();
    }

}