<?php
class Alliance_Sweepstakes_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->_redirect('/');
		$this->renderLayout();
	}
	
	public function confirmAction()
	{
		$this->loadLayout();
		$this->_redirect('/');
		$this->renderLayout();
	}
}

