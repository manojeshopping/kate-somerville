<?php

class Alliance_TOA_Block_Messages extends Mage_Core_Block_Template
{
	
	public function getToaMessage()
	{
		$helper = Mage::helper('toa');
		$cartAmount = $helper->getApplicableSubtotal();
		$TOAcount = $helper->getTOAcount();
		
		$message = '';
		for($i = 0; $i < $TOAcount; $i++) {
			if(! $helper->getModuleEnabled($i) || ! $helper->getMessageEnabled($i)) continue;
			if(! empty($message)) $message .= "<br/>".PHP_EOL;
			
			$minimumAmount = $helper->getMinimumAmount($i);
			if($cartAmount < $minimumAmount) {
				$unqualifiedMessage = $helper->getUnqualifiedMessaget($i);
				$message .= str_replace("{difference}", ($minimumAmount - $cartAmount), $unqualifiedMessage);
			} else {
				$qualifiedMessage = $helper->getQualifiedMessaget($i);
				$message .= $qualifiedMessage;
			}
		}
		
		return $message;
	}
	
}

