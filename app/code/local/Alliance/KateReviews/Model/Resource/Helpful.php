<?php

class Alliance_KateReviews_Model_Resource_Helpful extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('alliance_katereviews/helpful', 'id');
    }
}