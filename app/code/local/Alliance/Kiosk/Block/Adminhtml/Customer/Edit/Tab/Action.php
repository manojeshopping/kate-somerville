<?php
class Alliance_Kiosk_Block_Adminhtml_Customer_Edit_Tab_Action 
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface {
 
    /**
     * Set the template for the block
     *
     */
    public function _construct()
    {
        parent::_construct();
        $id = $this->getRequest()->getParam('id');
        $query = "SELECT answers_group FROM  `nordstrom_quiz_completed` WHERE customer_id = $id"; 
        $completed = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        if ($completed) { // If customer has a quiz on his name
        $report = Mage::getModel('kiosk/report');
        
        $report->load($completed);
        $query = "SELECT question_id, answer_id FROM  `nordstrom_quiz_completed_details` WHERE answers_group = $completed"; 
        $supplement = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
        foreach ($supplement as $entry) {
          $procSupplement[$entry['question_id']][] = $entry['answer_id'];
        }

        // Load Questions
        $query = "SELECT id, text FROM  `nordstrom_quiz_questions` WHERE 1"; 
        $questions = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
        foreach ($questions as $question) {
          $procQuestions[$question['id']] = $question['text'];
        }
        // Load Answers
        $query = "SELECT id, answer_text FROM  `nordstrom_quiz_answers` WHERE 1"; 
        $answers = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
        foreach ($answers as $answer) {
          $procAnswers[$answer['id']] = $answer['answer_text'];
        }
        Mage::register('kiosk_question', $procQuestions);
        Mage::register('kiosk_report', $report);
        Mage::register('kiosk_supplement', $procSupplement);
        Mage::register('kiosk_answer', $procAnswers);
        } else {
        $nodata = 123;
        Mage::register('kiosk_nodata',  $nodata);
        }
        $this->setTemplate('kiosk/nordstrom.phtml');
    }
     
    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Nordstrom Quiz');
    }
    

    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Click here to view nordstrom quiz results');
    }
     
    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
     
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
    public function getAfter()
    {
        return 'tags';
    }
 

 
}


