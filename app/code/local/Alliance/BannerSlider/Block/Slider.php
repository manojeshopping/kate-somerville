<?php

/**
 * Class Alliance_BannerSlider_Block_Slider
 */
class Alliance_BannerSlider_Block_Slider extends Mage_Core_Block_Template
{
    public $width = 500;
    public $height = 500;
    public $delay = 4000;
    public $banner_collection = 'homepage';

    /**
     * Loads banner collection by slider code, filtering out disasbled banners
     * and banners not belonging to the currently loaded store view
     *
     * @param $slider_code
     */
    public function loadBannerCollection($slider_code)
    {
        $current_store_code = Mage::app()->getStore()->getCode();
        $collection = Mage::getModel('alliance_bannerslider/banner')
            ->getCollection()
            ->addFieldToFilter('status', 'Enabled')
            ->addFieldToFilter('slider_code', $slider_code)
            ->addFieldToFilter('store_code', $current_store_code)
            ->setOrder('sort_order', 'ASC');
        $this->banner_collection = $collection;
    }

    /**
     * Sets the width of the slider in pixels
     *
     * @param $pixels
     */
    public function setWidth($pixels)
    {
        $this->width = $pixels;
    }

    /**
     * Sets the height of the slider in pixels
     *
     * @param $pixels
     */
    public function setHeight($pixels)
    {
        $this->height = $pixels;
    }

    /**
     * Sets the delay of the slider's transition in milliseconds
     *
     * @param $milliseconds
     */
    public function setDelay($milliseconds)
    {
        $this->delay = $milliseconds;
    }
}