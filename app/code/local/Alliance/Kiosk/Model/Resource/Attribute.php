<?php
class Alliance_Kiosk_Model_Resource_Attribute extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('kiosk/attribute', 'answer_id');
    }
}
