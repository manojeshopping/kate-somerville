<?php
class Alliance_Kiosk_Model_Resource_Answer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('kiosk/answer');
    }
}
