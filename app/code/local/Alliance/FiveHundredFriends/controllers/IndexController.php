<?php

/**
 * Class Alliance_FiveHundredFriends_IndexController
 */
class Alliance_FiveHundredFriends_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}