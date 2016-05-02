<?php

/**
 * Class Alliance_Giveawayfb_Block_Giveawayfb
 */
class Alliance_Giveawayfb_Block_Giveawayfb extends Mage_Core_Block_Template
{
	// Get action for verification form.
	public function getCustomerVerificationAction()
	{
		return Mage::helper('giveawayfb')->getVerificationURL();
	}
	
	// Get action for creation form.
	public function getCreationAction()
	{
		return Mage::helper('giveawayfb')->getCreationPostURL();
	}
	
	// Get action for creation samples form.
	public function getCreationSamplesAction()
	{
		return Mage::helper('giveawayfb')->getCreationSamplesPostURL();
	}
	
	// Get action for creation samples form.
	public function getCreationAddressAction()
	{
		return Mage::helper('giveawayfb')->getCreationAddressPostURL();
	}
	
	// Get action for resend email.
	public function getResendAction()
	{
		return Mage::helper('giveawayfb')->getResendPostURL();
	}
	
	// Get public key for captcha validation.
	public function getCaptchaPublicKey()
	{
		return Mage::helper('giveawayfb')->getCaptchaConfig('public');
	}
	
	//Get session data.
	public function getModuleData($key)
	{
		return Mage::helper('giveawayfb')->getSessionData($key);
	}

	// Get site protocol.
	public function siteProtocol() {
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		return $protocol;
	}
}

