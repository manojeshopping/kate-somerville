<?php
require("Mage/Contacts/controllers/IndexController.php");
require_once(Mage::helper('contact')->getRecaptchaURL());

class Alliance_Contact_IndexController extends Mage_Contacts_IndexController
{
    const CAPTCHA_PUBLIC = 'alliance_recaptcha/recaptcha_keys/public_key';
    const CAPTCHA_PRIVATE = 'alliance_recaptcha/recaptcha_keys/private_key';

	
   public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function successAction()
    {

        $this->loadLayout();
        $this->renderLayout();
    }
	
    public function submitAction()
    {
        $privatekey = Mage::getStoreConfig(self::CAPTCHA_PRIVATE); // btpdev key
        $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		

		if($resp->is_valid){
			
			$recepientName = Mage::getStoreConfig('contacts/email/sender_email_identity');
			$recepientEmail = Mage::getStoreConfig('contacts/email/recipient_email');
			$templateId = Mage::getStoreConfig('contacts/email/email_template');
			$storeId = Mage::app()->getStore()->getId();
			$sender = array('name' => $_POST['ctfirstname'], 'email' => $_POST['ctemail']);
		
			$vars = array(
				'ctfirstname' => $_POST['ctfirstname'],
				'ctlastname' => $_POST['ctlastname'],
				'ctemail' => $_POST['ctemail'],
				't1' => $_POST['t1'],
				't2' => $_POST['t2'],
				't3' => $_POST['t3'],
				'street1' => $_POST['street1'],
				'street2' => $_POST['street2'],
				'city' => $_POST['city'],
				'state' => $_POST['state'],
				'zip' => $_POST['zip'],
				'ctmessages' => $_POST['ctmessages']
			);
			
			$translate  = Mage::getSingleton('core/translate');
			
			Mage::getModel('core/email_template')
			->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);	
			
			$translate->setTranslateInline(true);            
			echo "Thank you for contacting us. We will respond to your inquiry shortly.";
			return;	
		}else{
			echo "Unable to submit your request. Please try to submit your input again with a correct Security Code.";
			return;
		}
		
    }

}
