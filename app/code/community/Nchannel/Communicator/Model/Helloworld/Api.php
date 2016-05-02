<?php

class Nchannel_Communicator_Model_Helloworld_Api extends Mage_Api_Model_Resource_Abstract
{
	
	public function hello($msg) {
		return "My Custom HelloWorld API In Magento. Here is Your Message ". $msg ;
	}
}