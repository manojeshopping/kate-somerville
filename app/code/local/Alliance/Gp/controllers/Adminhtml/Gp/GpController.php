<?php
 
class Alliance_Gp_Adminhtml_Gp_GpController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('gp/gp')
		;
		return $this;
	}

	public function indexAction()
	{
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function reimportAction()
	{
		$ordersId = $this->getRequest()->getParam('order_id');
		
		if(!is_array($ordersId)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select order(s).'));
		} else {
			try {
				$gpModel = Mage::getModel('gp/gp');
				foreach ($ordersId as $id) {
					$gpModel->load($id)
						->setStatus('reimported')
						->save()
					;
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('tax')->__(
						'Total of %d record(s) will be reimported in the next batch.', count($ordersId)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}


	public function filesAction()
	{
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function downloadAction()
	{
		// Get file id.
		$fileId = $this->getRequest()->getParam('file_id');
		if(empty($fileId) || ! is_numeric($fileId)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select file.'));
			$this->_redirect('*/*/files');
		}
		
		// Load file.
		$file = Mage::getModel('gp/gpfiles')->load($fileId);
		$fileName = $file->getFileName();
		if(empty($fileName)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Error to get Filename.'));
			$this->_redirect('*/*/files');
		}
		
		// Get absolute path file.
		$localFolder = Mage::helper('gp')->getLocalFolder();
		$file = $localFolder.$fileName;
		
		// Check File.
		if(!file_exists($file)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('The file does not exist.'));
			$this->_redirect('*/*/files');
		}
		
		$content = file_get_contents($file);
		$this->_prepareDownloadResponse($fileName, $content);
	}

	/**
	* Product grid for AJAX request.
	* Sort and filter result for example.
	*/
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('gp/adminhtml_gp_grid')->toHtml()
		);
	}
}

