<?php
class Alliance_Kiosk_Adminhtml_Kiosk_ReportController extends Mage_Adminhtml_Controller_Action
{
  protected function _initAction()
  {
    $this->_title($this->__('Report'));
    $this->loadLayout()
    ->_setActiveMenu('kiosk/report')
    ->_addBreadcrumb(Mage::helper('adminhtml')->__('Completed Quizes'), Mage::helper('adminhtml')->__('Completed Quizes'));
    return $this;
  }  
  
  public function indexAction()
  {
    $this->loadLayout();

    $this->renderLayout();


  }
  public function detailsAction() {
    Mage::setIsDeveloperMode(true);
    $this->_title($this->__('Report details'));
    $id = $this->getRequest()->getParam('id');
    $report = Mage::getModel('kiosk/report');
    if ($id) {
      $report->load($id);
      if (!$report->getId()) {
        Mage::getSingleton('adminhtml/session')->addError($this->__('This report does not exist.'));
        $this->_redirect('*/*/');
        return;
      }
      $this->_title($this->__('Report #') . $report->getId());
      // Load supplement
      $query = "SELECT question_id, question_text, answer_text FROM  `nordstrom_quiz_completed_details` WHERE answers_group = $id"; 
      $supplement = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
      foreach ($supplement as $entry) {
        $procSupplement[$entry['question_id']]['question_text'] = $entry['question_text'];
        $procSupplement[$entry['question_id']]['answer_text'][] = $entry['answer_text'];
      }

      
      Mage::register('kiosk_report', $report);
      Mage::register('kiosk_supplement', $procSupplement);

      $this->_initAction()
      ->_addBreadcrumb($this->__('Report Details'), $this->__('Report Details'))
      ->renderLayout();   
    }
  }
  public function messageAction()
  {
    $data = Mage::getModel('kiosk/report')->load($this->getRequest()->getParam('id'));
    echo $data->getContent();
  }
  
  public function exportCsvAction()
  {
    // Export CSV function is using native php functionality to generate csv due 
    // to dynamic nature of the quiz. In other words we do not have static fields, like\
    // say with sales_orders.

	
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=kiosk-report-".$date = date('m/d/Y', time()).".csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $report = Mage::getModel('kiosk/report')->getCollection();
    // echo '"ID", "Username", "Age", "Gender", "Customer ID", "Date Completed", "Questions and Answers"';
    echo '"ID", "Location", "Printed", "Emailed", "Username", "Age", "Gender", "Date Completed", "Questions and Answers"';
    echo "\n";
    foreach ($report->getData() as $_entry) {
      echo '"' . $_entry['id'] . '",';
      echo '"' . $_entry['location'] . '",';
      echo '"' . $_entry['printed'] . '",';
      echo '"' . $_entry['emailed'] . '",';
      echo '"' . $_entry['username'] . '",';
      echo '"' . $_entry['age'] . '",';
      echo '"' . $_entry['gender'] . '",';
     // echo '"' . $_entry['customer_id'] . '",';
      $date = new Datetime($_entry['date_completed']);
      echo '"' . $date->format('m/d/Y h:i:s a') . '"';
      $query = "SELECT question_id, question_text, answer_text FROM  `nordstrom_quiz_completed_details` WHERE answers_group = " . $_entry['id'];
      $supplement = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
      foreach ($supplement as $_qa) {
		 echo ',"';
        echo "({$_qa['question_id']}) {$_qa['question_text']} -- {$_qa['answer_text']}";
        echo '"';
      }
      echo "\n";
    }
  } 
      
}
