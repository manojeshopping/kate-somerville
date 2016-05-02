<?php
/**
 * Product feed controller. Handles manual execution requests from the admin
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Adminhtml_Ordergroove_Product_FeedController extends Mage_Adminhtml_Controller_Action{
	
	/**
	 * Attempt to generate the feed and upload to server
	 */
	public function generateAction(){
		$feed	=	Mage::helper('ordergroove/product_feed');
		$result	=	$feed->generate(TRUE);
		if($result){
			Mage::getSingleton('adminhtml/session')->addSuccess("Product feed was successfully generated and uploaded");
		}
		else{
			Mage::getSingleton('adminhtml/session')->addError("Product feed could not be uploaded: ".$feed->getErrorMessages().". Please review error logs for further details.");
		}
		$this->_redirectReferer();
	}
	
	/**
	 * Download feed
	 */
	public function downloadAction(){
		$feed	=	Mage::helper('ordergroove/product_feed');
		$fileDataArray	=	array(
			'type'	=>	'filename',
			'value'	=>	$feed->getExportDirectory().$this->getRequest()->getParam('file'),
			'rm'	=>	0
		);
		$this->_prepareDownloadResponse($this->getRequest()->getParam('file'), $fileDataArray);
	}
	
	/**
	 * Validate that the admin user has permission
	 * to access this function.
	 * 
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('ordergroove');
	}
}
?>
