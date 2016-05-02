<?php

/**
 * Class Alliance_RegionRoute_IndexController
 */
class Alliance_RegionRoute_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Handles route 'region/index/index'
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}