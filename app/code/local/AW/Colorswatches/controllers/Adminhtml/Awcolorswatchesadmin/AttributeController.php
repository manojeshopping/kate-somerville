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


class AW_Colorswatches_Adminhtml_Awcolorswatchesadmin_AttributeController extends Mage_Adminhtml_Controller_Action
{
    public function uploadAction()
    {
        $result = array();
        if (!empty($_FILES)) {
            try {
                $field = $this->getRequest()->getParam('field');
                $uploader = new Varien_File_Uploader($field);
                $uploader->setAllowRenameFiles(true);

                $uploader->setFilesDispersion(false);
                $uploader->setAllowCreateFolders(true);

                $path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'aw_colorswatches' . DS;

                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
                $uploadSaveResult = $uploader->save($path, $_FILES[$field]['name']);

                $result['file_name'] = $uploadSaveResult['file'];
            } catch (Exception $e) {
                $result = array(
                    "error"      => $e->getMessage(),
                    "error_code" => $e->getCode(),
                    "status"     => "error",
                );
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}