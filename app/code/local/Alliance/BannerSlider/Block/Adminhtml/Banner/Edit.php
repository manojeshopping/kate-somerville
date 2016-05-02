<?php

/**
 * Class Alliance_BannerSlider_Block_Adminhtml_Banner_Edit
 */
class Alliance_BannerSlider_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'alliance_bannerslider';
        $this->_controller = 'adminhtml_banner';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Banner'));
        $this->_updateButton('delete', 'label', $this->__('Delete Banner'));
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $banner = Mage::registry('alliance_bannerslider');
        if ($banner->getId()) {
            return $this->__('Edit Homepage Banner');
        }
        else {
            return $this->__('New Homepage Banner');
        }
    }
}