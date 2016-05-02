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

class AW_Colorswatches_Model_Colorswatches extends Mage_Core_Model_Abstract
{

    public function addTabToAttributeEdit($event)
    {
        $block = $event->getBlock();
        if ($block->getId() == 'product_attribute_tabs' && !$block->getNoDispatch()) {
            if (!Mage::getSingleton('awcolorswatches/swatchattribute')->getSwatchattribute()) {
                return;
            }
            $block->setNoDispatch(true);
            Mage::unregister('attribute_type_hidden_fields');
            Mage::unregister('attribute_type_disabled_types');
            $content = Mage::app()->getLayout()->createBlock('awcolorswatches/adminhtml_swatch_edit_tab_swatch')
                ->toHtml()
            ;
            $block->addTab(
                'images',
                array(
                     'label'   => Mage::helper('awcolorswatches')->__('Images for Attribute'),
                     'title'   => Mage::helper('awcolorswatches')->__('Images for Attribute'),
                     'content' => $content,
                )
            )->toHtml();
        }
    }

    /**
     * @param $event
     * Catch saved attribute and save swatches
     */
    public function attributeSave($event)
    {
        $attribute = $event->getData('data_object');
        $swatchArray = $attribute->getAw();
        if (!is_array($swatchArray)) {
            return;
        }
        $swatchAttribute = Mage::getModel('awcolorswatches/swatchattribute')->getSwatchAttributeByAttribute($attribute);
        /**
         * save swatch attribute config
         */
        $swatchAttribute->setData('swatch_status', $swatchArray['swatch_status']);
        $swatchAttribute->setData('display_popup', $swatchArray['display_popup']);
        $swatchAttribute->setData('attribute_code', $attribute->getData('attribute_code'));
        $swatchAttribute->save();

        /**
         * save relations option = image
         */
        foreach ($swatchArray['swatch'] as $optionId => $swatch) {
            $swatchModel = Mage::getModel('awcolorswatches/swatch')->load($optionId, 'option_id');
            $swatchModel->setData('option_id', $optionId);

            if (isset($swatch['delete_image'])) {
                //delete functionality
                $swatchModel->deleteImage();
            } elseif (isset($swatch['file'])) {
                $swatchModel->setData('image', $swatch['file']);
            }
            $swatchModel->save();
        }
    }

    public function cleanCache($event)
    {
        $cacheDir = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'aw_colorswatches' . DS . 'resized';
        if (!file_exists($cacheDir)) {
            return;
        }
        $resizes = scandir($cacheDir);
        array_shift($resizes);
        array_shift($resizes);
        if (count($resizes) > 0) {
            foreach ($resizes as $subfolder) {
                $images = scandir($cacheDir . DS . $subfolder);
                array_shift($images);
                array_shift($images);
                if (count($images) > 0) {
                    foreach ($images as $image) {
                        @unlink($cacheDir . DS . $subfolder . DS . $image);
                    }
                }
            }
        }
    }

    public function processAttributesBlocks($event)
    {
        //for compatibility with AW_Mobile
        if (
            Mage::helper('core')->isModuleEnabled('AW_Mobile') &&
            !Mage::helper('awmobile')->getDisabledOutput() &&
            (
                Mage::getSingleton('customer/session')->getShowDesktop() === false ||
                Mage::helper('awmobile')->getTargetPlatform() == AW_Mobile_Model_Observer::TARGET_MOBILE
            )
        ) {
            return;
        }
        if (!Mage::helper('awcolorswatches')->isEnabled()) {
            return;
        }
        $block = $event->getData('block');
        $name = $block->getNameInLayout();
        $template = $block->getTemplate();
        $type = $block->getType();

        //Product options block
        if ($name == 'product.info.options.configurable') {
            if ($template == 'ajaxcartpro/options/configurable.phtml') {
                $block->setTemplate('aw_colorswatches/ajaxcartpro/options/configurable.phtml');
            } else {
                $block->setTemplate('aw_colorswatches/catalog/product/view/type/options/configurable.phtml');
            }
        }

        if (!Mage::getStoreConfig('awcolorswatches/global/layered', Mage::app()->getStore()->getId())) {
            return;
        }

        //Layered navigation blocks
        if ($type == 'catalog/layer_state') {
            if ($template == 'catalog/layer/state.phtml') {
                $block->setTemplate('aw_colorswatches/catalog/layer/state.phtml');
            }
        }

        if ($type == 'catalog/layer_view' || $type == 'catalogsearch/layer') {
            $filters = $block->getFilters();
            foreach ($filters as $filter) {
                $type = $filter->getData('type');
                if ($type == 'catalog/layer_filter_attribute' || $type == 'catalogsearch/layer_filter_attribute') {
                    $filter->setTemplate('aw_colorswatches/catalog/layer/filter.phtml');
                }
            }
        }

        if ($type == 'enterprise_search/catalog_layer_view' || $type == 'enterprise_search/catalogsearch_layer') {
            $filters = $block->getFilters();
            foreach ($filters as $filter) {
                $type = $filter->getData('type');
                if ($type == 'catalog/layer_filter_attribute' || $type == 'catalogsearch/layer_filter_attribute') {
                    $filter->setTemplate('aw_colorswatches/catalog/layer/filter.phtml');
                }
            }
        }

    }
}