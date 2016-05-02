<?php

/**
 * Class Alliance_GlobalBanner_Model_Resource_Banner
 */
class Alliance_GlobalBanner_Model_Resource_Banner extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alliance_globalbanner/banner', 'id');
    }
}