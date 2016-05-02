<?php

/**
 * Class Alliance_GlobalBanner_Block_GlobalBanner
 */
class Alliance_GlobalBanner_Block_GlobalBanner extends Mage_Core_Block_Template
{

    /**
     * @var Alliance_GlobalBanner_Model_Banner
     */
    public $current_banner;

    /**
     * Constructor loads current banner
     */
    public function __construct()
    {
        $this->current_banner = Mage::helper('alliance_globalbanner')->getCurrentBanner();
    }

    /**
     * Fetches image location relative to media path
     *
     * @return string
     */
    public function getImage()
    {
        return Mage::getBaseUrl('media') . $this->current_banner->getImage();
    }

    /**
     * Fetches value for HREF attribute
     *
     * @return string
     */
    public function getImageLink()
    {
        return $this->current_banner->getImageLink();
    }

    /**
     * Fetches global header text for use as image alt and title text
     *
     * @return string
     */
    public function getImageAlt()
    {
        return $this->current_banner->getImageAlt();
    }

    /**
     * Fetches value for new_tab to determine whether link should open in a new tab or not
     */
    public function getNewTab()
    {
        return $this->current_banner->getNewTab();
    }


}
