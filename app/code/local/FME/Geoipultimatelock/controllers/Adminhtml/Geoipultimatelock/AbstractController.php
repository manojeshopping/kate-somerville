<?php
/**
 * Geoip Ultimate Lock extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Geoipultimatelock
 * @author     R.Rao <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
class FME_Geoipultimatelock_Adminhtml_Geoipultimatelock_AbstractController extends Mage_Adminhtml_Controller_Action {

    public function newConditionHtmlAction() {

        if ($this->_validateFormKey()) {
            $id = $this->getRequest()->getParam('id');
            $typeArr = $this->getRequest()->getParam('type') ? $this->getRequest()->getParam('type') : 'geoipultimatelock-rule_condition_combine';
            $typeArr = explode('|', str_replace('-', '/', $typeArr));
            $type = $typeArr[0];
            $prefix = ($this->getRequest()->getParam('prefix')) ? $this->getRequest()->getParam('prefix') : 'conditions';
            $rule = ($this->getRequest()->getParam('rule')) ? base64_decode($this->getRequest()->getParam('rule')) : 'geoipultimatelock/geoipultimatelock';

            $model = Mage::getModel($type)
                    ->setId($id)
                    ->setType($type)
                    ->setRule(Mage::getModel($rule))
                    ->setPrefix($prefix);
            if (!empty($typeArr[1])) {
                $model->setAttribute($typeArr[1]);
            }

            if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
                $model->setJsFormObject($this->getRequest()->getParam('form'));
                $html = $model->asHtmlRecursive();
            } else {
                $html = '';
            }
            $this->getResponse()->setBody($html);
        }
    }

    /**
     * Returns true when admin session contain error messages
     */
    protected function _hasErrors() {
        return (bool) count($this->_getSession()->getMessages()->getItemsByType('error'));
    }

    /**
     * Set title of page
     */
    protected function _setTitle($action) {
        if (method_exists($this, '_title')) {
            $this->_title($this->__('Geoip Ultimate Lock Rules'))->_title($this->__($action));
        }
        return $this;
    }

}
