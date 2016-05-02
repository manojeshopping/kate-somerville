<?php

class Alliance_AutoReplenish_Model_Resource_Autoreplenish extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('autoreplenish/autoreplenish', 'autoreplenish_id');
    }
}