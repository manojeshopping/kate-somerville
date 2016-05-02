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

class AW_Colorswatches_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DEFAULT_THUMBNAIL_WIDTH = 100;
    const DEFAULT_THUMBNAIL_HEIGHT = 100;

    public static function getThumbnailSize()
    {
        $width = Mage::getStoreConfig('awcolorswatches/global/width', Mage::app()->getStore()->getId());
        $height = Mage::getStoreConfig('awcolorswatches/global/height', Mage::app()->getStore()->getId());
        if (!(int)$width) {
            $width = self::DEFAULT_THUMBNAIL_WIDTH;
        }

        if (!(int)$height) {
            $height = self::DEFAULT_THUMBNAIL_HEIGHT;
        }

        return array('width' => $width, 'height' => $height);
    }

    public static function resizeImg($fileName, $width = null, $height = null)
    {
        $extname = 'aw_colorswatches';
        $imageURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "{$extname}/{$fileName}";
        $basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $extname . DS . $fileName;
        $sizes = AW_Colorswatches_Helper_Data::getThumbnailSize();
        if (is_null($width)) {
            $width = $sizes['width'];
        }
        if (is_null($height)) {
            $height = $sizes['height'];
        }
        $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $extname . DS . "resized"
            . DS . $width . "_" . $height . DS . $fileName
        ;

        //if image has already resized then just return URL
        if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
            $imageObj = new Varien_Image($basePath);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->backgroundColor(array(255, 255, 255));
            try {
                $imageObj->resize($width, $height);
                $imageObj->save($newPath);
            } catch (Exception $e) {
                return $imageURL;
            }
        }
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)
        . "{$extname}/resized/{$width}_{$height}/{$fileName}";
    }


    public function isEnabled()
    {
        return $this->isModuleOutputEnabled()
        && Mage::getStoreConfig(
            'awcolorswatches/global/enabled', Mage::app()->getStore()->getId()
        );
    }

    public function isSwatchEnabled($attribute)
    {
        $swatchAttribute = Mage::getModel('awcolorswatches/swatchattribute')->load(
            $attribute->getData('attribute_code'), 'attribute_code'
        );
        if ($swatchAttribute->getId() && $swatchAttribute->getData('swatch_status')) {
            return true && $this->isEnabled();
        }
        return false;
    }

    public function displayPopup($attribute)
    {
        $swatchAttribute = Mage::getModel('awcolorswatches/swatchattribute')->load(
            $attribute->getData('attribute_code'), 'attribute_code'
        );
        return (bool)$swatchAttribute->getData('display_popup');
    }

    public function getControllerUrl()
    {
        return Mage::getUrl('colorswatches');
    }
}