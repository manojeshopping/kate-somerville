<?php

class Nchannel_Communicator_Model_Source_NchannelEnvironment
{
	public function toOptionArray($isMultiselect)
	{
		return array(
			array('value' => 'https://api.nchannel.com/Events/Neworder/', 'label' => Mage::helper('Communicator')->__('Live (Production)')),
			array('value' => 'http://api.dev.nchannel.com/Events/NewOrder/', 'label' => Mage::helper('Communicator')->__('Dev (Testing)')),
			array('value' => 'http://localhost/api/Events/NewOrder/', 'label' => Mage::helper('Communicator')->__('local (Developer)')),
			// and so on...
			);
	}
}
?>