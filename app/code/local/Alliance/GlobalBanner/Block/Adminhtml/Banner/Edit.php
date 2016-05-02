<?php

/**
 * Class Alliance_GlobalBanner_Block_Adminhtml_Homepage_Banner_Edit
 */
class Alliance_GlobalBanner_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alliance_globalbanner';
        $this->_controller = 'adminhtml_banner';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Global Banner'));
        $this->_updateButton('delete', 'label', $this->__('Delete Global Banner'));
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $banner = Mage::registry('alliance_globalbanner');
        if ($banner->getId()) {
            return $this->__('Edit Global Banner');
        } else {
            return $this->__('New Global Banner');
        }
    }
}
