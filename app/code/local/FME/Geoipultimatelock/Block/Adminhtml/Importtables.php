<?php

class FME_Geoipultimatelock_Block_Adminhtml_Importtables extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('geoipultimatelock/importtables.phtml');
        $this->setFormAction(Mage::getUrl('*/*/updateTables'));
    }

    protected function _beforeToHtml() {
        return parent::_beforeToHtml();
    }

}