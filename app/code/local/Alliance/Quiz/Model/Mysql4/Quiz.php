<?php

class Alliance_Quiz_Model_Mysql4_Quiz extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the quiz_id refers to the key field in your database table.
        $this->_init('quiz/quiz', 'quiz_id');
    }
}