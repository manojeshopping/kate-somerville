<?php

/**
 * Class Alliance_EmailCapture_Helper_Data
 */
class Alliance_EmailCapture_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * @var Path from base media directory to signup image subdirectory
	 */
	public $signup_media_path;

	/**
	 * @var Path from base media directory to thankyou image subdirectory
	 */
	public $thankyou_media_path;

	/**
	 * Returns path from base media directory to signup image subdirectory
	 *
	 * @return string
	 */
	public function getSignupMediaPath()
	{
		if (isset($this->signup_media_path)) {
			return $this->signup_media_path;
		} else {
			$this->signup_media_path = 'alliance/emailcapture/signup/';
			return $this->signup_media_path;
		}
	}

	/**
	 * Returns path from base media directory to thankyou image subdirectory
	 *
	 * @return string
	 */
	public function getThankyouMediaPath()
	{
		if (isset($this->thankyou_media_path)) {
			return $this->thankyou_media_path;
		} else {
			$this->thankyou_media_path = 'alliance/emailcapture/thankyou/';
			return $this->thankyou_media_path;
		}
	}

	/**
	 * Returns the cookie domain used by EmailCapture
	 *
	 * @return mixed
	 */
	public function getCookieDomain()
	{
		$parsed_default_url = parse_url(Mage::app()->getStore('default')->getBaseUrl());
		$cookie_domain = '.' . $parsed_default_url['host'];
		return $cookie_domain;
	}
}