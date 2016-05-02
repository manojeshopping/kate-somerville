<?php

/**
 * Class Alliance_BannerSlider_Model_Resource_Slider
 */
class Alliance_BannerSlider_Model_Resource_Slider extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alliance_bannerslider/slider', 'id');
    }
}