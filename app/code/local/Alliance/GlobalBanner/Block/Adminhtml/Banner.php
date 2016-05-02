<?php

/**
 * Class Alliance_GlobalBanner_Block_Adminhtml_Banner
 */
class Alliance_GlobalBanner_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alliance_globalbanner';
        $this->_controller = 'adminhtml_banner';
        $this->_headerText = $this->__('Global Banners');

        parent::__construct();

        $this->_updateButton('add', 'label', Mage::helper('alliance_globalbanner')->__('Add New Global Banner'));
    }
}
