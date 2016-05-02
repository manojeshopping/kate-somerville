<?php
class Alliance_KateReviews_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alliance_katereviews';
        $this->_controller = 'adminhtml_report';
        $this->_headerText = $this->__('Product Reviews Report');

        parent::__construct();
        $this->removeButton('add');
    }
}