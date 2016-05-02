<?php
class Alliance_Quiz_Block_Quiz extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getQuiz()     
     { 
        if (!$this->hasData('quiz')) {
            $this->setData('quiz', Mage::registry('quiz'));
        }
        return $this->getData('quiz');
        
    }
}