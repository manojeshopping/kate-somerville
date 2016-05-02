<?php
class Alliance_KateReviews_Block_Adminhtml_Pending extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alliance_katereviews';
        $this->_controller = 'adminhtml_pending';
        $this->_headerText = $this->__('Pending Product Reviews');

        parent::__construct();
        $this->removeButton('add');
    }
}