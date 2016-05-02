<?php
class Alliance_Giveawayfb_SkuController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__("Discover Hollywood's Best Kept Secret"));
		$this->renderLayout();
	}
}

