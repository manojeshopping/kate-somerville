<?php

/**
 * Class Alliance_BannerSlider_Model_Resource_Banner
 */
class Alliance_BannerSlider_Model_Resource_Banner extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alliance_bannerslider/banner', 'id');
    }
}