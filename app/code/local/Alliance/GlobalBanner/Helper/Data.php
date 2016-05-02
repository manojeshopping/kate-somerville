<?php

/**
 * Class Alliance_GlobalBanner_Helper_Data
 */
class Alliance_GlobalBanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Fetches currently active instance of Alliance_GlobalBanner_Model_Banner
     *
     * @return Alliance_GlobalBanner_Model_Banner
     */
    public function getCurrentBanner()
    {
        $active_banners = Mage::getModel('alliance_globalbanner/banner')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter(array('from_date', 'from_date'), array(
                array('lteq' => Mage::getModel('core/date')->date('Y-m-d')),
            ))
            ->addFieldToFilter(array('to_date', 'to_date'), array(
                array('gteq' => Mage::getModel('core/date')->date('Y-m-d')),
                array('null' => true),
            ))
            ->addFieldToFilter('status', array('eq' => 'Enabled'))
            ->setOrder('priority');

        $current_store = Mage::app()->getStore()->getId();
        foreach ($active_banners as $key => $banner) {
            if (!in_array($current_store, explode(',', $banner->getStores()))) {
                $active_banners->removeItemByKey($key);
            }
        }

        $current_page = $this->_getCurrentPageType();
        foreach ($active_banners as $key => $banner) {
            if (!in_array($current_page, explode(',', $banner->getPages()))) {
                $active_banners->removeItemByKey($key);
            }
        }

        $logged_in = Mage::getSingleton('customer/session')->isLoggedIn() ? 1 : 0;
        foreach ($active_banners as $key => $banner) {
            if (!in_array($logged_in, explode(',', $banner->getLoggedInStatus()))) {
                $active_banners->removeItemByKey($key);
            }
        }

        return $active_banners->count() ? $active_banners->getFirstItem() : false;
    }

    protected function _getCurrentPageType()
    {
        $route_name      = $this->getRouteName();
        $controller_name = $this->getControllerName();
        $action_name     = $this->getActionName();
        $module_name     = $this->getModuleName();

        if ($route_name == 'cms'
            && $controller_name == 'index'
            && $action_name == 'index'
        ) return 0;

        if (Mage::registry('current_category')
            && $route_name == 'catalog'
            && $controller_name == 'category'
        ) return 1;

        if (Mage::registry('current_product')
            && $route_name == 'catalog'
            && $controller_name == 'product'
        ) return 2;

        if (in_array($route_name, array('checkout', 'offers', 'onestepcheckout'))
            && in_array($module_name, array('checkout', 'offers', 'onestepcheckout'))
        ) return 3;

        if (in_array($route_name, array('customer', 'wishlist', 'newsletter', 'katerewards', 'subscriptions', 'sales', 'katereviews',
                'enterprise_customerbalance', 'enterprise_giftcardaccount', 'enterprise_invitation', 'storelocator'))
            && in_array($controller_name, array('account', 'address', 'customer', 'order', 'index', 'manage', 'info'))
            && in_array($module_name, array('customer', 'rewards-program', 'subscriptions', 'sales', 'wishlist', 'newsletter', 'katereviews',
                'storecredit', 'giftcard', 'invitation', 'storelocator'))
        ) return 4;

        if ($route_name == 'cms'
            && $controller_name == 'page'
        ) return 5;

        return 6;
    }

    public function getRouteName()
    {
        return Mage::app()->getFrontController()->getRequest()->getRouteName();
    }

    public function getControllerName()
    {
        return Mage::app()->getFrontController()->getRequest()->getControllerName();
    }

    public function getActionName()
    {
        return Mage::app()->getFrontController()->getRequest()->getActionName();
    }

    public function getModuleName()
    {
        return Mage::app()->getFrontController()->getRequest()->getModuleName();
    }
}