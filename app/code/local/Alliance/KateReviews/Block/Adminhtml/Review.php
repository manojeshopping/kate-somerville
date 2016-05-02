<?php
class Alliance_KateReviews_Block_Adminhtml_Review extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alliance_katereviews';
        $this->_controller = 'adminhtml_review';
        $this->_headerText = $this->__('All Product Reviews');

        parent::__construct();
        $this->removeButton('add');
    }
}