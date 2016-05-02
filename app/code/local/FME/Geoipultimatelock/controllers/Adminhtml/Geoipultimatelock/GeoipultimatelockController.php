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
require_once('AbstractController.php');

class FME_Geoipultimatelock_Adminhtml_Geoipultimatelock_GeoipultimatelockController extends FME_Geoipultimatelock_Adminhtml_Geoipultimatelock_AbstractController {


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
        $model = Mage::getModel('geoipultimatelock/geoipultimatelock')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('geoipultimatelock_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('geoipultimatelock/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('ACL Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));


            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setCanLoadRulesJs(true);
            $this->_addContent($this->getLayout()->createBlock('geoipultimatelock/adminhtml_geoipultimatelock_edit'))
                    ->_addLeft($this->getLayout()->createBlock('geoipultimatelock/adminhtml_geoipultimatelock_edit_tabs'));

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
//echo '<pre>';print_r($data);echo '</pre>';exit;
            if (count($data['stores']) > 0) {
                $data['stores'] = implode(',', $data['stores']);
            } else {
                $data['stores'] = null;
            }

            if (count($data['cms_pages']) > 0) {
                $data['cms_pages'] = implode(',', $data['cms_pages']);
            } else {
                $data['cms_pages'] = null;
            }

            $model = Mage::getModel('geoipultimatelock/geoipultimatelock');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {

                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {

                    $model->setUpdateTime(now());
                }

                $request = $this->getRequest();
                $rule = $request->getParam('rule');
                $cond = array();
                $rule['css'] = Mage::helper('geoipultimatelock')->updateChild($rule['css'], 'catalogrule/rule_condition_combine', 'geoipultimatelock/rule_condition_combine');
                $conditions = Mage::helper('geoipultimatelock')->convertFlatToRecursive($rule, array('css'));


                if (is_array($conditions) && isset($conditions['css']) && isset($conditions['css']['css_conditions_fieldset'])) {

                    $conditionPart = $conditions['css']['css_conditions_fieldset'];
                    if (isset($conditionPart['css']) && count($conditionPart['css']) > 0) {
                        $cond['rules']['conditions'] = $conditions['css']['css_conditions_fieldset'];
                    } else {
                        $cond['rules'] = '';
                    }

                    //$cond['rules']['conditions'] = ($conditions['css']['css_conditions_fieldset']); // for storing default rule
                } else {

                    $cond['rules']['conditions'] = array();
                }

                $model->setRules($cond['rules']);

                if (isset($data['blocked_countries']) && count($data['blocked_countries']) > 0) {
                    $model->setBlockedCountries(serialize($data['blocked_countries']));
                } else {
                    $model->setBlockedCountries('');
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('geoipultimatelock')->__('ACL was successfully saved'));
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

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__('Unable to find ACL to save'));
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

    public function onlineIpAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('geoipultimatelock/adminhtml_onlineip_grid', 'adminhtml_geoipultimatelock.grid'));
        $this->renderLayout();
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

    public function massBlockOnlineAction() {

        $params = $this->getRequest()->getParam('geoipultimatelock'); //echo '<pre>';print_r($params);echo '</pre>';exit;
        $visitorData = Mage::getModel('log/visitor_online')
                ->load($params);  //echo '<pre>';print_r($visitorData->getData());echo '</pre>';exit;
        $remoteAdd = $visitorData->getRemoteAddr();
        $ip = long2ip($remoteAdd); // getting and ip address

        if (!is_array($params)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($params as $id) {
                    $geoipultimatelock = Mage::getSingleton('geoipultimatelock/geoipblockedips')
                            ->setBlockedIp($ip)
                            ->setVisitorId($visitorData->getVisitorId())
                            ->setCustomerId($visitorData->getCustomerId())
                            ->setRemoteAddr($remoteAdd)
                            ->setType($visitorData->getVisitorType())
                            ->setStatus(2)
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

        $this->_redirect('*/*/onlineIp');
    }

    public function ipBlockedAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('geoipultimatelock/adminhtml_ipblocked_grid', 'adminhtml_geoipultimatelock.grid'));
        $this->renderLayout();
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
            $this->_redirect('*/*/ipBlocked');
            return;
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__('Error: ' . $ex->getMessage()));
            $this->_redirect('*/*/ipBlocked');
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
        $this->_redirect('*/*/ipBlocked');
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

        $this->_redirect('*/*/ipBlocked');
    }

    public function importTablesAction() {

        $this->loadLayout()
                ->renderLayout();
    }

    public function updateTablesAction() {

        try {
            $records = $this->_beginImport(); //echo '<pre>';print_r($data);echo '</pre>';exit;
            if ($records) {
                Mage::getSingleton('adminhtml/session')->addSuccess('Table imported successfully! ' . $records . ' records imported!');
            }
        } catch (Mage_Core_Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        } catch (Exception $exx) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__('Invalid File type!' . ' (' . $exx->getMessage() . ')'));
        }

        $this->_redirect('*/*/importTables');
    }

    protected function _beginImport() {

        $file = Mage::getBaseDir('media') . DS . 'geoipultimatelock' . DS . 'GeoIPCountryWhois.csv';
        $csvObj = new Varien_File_Csv();
        $data = $csvObj->getData($file);
        /* all csv data */
        if (!empty($data)) {

            $installer = new Mage_Core_Model_Resource_Setup();
            $installer->startSetup();

            $installer->run("
                DROP TABLE IF EXISTS {$installer->getTable('geoip_cl')};
                    CREATE TABLE {$installer->getTable('geoip_cl')} (
                        ci tinyint(3) unsigned NOT NULL auto_increment,
                        cc char(2) NOT NULL,
                        cn varchar(50) NOT NULL,
                        PRIMARY KEY (ci)
                    ) AUTO_INCREMENT=1 ;
                    

                    DROP TABLE IF EXISTS {$installer->getTable('geoip_csv')};
                    CREATE TABLE {$installer->getTable('geoip_csv')} (
                        start_ip char(15)NOT NULL,
                        end_ip char(15)NOT NULL,
                        start int(10) unsigned NOT NULL,
                        end int(10) unsigned NOT NULL,
                        cc char(2) NOT NULL,
                        cn varchar(50) NOT NULL
                    );
                    
                    DROP TABLE IF EXISTS {$installer->getTable('geoip_ip')};
                    CREATE TABLE {$installer->getTable('geoip_ip')} (
                        start int(10) unsigned NOT NULL,
                        end int(10) unsigned NOT NULL,
                        ci tinyint(3) unsigned NOT NULL
                    );
            ");
            $installer->endSetup();
            set_time_limit(72000);
            $i = 0;
            $resource = Mage::getSingleton('core/resource');
            $write = $resource->getConnection('core_write');
            $query = '';
            foreach ($data as $_ix) {

                try {

                    $countryname = Mage::getSingleton('core/resource')->getConnection('default_write')->quote($_ix[5]);
                    $query = "INSERT INTO " . $resource->getTableName('geoip_csv') . " 
                         (start_ip, end_ip, start, end, cc, cn) 
                         VALUES ('{$_ix[0]}', '{$_ix[1]}', '{$_ix[2]}', '{$_ix[3]}', '{$_ix[4]}', $countryname);";
                    $write->query($query);

                    $i++;
                } catch (Exception $ex) {

                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__($ex->getMessage() . ' Query: ' . $query));
                    $this->_redirect('*/*/importTables');
                    return;
                }
            }

            if ($i > 0) {

                try {

                    $installer->startSetup();

                    $installer->run("
                        INSERT INTO " . $resource->getTableName('geoip_cl') . " 
                            SELECT DISTINCT NULL, cc, cn FROM " . $resource->getTableName('geoip_csv') . ";

                        INSERT INTO " . $resource->getTableName('geoip_ip') . " 
                            SELECT start, end, ci FROM " . $resource->getTableName('geoip_csv') . " NATURAL JOIN " . $resource->getTableName('geoip_cl') . ";
                    ");

                    $installer->endSetup();
                } catch (Exception $e) {

                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('geoipultimatelock')->__($e->getMessage()));
                }
            }
        }

        return $i;
    }

    public function saveBlockedIps() {

        $this->loadLayout();
        $this->_setActiveMenu('geoipultimatelock/items');

//        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('ACL Manager'));
//        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
        //$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setCanLoadRulesJs(true);
        $this->_addContent($this->getLayout()->createBlock('geoipultimatelock/adminhtml_ipblocked_edit'))
                ->_addLeft($this->getLayout()->createBlock('geoipultimatelock/adminhtml_ipblocked_edit_tabs'));

        $this->renderLayout();
    }

}
