<?php

/**
 * Class Alliance_BannerSlider_Block_Adminhtml_Banner
 */
class Alliance_BannerSlider_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alliance_bannerslider';
        $this->_controller = 'adminhtml_banner';
        $this->_headerText = $this->__('Homepage Banners');

        parent::__construct();
    }
}