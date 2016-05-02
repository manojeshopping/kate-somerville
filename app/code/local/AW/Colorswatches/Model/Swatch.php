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

class AW_Colorswatches_Model_Swatch extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('awcolorswatches/swatch', 'swatch_id');
    }

    public function getSwatchByOption($option)
    {
        return $this->getCollection()
            ->addFieldToFilter('option_id', array('eq' => $option))->getFirstItem();
    }

    public function getOptionsByAttribute($attribute)
    {
        $options = $attribute->getSource()->getAllOptions();
        $optionIds = array();
        foreach ($options as $option) {
            if (!$option['value']) {
                continue;
            }
            $optionIds[] = $option['value'];
        }

        return $this->getCollection()
            ->addFieldToFilter('option_id', array('in' => $optionIds));
    }

    public function getImageUrl()
    {
        if ($this->getData('image')) {
            return $this->getData('image');
        }
        return Mage::getDesign()->getSkinUrl('images/no_image.jpg');
    }

    public function getFullImageUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'aw_colorswatches' . DS . $this->getImageUrl();
    }

    public function getThumbnail()
    {
        if ($this->getData('image')) {
            return AW_Colorswatches_Helper_Data::resizeImg($this->getImageUrl());
        }
        return '';
    }

    public function getLayeredImage()
    {
        if ($this->getData('image')) {
            return AW_Colorswatches_Helper_Data::resizeImg($this->getImageUrl(), 16, 16);
        }
        return '';
    }

    public function deleteImage()
    {
        @unlink(
            Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'aw_colorswatches' . DS . $this->getData(
                'image'
            )
        );
        $resizes = array();
        //remove resized images
        if (file_exists(
            Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'aw_colorswatches' . DS . 'resized'
        )
        ) {
            $resizes = scandir(
                Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'aw_colorswatches' . DS . 'resized'
            );
        }

        if (!empty($resizes) && count($resizes) > 2) {
            array_shift($resizes);
            array_shift($resizes);
            foreach ($resizes as $subfolder) {
                @unlink(
                    Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'aw_colorswatches' . DS . 'resized'
                    . DS . $subfolder . DS . $this->getData('image')
                );
            }
        }
        $this->setData('image', '');
    }

}