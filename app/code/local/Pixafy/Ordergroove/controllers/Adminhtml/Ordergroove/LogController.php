<?php
/**
 * Log entry controller that handles all 
 * log related functions.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Adminhtml_Ordergroove_LogController extends Mage_Adminhtml_Controller_Action{

	/**
	 * Display the default grid
	 */
	public function indexAction(){
		$this->_title($this->__('OrderGroove'))->_title($this->__('Log Entries'));
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Grid function used for
	 * filter and sort calls
	 */
	public function gridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * View / Edit a single log entry
	 */
	public function editAction(){
		if($logId	=	$this->getRequest()->getParam('id')){
			$log	=	Mage::getModel('ordergroove/log')->load($logId);
			if($log->getId()){
				if(!$log->getIsRead()){
					$log->setIsRead(1)->save();
				}
				Mage::register(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_CURRENT_LOG, $log);
				$this->loadLayout();
				$this->_addContent($this->getLayout()->createBlock('ordergroove/adminhtml_log_edit'));
				$this->renderLayout();
			}
		}
	}
	
	/**
	 * Delete a single log entry
	 */
	public function deleteAction(){
		if($logId	=	$this->getRequest()->getParam('id')){
			$log	=	Mage::getModel('ordergroove/log')->load($logId);
			if($log->getId()){
				try{
					$log->delete();
					Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Log with id %s deleted", $logId));
					$this->_redirect('*/*/');
					return;
				}
				catch(Exception $e){
					Mage::getSingleton('adminhtml/session')->addError($this->__("Could not delete log with id %s: %s", $log->getId(), $e->getMessage()));
					$this->_redirect('*/*/');
					return;
				}
			}
			else{
				Mage::getSingleton('adminhtml/session')->addError($this->__("Could not load log with id %s ", $logId));
				$this->_redirect('*/*/');
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError($this->__("No log id provided."));
		$this->_redirect('*/*/index');
		return;
	}
	
	/**
	 * Delete multiple log entries at one time
	 */
	public function massDeleteAction(){
		$logIds	=	$this->getRequest()->getParam('og_logs');
		
		$deleteCount	=	0;
		if(is_array($logIds)){
			if(!empty($logIds)){
				foreach($logIds as $logId){
					$log	=	Mage::getModel('ordergroove/log')->load($logId);
					if($log->getId()){
						$log->delete();
						$deleteCount++;
					}
				}
			}
		}
		
		if($deleteCount > 0){
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__("%s log(s) deleted. ", $deleteCount));
		}
		else{
			Mage::getSingleton('adminhtml/session')->addError($this->__("Could not load or find logs to delete"));
		}
		$this->_redirect('*/*/index');
		return;
	}
}
?>
