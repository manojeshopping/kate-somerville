<?php

class Alliance_Quiz_Model_Mysql4_Quiz_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('quiz/quiz');
    }
}