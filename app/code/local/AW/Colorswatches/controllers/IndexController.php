<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Colorswatches
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Colorswatches_IndexController extends Mage_Core_Controller_Front_Action
{

    protected function _config($option)
    {
        return Mage::getStoreConfig("awcolorswatches/global/{$option}", Mage::app()->getStore()->getId());
    }

    public function indexAction()
    {
        $_helper = Mage::helper('catalog/output');
        $product = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('id'));
        if (!Mage::helper('awcolorswatches')->isEnabled()) {
            return $this;
        }
        $image = null;
        $description = null;
        $shortDescription = null;
        $title = null;
        $additional = null;

        Mage::register('product', $product);

        if ($this->_config('title')) {
            $title = $_helper->productAttribute($product, $product->getName(), 'name');
        }

        if (!is_null($product->getData('image')) && $this->_config('image')
            && 'no_selection' != $product->getData('image')
        ) {
            $imageBlock = $this->getLayout()->createBlock('catalog/product_view_media');
            $imageBlock->setTemplate('catalog/product/view/media.phtml');
            $image = $imageBlock->toHtml();
        }
        if ($this->_config('additional')) {
            $additionalBlock = $this->getLayout()->createBlock('catalog/product_view_attributes');
            $additionalBlock->setTemplate('catalog/product/view/attributes.phtml');
            $additional = $additionalBlock->toHtml();
        }

        if (!is_null($product->getData('description')) && $this->_config('description')) {
            $descriptionBlock = $this->getLayout()->createBlock('catalog/product_view_description');
            $descriptionBlock->setTemplate('catalog/product/view/description.phtml');
            $description = $descriptionBlock->toHtml();
        }

        if (!is_null($product->getData('short_description')) && $this->_config('short_description')) {
            $shortDescription = $_helper->productAttribute(
                $product, nl2br($product->getShortDescription()), 'short_description'
            );
        }

        $dataArray = array(
            'id'               => $product->getId(),
            'title'            => $title,
            'fullDescritption' => $description,
            'shortDescription' => $shortDescription,
            'image'            => $image,
            'additional'       => $additional,
        );

        return $this->getResponse()->setBody(json_encode($dataArray));
    }

    public function galleryAction()
    {
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
        Mage::register('product', $product);
        $this->loadLayout();
        $this->renderLayout();
    }
}