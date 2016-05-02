<?php

class Alliance_KateReviews_Model_Resource_Topcontributor extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alliance_katereviews/topcontributor', 'id');
    }
}