<?php    
class Alliance_Kiosk_NordstromController extends Mage_Core_Controller_Front_Action {

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
 
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }   
    
    public function indexAction()
    {
        $id = Mage::getSingleton('customer/session')->getId();
        $query = "SELECT id FROM  `nordstrom_quiz_completed` WHERE customer_id = " . $id . ""; 
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
        
        
        $this->loadLayout();
        $this->renderLayout();
    }

}    
