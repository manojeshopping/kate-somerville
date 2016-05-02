<?php

/**
 * Class Alliance_GlobalBanner_Adminhtml_GlobalbannersController
 */
class Alliance_GlobalBanner_Adminhtml_GlobalbannerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Handles adminhtml area route /adminhtml/globalbanner/index
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Handles adminhtml area route /adminhtml/globalbanner/new
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Handles adminhtml area route /adminhtml/globalbanner/edit
     */
    public function editAction()
    {
        $this->_initAction();

        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('alliance_globalbanner/banner');

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This global banner no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Global Banner'));

        $data = Mage::getSingleton('adminhtml/session')->getBannerData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('alliance_globalbanner', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Global Banner') : $this->__('New Global Banner'),
                $id ? $this->__('Edit Global Banner') : $this->__('New Global Banner'))
            ->_addContent($this->getLayout()->createBlock('alliance_globalbanner/adminhtml_banner_edit')
                ->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    /**
     * Handles adminhtml area route /adminhtml/globalbanner/save
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $stores = $this->getRequest()->getPost('stores');
            Mage::log(print_r($stores, true), null, 'globalbanners.log');
            if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                try {
                    $file_name_info = pathinfo($_FILES['image']['name']);
                    $file_extension = $file_name_info['extension'];
                    $filename_hash  = md5(str_shuffle($_FILES['image']['name'] . rand(1, 1000) . time()));
                    $final_filename = $filename_hash . '.' . $file_extension;

                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    $path = Mage::getBaseDir('media') . DS . 'alliance' . DS . 'globalbanner';

                    $uploader->save($path, $final_filename);

                    $postData['image'] = 'alliance' . DS . 'globalbanner' . DS . $final_filename;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')
                        ->addError($this->__('An error occurred while saving this banner.'));
                }
            } else {
                if (isset($postData['image']['delete']) && $postData['image']['delete'] == 1)
                    $postData['image'] = '';
                else
                    unset($postData['image']);
            }

            $model = Mage::getSingleton('alliance_globalbanner/banner');
            $model->setData($postData);

            $this->_prepareForSave($model);
            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The global banner has been saved.'));
                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('An error occurred while saving this global banner.'));
            }

            Mage::getSingleton('adminhtml/session')->setSliderData($postData);
            $this->_redirectReferer();
        }
    }

    /**
     * Handles adminhtml area route /adminhtml/globalbanner/delete
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('alliance_globalbanner/banner');
            $model->load($id);
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalog')->__('The global banner has been deleted.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('catalog')->__('An error occurred while deleting this global banner.'));
        $this->_redirect('*/*/');
    }

    /**
     * Handles adminhtml area route /adminhtml/globalbanner/message
     */
    public function messageAction()
    {
        $data = Mage::getModel('alliance_globalbanner/banner')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/alliance_globalbanner')
            ->_title($this->__('CMS'))->_title($this->__('Global Banners'))->_title($this->__('Manage Global Banners'))
            ->_addBreadcrumb($this->__('CMS'), $this->__('CMS'))
            ->_addBreadcrumb($this->__('Global Banners'), $this->__('Global Banners'));

        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/alliance_globalbanner');
    }

    protected function _prepareForSave($model)
    {
        $fields = array('stores', 'pages', 'logged_in_status');
        foreach ($fields as $field) {
            $val = $model->getData($field);
            $model->setData($field, '');
            if (is_array($val)) {
                $model->setData($field, implode(',', $val));
            }
        }

        return true;
    }
}