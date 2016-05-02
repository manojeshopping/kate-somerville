<?php
class Alliance_Kiosk_Adminhtml_Kiosk_QuestionController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('kiosk/question')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Questions'));
        return $this;
    }  
  
    public function indexAction()
    {
        $this->_initAction()->renderLayout();

    }
    
    public function newAction() {
    $this->_forward('edit');
  }
  
  public function editAction() {
    $id = $this->getRequest()->getParam('id');
    $question = Mage::getModel('kiosk/question');
    $attributes = Mage::getModel('kiosk/attribute')->getCollection();
    // Prepare attributes for display
    foreach ($attributes->getData() as $attr) {
      $processedAttr[$attr['answer_id']][$attr['attribute_id']] = 1;
    }
   
    if ($id) {
      $question->load($id);
      $query = "SELECT * FROM `nordstrom_quiz_answers` WHERE question_id = $id"; 
      $answer = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
      Mage::register('kiosk_answer', $answer);
      if (!$question->getId()) {
        Mage::getSingleton('adminhtml/session')->addError($this->__('This question does not exist.'));
        $this->_redirect('*/*/');
        return;
      }
    } 
    // 
    $attr = array('skin_concern', 'cleanser_benefits', 'acne_treatment_benefits', 'anti_aging_treatment', 'discoloration_benefits', 'exfoliator_benefits', 'eye_cream_benefits', 'moisturizer_benefits', 'spf_benefits', 'serum_benefits' );
    foreach ($attr as $name) {
    $product_attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $name);
     if ($product_attr->usesSource()) {
            $options[$name] = $product_attr->getSource()->getAllOptions(false);
            
     }
    }
    Mage::register('kiosk_options', $options);
        
       
        
        

    $this->_title($question->getId() ? $this->__('Edit Question') : $this->__('New Question'));
    
     Mage::register('kiosk_question', $question);
     
     Mage::register('kiosk_attributes', $processedAttr);

      $this->_initAction()
          ->_addBreadcrumb($id ? $this->__('Edit Question') : $this->__('New Question'), $id ? $this->__('Edit Question') : $this->__('New Question'))
            ->renderLayout(); 
          

      }
      
      public function saveAction() {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write'); 
        if ($postData = $this->getRequest()->getPost()) {
         
          $question = Mage::getSingleton('kiosk/question');
          $answer = Mage::getModel('kiosk/answer');
          $attributes = Mage::getModel('kiosk/attribute');
          if ($postData['question']['id']) {
            $question->setData('id', $postData['question']['id']); 
          }
          $question->setData('sort_order', $postData['question']['sort_order']); 
          $question->setData('text', $postData['question']['text']); 
          $question->setData('hint_title', $postData['question']['hint_title']); 
          $question->setData('hint_text', $postData['question']['hint_text']); 
          if ($postData['question']['single_answer'] == 1) {
            $question->setData('single_answer', $postData['question']['single_answer']); 
          } else {
            $question->setData('single_answer', 0); 
          }
          try {
                $question->save();   
           }  
           catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirectReferer();
           }
           catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this question.'));
                $this->_redirectReferer();
           }
           if (!$postData['question']['id']) {
            $postData['question']['id'] = $write->fetchOne('SELECT last_insert_id()');
          
          }
        
          foreach ($postData['answer'] as $postAnswer) {
          // IF EXIST -- UPDATE
          
         if ($postAnswer['id']) {
   // SECTION OF ANSWER EXISTS
         $query = "UPDATE `nordstrom_quiz_answers` SET  `answer_text` =  '".$postAnswer['text']."' WHERE `id` =". $postAnswer['id'] ."";
         $write->query($query);
         $write->query("DELETE FROM `nordstrom_quiz_attributes` WHERE answer_id = " . $postAnswer['id'] . "");
         foreach ($postAnswer['attributes'] as $attr) {
              $write->query("INSERT INTO `nordstrom_quiz_attributes` (answer_id, attribute_id) VALUES (". $postAnswer['id'] .",". $attr.")");            
         }
         } else {
   // SECTION IF IT DOES NOT   
         $query = "INSERT INTO `nordstrom_quiz_answers` (id, question_id, answer_text) 
            VALUES (NULL,". $postData['question']['id'].",'".$postAnswer['text']."')";
         $write->query($query);
         $lastAns = $write->fetchOne('SELECT last_insert_id()');   
            foreach ($postAnswer['attributes'] as $attr) {
              $write->query("INSERT INTO `nordstrom_quiz_attributes` (answer_id, attribute_id) VALUES (". $lastAns .",". $attr.")");            
            }
         
         }
         /*
          $query = "INSERT INTO `nordstrom_quiz_answers` (id, question_id, answer_text) 
            VALUES (NULL,". $postData['question']['id'].",'".$postAnswer['text']."')";
             echo $postAnswer['text'] . 'will be created';
          
          
          // Flush all attr.
           
            $lastAns = $write->fetchOne('SELECT last_insert_id()');   
         
          $write->query("DELETE FROM `nordstrom_quiz_attributes` WHERE answer_id = " . $lastAns . "");
            foreach ($postAnswer['attributes'] as $attr) {
              $write->query("INSERT INTO `nordstrom_quiz_attributes` (answer_id, attribute_id) VALUES (". $lastAns .",". $attr.")");            
            }
         */ 
          }

  
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The question has been saved.')); 
           $this->_redirect('*/*/index');
           return;
          
        }
        
      }
      public function deleteAction() {
      $postData = $this->getRequest()->getPost();
      $id = $postData['id'];
      $question = Mage::getModel('kiosk/question')->load($id);
      $write = Mage::getSingleton('core/resource')->getConnection('core_write'); 
      // Delete question
      $question->delete();
      
      // Delete all attributes
      $query = "SELECT id FROM `nordstrom_quiz_answers` WHERE question_id = $id"; 
      $answer = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
      foreach ($answer as $entry) {
       $write->query("DELETE FROM `nordstrom_quiz_attributes` WHERE answer_id = " . $entry['id'] . "");
      }
      // Delete answers
      $write->query("DELETE FROM `nordstrom_quiz_answers` WHERE question_id = " . $id . "");
      // Delete results for this question */
      $write->query("DELETE FROM `nordstrom_quiz_completed_details` WHERE question_id = " . $id . "");
      
      Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The question has been deleted.')); 
             $this->_redirect('*/*/index');
             return;
      
      }
      public function messageAction()
    {
        $data = Mage::getModel('kiosk/question')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

}
