<?php
class Alliance_Kiosk_Model_Answer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('kiosk/answer');
    }
}