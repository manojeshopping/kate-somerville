<?php

/**
 * Class Alliance_EmailCapture_Block_Lightbox
 */
class Alliance_EmailCapture_Block_Lightbox extends Mage_Core_Block_Template
{
    public $signup_width;
    public $signup_height;
    public $signup_image_url;

    public $thankyou_width;
    public $thankyou_height;
    public $thankyou_image_url;

    public function __construct()
    {

    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('emailcapture/general/enabled');
    }
    /**
     * @return bool
     */
    public function alreadyShown()
    {
        return Mage::getModel('core/cookie')->get('alliance_emailcapture_shown') == true;
    }

    /**
     * @return bool
     */
    public function reportShown()
    {
        if (Mage::getModel('core/cookie')->set('alliance_emailcapture_shown', 'true', 315360000, '/')) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getSignupWidth()
    {
        if (!isset($this->signup_width)) {
            $signup_size_config = Mage::getStoreConfig('emailcapture/general/signup_size');
            $exploded = explode('x', $signup_size_config);
            $this->signup_width = intval($exploded[0]);
        }
        return $this->signup_width;
    }

    /**
     * @return int
     */
    public function getSignupHeight()
    {
        if (!isset($this->signup_height)) {
            $signup_size_config = Mage::getStoreConfig('emailcapture/general/signup_size');
            $exploded = explode('x', $signup_size_config);
            $this->signup_height = intval($exploded[1]);
        }
        return $this->signup_height;
    }

    /**
     * @return int
     */
    public function getThankyouWidth()
    {
        if (!isset($this->thankyou_width)) {
            $signup_size_config = Mage::getStoreConfig('emailcapture/general/thankyou_size');
            $exploded = explode('x', $signup_size_config);
            $this->thankyou_width = intval($exploded[0]);
        }
        return $this->thankyou_width;
    }

    /**
     * @return int
     */
    public function getThankyouHeight()
    {
        if (!isset($this->thankyou_height)) {
            $signup_size_config = Mage::getStoreConfig('emailcapture/general/thankyou_size');
            $exploded = explode('x', $signup_size_config);
            $this->thankyou_height = intval($exploded[1]);
        }
        return $this->thankyou_height;
    }

    /**
     * @return string
     */
    public function getSignupImageUrl()
    {
        if (!isset($this->signup_image_url)) {
            $helper = Mage::helper('alliance_emailcapture');
            $media_base_url = Mage::getBaseUrl('media');
            $thankyou_media_path = $helper->getSignupMediaPath();
            $thankyou_image_path = Mage::getStoreConfig('emailcapture/general/signup_image');
            $this->signup_image_url = $media_base_url . $thankyou_media_path . $thankyou_image_path;
        }
        return $this->signup_image_url;
    }

    /**
     * @return string
     */
    public function getThankyouImageUrl()
    {
        if (!isset($this->thankyou_image_url)) {
            $helper = Mage::helper('alliance_emailcapture');
            $media_base_url = Mage::getBaseUrl('media');
            $signup_media_path = $helper->getThankyouMediaPath();
            $signup_image_path = Mage::getStoreConfig('emailcapture/general/thankyou_image');
            $this->thankyou_image_url = $media_base_url . $signup_media_path . $signup_image_path;
        }
        return $this->thankyou_image_url;
    }

	/**
	 * @return mixed
	 */
	public function getThankyouLinkText()
	{
		return Mage::getStoreConfig('emailcapture/general/thankyou_link_text');
	}

	/**
	 * @return mixed
	 */
	public function getThankyouLinkUrl()
	{
		return Mage::getStoreConfig('emailcapture/general/thankyou_link_url');
	}

	/**
	 * @return mixed
	 */
	public function getThankyouDisclaimerText()
	{
		return Mage::getStoreConfig('emailcapture/general/thankyou_disclaimer_text');
	}

	/**
	 * @return mixed
	 */
	public function getThankyouCouponCode()
	{
		return Mage::getStoreConfig('emailcapture/general/thankyou_coupon_code');
	}
}
