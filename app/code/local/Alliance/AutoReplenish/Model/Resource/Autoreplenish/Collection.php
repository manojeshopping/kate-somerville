<?php

class Alliance_AutoReplenish_Model_Resource_Autoreplenish_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
       $this->_init('autoreplenish/autoreplenish');
    }
}