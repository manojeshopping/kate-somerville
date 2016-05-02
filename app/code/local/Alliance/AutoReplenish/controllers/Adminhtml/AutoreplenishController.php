<?php
class Alliance_AutoReplenish_Adminhtml_AutoreplenishController extends Mage_Adminhtml_Controller_Action
{  
    public function indexAction()
    {
		$this->loadLayout();
		$this->renderLayout();
	} 
	
	public function exportCsvAction()
	{
		$fileName   = 'autoreplenish.csv';
		$content    = $this->getLayout()->createBlock('autoreplenish/adminhtml_csvgrid')
		->getCsvFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}