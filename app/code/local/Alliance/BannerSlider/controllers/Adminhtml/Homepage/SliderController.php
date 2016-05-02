<?php

/**
 * Class Alliance_BannerSlider_Adminhtml_Homepage_SliderController
 */
class Alliance_BannerSlider_Adminhtml_Homepage_SliderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Handles adminhtml area route /adminhtml/homepage_slider/index
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Handles adminhtml area route /adminhtml/homepage_slider/new
     */
    public function newAction()
    {
        $this->_forward('edit');
    }


    /**
     * Handles adminhtml area route /adminhtml/homepage_slider/edit
     */
    public function editAction()
    {
        $this->_initAction();

        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('alliance_bannerslider/banner');

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This banner no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Banner'));

        $data = Mage::getSingleton('adminhtml/session')->getSliderData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('alliance_bannerslider', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Banner') : $this->__('New Banner'),
                $id ? $this->__('Edit Banner') : $this->__('New Banner'))
            ->_addContent($this->getLayout()->createBlock('alliance_bannerslider/adminhtml_banner_edit')
                ->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    /**
     * Handles adminhtml area route /adminhtml/homepage_slider/save
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                try {
                    $file_name_info = pathinfo($_FILES['image']['name']);
                    $file_extension = $file_name_info['extension'];
                    $filename_hash = md5(str_shuffle($_FILES['image']['name'].rand(1,1000).time()));
                    $final_filename = $filename_hash . '.' . $file_extension;

                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    $path = Mage::getBaseDir('media') . DS . 'alliance' . DS . 'bannerslider';

                    $uploader->save($path, $final_filename);

                    $postData['image'] = 'alliance' . DS .'bannerslider' . DS . $final_filename;
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')
                        ->addError($this->__('An error occurred while saving this banner.'));
                }
            }
            else {
                if(isset($postData['image']['delete']) && $postData['image']['delete'] == 1)
                    $postData['image'] = '';
                else
                    unset($postData['image']);
            }

            $model = Mage::getSingleton('alliance_bannerslider/banner');
            $model->setData($postData);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The banner has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($this->__('An error occurred while saving this banner.'));
            }

            Mage::getSingleton('adminhtml/session')->setSliderData($postData);
            $this->_redirectReferer();
        }
    }

    /**
     * Handles adminhtml area route /adminhtml/homepage_slider/delete
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('alliance_bannerslider/banner');
            $model->load($id);
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalog')->__('The banner has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('catalog')->__('An error occurred while deleting this banner.'));
        $this->_redirect('*/*/');
    }

    /**
     * Handles adminhtml area route /adminhtml/homepage_slider/message
     */
    public function messageAction()
    {
        $data = Mage::getModel('alliance_bannerslider/banner')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/alliance_bannerslider')
            ->_title($this->__('CMS'))->_title($this->__('Banner Sliders'))->_title($this->__('Manage Homepage Slider'))
            ->_addBreadcrumb($this->__('CMS'), $this->__('CMS'))
            ->_addBreadcrumb($this->__('Banner Sliders'), $this->__('Banner Sliders'));

        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/alliance_bannerslider');
    }
}