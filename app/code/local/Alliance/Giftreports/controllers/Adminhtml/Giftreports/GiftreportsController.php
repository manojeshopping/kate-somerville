<?php

class Alliance_Giftreports_Adminhtml_Giftreports_GiftreportsController extends Mage_Adminhtml_Controller_Action {
    
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('giftreports/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Giftcard Reports'), Mage::helper('adminhtml')->__('Giftcard Reports'));

        return $this;
    }

    public function indexAction() {
	    $this->_initAction()
                ->renderLayout();				
	}

	public function redemptionAction() { 
	   $this->_initAction()
                ->renderLayout();
	}
 /**
     * Render GCA grid
     */
    public function gridAction()
    {
     
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('giftreports/adminhtml_giftreports_grid', 'giftreports.grid')
                ->toHtml()
        );
    }	
   public function exportCsvAction()
    {
        $fileName   = 'giftreports.csv';
        $content    = $this->getLayout()->createBlock('giftreports/adminhtml_giftreports_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'giftreports.xml';
        $content    = $this->getLayout()->createBlock('giftreports/adminhtml_giftreports_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
  

  
}
