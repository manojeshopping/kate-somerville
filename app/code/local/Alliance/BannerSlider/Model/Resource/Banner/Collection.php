<?php

/**
 * Class Alliance_BannerSlider_Model_Resource_Banner_Collection
 */
class Alliance_BannerSlider_Model_Resource_Banner_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('alliance_bannerslider/banner');
    }
}