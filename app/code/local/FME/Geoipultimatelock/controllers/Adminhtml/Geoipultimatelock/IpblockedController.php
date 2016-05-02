<?php

class FME_Geoipultimatelock_Adminhtml_Geoipultimatelock_IpblockedController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {

        $this->loadLayout()
                ->_setActiveMenu('geoipultimatelock/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {

        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('geoipultimatelock/geoipblockedips')->load($id);

        if ($model->getId() || $id == 0) {

            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('geoipblockedips_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('geoipultimatelock/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('ACL Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('geoipultimatelock/adminhtml_ipblocked_edit'))
                    ->_addLeft($this->getLayout()->createBlock('geoipultimatelock/adminhtml_ipblocked_edit_tabs'));

            $this->renderLayout();
        } else {

            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {

        $this->_forward('edit');
    }

    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {
            
            $ipToBlock = explode(',',$data['blocked_ip']);
            $ipArr = array();
            foreach ($ipToBlock as $ip) {
                if (Mage::helper('geoipultimatelock')->validateIpFilter($ip)) {
                    $ipArr[] = $ip;
                }
            }

            try {
                foreach ($ipArr as $ip) {
                    $model = Mage::getModel('geoipultimatelock/geoipblockedips');
                    $remoteAddr = ip2long($ip);
                    
                    $model->setRemoteAddr($remoteAddr)
                            ->setBlockedIp($ip)
                            ->setStatus(2);
                    
                    $model->save();
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('geoipultimatelock')->__('IP(s) are successfully blocked.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {

                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__('Unable to process.'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('geoipultimatelock/geoipultimatelock');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        
        $geoipultimatelockIds = $this->getRequest()->getParam('geoipultimatelock');
        
        if (!is_array($geoipultimatelockIds)) {
            
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            
            try {
                
                foreach ($geoipultimatelockIds as $geoipultimatelockId) {
                    
                    $geoipultimatelock = Mage::getModel('geoipultimatelock/geoipultimatelock')->load($geoipultimatelockId);
                    $geoipultimatelock->delete();
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($geoipultimatelockIds)
                        )
                );
            } catch (Exception $e) {
                
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {

        $geoipultimatelockIds = $this->getRequest()->getParam('geoipultimatelock');
        if (!is_array($geoipultimatelockIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($geoipultimatelockIds as $geoipultimatelockId) {
                    $geoipultimatelock = Mage::getSingleton('geoipultimatelock/geoipultimatelock')
                            ->load($geoipultimatelockId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($geoipultimatelockIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'geoipultimatelock.csv';
        $content = $this->getLayout()->createBlock('geoipultimatelock/adminhtml_geoipultimatelock_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'geoipultimatelock.xml';
        $content = $this->getLayout()->createBlock('geoipultimatelock/adminhtml_geoipultimatelock_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    public function blockIpAction() {//echo 'here';exit;
        $visitorData = Mage::getModel('log/visitor_online')
                ->load($this->getRequest()->getParam('id')); // echo '<pre>';print_r($visitorData->getData());echo '</pre>';//exit;
        $remoteAdd = $visitorData->getRemoteAddr();
        $ip = long2ip($remoteAdd); // getting and ip address

        $model = Mage::getModel('geoipultimatelock/geoipblockedips');

        try {
            $model->setBlockedIp($ip)
                    ->setVisitorId($visitorData->getVisitorId())
                    ->setCustomerId($visitorData->getCustomerId())
                    ->setRemoteAddr($remoteAdd)
                    ->setType($visitorData->getVisitorType())
                    ->setStatus(2);

            $model->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('geoipultimatelock')->__('IP Blocked!'));
            $this->_redirect('*/*/onlineIp');
            return;
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__('Error: ' . $ex->getMessage()));
            $this->_redirect('*/*/onlineIp');
            return;
        }
    }

    public function unblockIpAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('geoipultimatelock/geoipblockedips');
        $_status = $model->load($id)->getStatus(); //echo $_status;exit;

        if ($_status == 2) {
            $_status = 1;
        } else if ($_status == 1) {
            $_status = 2;
        }

        $model->setId($id);

        try {
            $model->setStatus($_status);

            $model->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('geoipultimatelock')->__("IP status changed."));
            $this->_redirect('*/*/');
            return;
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__('Error: ' . $ex->getMessage()));
            $this->_redirect('*/*/');
            return;
        }
    }

    public function allIpStatusDeleteAction() {

        $params = $this->getRequest()->getParam('geoipultimatelock');
        if (!is_array($params)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($params as $id) {
                    $geoipultimatelock = Mage::getModel('geoipultimatelock/geoipblockedips')->load($id);
                    $geoipultimatelock->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($params)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function massBlockStatusAction() {
        $params = $this->getRequest()->getParam('geoipultimatelock'); //echo '<pre>';print_r($params);echo '</pre>';exit;

        if (!is_array($params)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($params as $id) {
                    $geoipultimatelock = Mage::getSingleton('geoipultimatelock/geoipblockedips')
                            ->load($id)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($params))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }

}