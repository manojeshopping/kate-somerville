<?php
class Alliance_Kiosk_Model_Resource_Answer extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('kiosk/answer', 'id');
    }
}
