<?php
class Alliance_Contact_Helper_Data extends Mage_Core_Helper_Abstract {

     public function getRecaptchaURL()
    {
        return dirname(dirname(dirname(__FILE__))).'/Recaptcha/lib/recaptchalib.php';
    }
}