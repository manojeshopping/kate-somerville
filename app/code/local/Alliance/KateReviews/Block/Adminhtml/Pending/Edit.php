<?php

class Alliance_KateReviews_Block_Adminhtml_Pending_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'alliance_katereviews';
        $this->_controller = 'adminhtml_pending';

        parent::__construct();

		$base64_referer_url = $this->getRequest()->getParam('referer');
		Mage::getSingleton('core/session')->setKatereviewsReferer($base64_referer_url);

		$this->_updateButton('save', 'label', $this->__('Save Review'));
        $this->_updateButton('delete', 'label', $this->__('Delete Review'));
		$this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::helper('core')->urlDecode($base64_referer_url) . '\')');
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('alliance_katereviews')->getId()) {
            return $this->__('Edit Review');
        }
        else {
            return $this->__('New Review');
        }
    }
}