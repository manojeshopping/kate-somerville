<?php
class Alliance_KateReviews_Adminhtml_KatereviewsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
             ->renderLayout();
    }

    public function reportAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function exportCsvAction() {
        $fileName = 'reviewsreport.csv';
        $content = $this->getLayout()
            ->createBlock('alliance_katereviews/adminhtml_report_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function pendingAction()
    {
        $this->_initPendingAction()
             ->renderLayout();
    }

    protected function dispatchEmail($email_template, $sender_email_identity, $email_recipient, $data)
    {
        $helper = Mage::helper('alliance_katereviews');
        if ($helper->getConfigEnableCustomerNotification()) {
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(FALSE);

            try {
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->sendTransactional(
                        $email_template,
                        $sender_email_identity,
                        $email_recipient, null, array('data' => $data)
                    );
                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
                $translate->setTranslateInline(TRUE);
                return TRUE;
            }
            catch (Exception $e) {
                $translate->setTranslateInline(TRUE);
                $this->_redirect('*/*/');
                return;
            }
        }
        return FALSE;
    }

    public function editAction()
    {
        $this->_initAction();

        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('alliance_katereviews/review');

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This review no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Review'));

        $data = Mage::getSingleton('adminhtml/session')->getReviewData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('alliance_katereviews', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Review') : $this->__('New Review'), $id ? $this->__('Edit Review') : $this->__('New Review'))
            ->_addContent($this->getLayout()->createBlock('alliance_katereviews/adminhtml_pending_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('alliance_katereviews/review');
            $customer = Mage::getModel('customer/customer');
            $product = Mage::getModel('catalog/product');
            $model->setData($postData);
			$previously_pending = Mage::getModel('alliance_katereviews/review')->load($model->getId())->getStatus() == 'Pending';

            try {
                if ($model->save()) {
                    $review = Mage::getModel('alliance_katereviews/review')->load($model->getId());
                    $helper = Mage::helper('alliance_katereviews');
                    $customer->load($review->getCustomerId());
                    $product->load($review->getProductId());
                    $review_status = $review->getStatus();
                    $event_data = array(
                        'customer' => $customer,
						'product'  => $product,
                    );

                    // dispatch event for review approval, providing customer email and customer ID
                    if ($review_status == 'Approved' && $previously_pending && !$review->getContributed()) {
                        Mage::dispatchEvent('alliance_katereviews_approval', $event_data);
                    }

                    if ($review_status == 'Approved' && !$review->getContributed()) {
                        $contributor = Mage::getModel('alliance_katereviews/contributor');
                        $contributor->loadByCustomerId($review->getCustomerId());
                        if ($contributor->getId()) {
                            $contributor->setReviewsCount($contributor->getReviewsCount() + 1);
                            $contributor->save();
                        }
                        else {
                            $new_contributor = Mage::getModel('alliance_katereviews/contributor');
                            $new_contributor->setReviewsCount(1);
                            $new_contributor->setCustomerId($review->getCustomerId());
                            $new_contributor->save();
                        }
                        $review->setContributed(TRUE);
                        $review->save();
                        $helper->updateTopContributors();
                    }

                    if ($review_status == 'Approved' && $review->getNotify() == 'Yes'
                        && !$review->getNotified() && $helper->getConfigEnableCustomerNotification()) {
                        $email_template = $helper->getConfigEmailTemplate();
                        $sender_email_identity = $helper->getConfigSenderEmailIdentity();
                        $email_recipient = $customer->getEmail();

                        $template_array = array();
                        $template_array['customer_firstname'] = $customer->getFirstname();
                        $template_array['product_name'] = $product->getName();
                        $template_array['product_link'] = $product->getProductUrl();
                        $data = new Varien_Object();
                        $data->setData($template_array);
                        if ($this->dispatchEmail($email_template, $sender_email_identity, $email_recipient, $data)) {
                            $review->setNotified(1);
                            $review->save();
                        }
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The review has been saved.'));

                $this->_redirectUrl(Mage::helper('core')->urlDecode(Mage::getSingleton('core/session')->getKatereviewsReferer()));

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this review.'));
            }

            Mage::getSingleton('adminhtml/session')->setReviewData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('alliance_katereviews/review');
            $model->load($id);
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalog')->__('The review has been deleted.'));
				$this->_redirectUrl(Mage::helper('core')->urlDecode(Mage::getSingleton('core/session')->getKatereviewsReferer()));
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('alliance_katereviews')->__('An error occurred while deleting this review.'));
        $this->_redirect('*/*/');
    }

    public function approveAction()
    {
        if ($review_id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('alliance_katereviews/review');
            $customer = Mage::getModel('customer/customer');
            $product = Mage::getModel('catalog/product');
            $helper = Mage::helper('alliance_katereviews');
            try {
                $model->load($review_id);
                if ($model->getStatus() != 'Approved') {
					$previously_pending = $model->getStatus() == 'Pending';
                    $model->setStatus('Approved');
                    if ($model->save()) {
                        $customer->load($model->getCustomerId());
                        $product->load($model->getProductId());
                        $review_status = $model->getStatus();
                        $event_data = array(
                            'customer' => $customer,
							'product'  => $product,
                        );

                        // dispatch event for review approval, providing customer email and customer ID
                        if ($review_status == 'Approved' && $previously_pending && !$model->getContributed()) {
                            Mage::dispatchEvent('alliance_katereviews_approval', $event_data);
                        }

                        if ($review_status == 'Approved' && !$model->getContributed()) {
                            $contributor = Mage::getModel('alliance_katereviews/contributor');
                            $contributor->loadByCustomerId($model->getCustomerId());
                            if ($contributor->getId()) {
                                $contributor->setReviewsCount($contributor->getReviewsCount() + 1);
                                $contributor->save();
                            } else {
                                $new_contributor = Mage::getModel('alliance_katereviews/contributor');
                                $new_contributor->setReviewsCount(1);
                                $new_contributor->setCustomerId($model->getCustomerId());
                                $new_contributor->save();
                            }
                            $model->setContributed(TRUE);
                            $model->save();
                        }

                        if ($review_status == 'Approved' && $model->getNotify() == 'Yes'
                            && !$model->getNotified() && $helper->getConfigEnableCustomerNotification()
                        ) {
                            $email_template = $helper->getConfigEmailTemplate();
                            $sender_email_identity = $helper->getConfigSenderEmailIdentity();
                            $email_recipient = $customer->getEmail();

                            $template_array = array();
                            $template_array['customer_firstname'] = $customer->getFirstname();
                            $template_array['product_name'] = $product->getName();
                            $template_array['product_link'] = $product->getProductUrl();
                            $data = new Varien_Object();
                            $data->setData($template_array);
                            if ($this->dispatchEmail($email_template, $sender_email_identity, $email_recipient, $data)) {
                                $model->setNotified(1);
                                $model->save();
                            }
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session')
                            ->addError(Mage::helper('alliance_katereviews')->__('An error occurred while trying to approve the review(s).'));
                        $this->_redirect('*/*/pending');
                    }
                }
                $helper->updateTopContributors();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully approved the review(s).'));
                $this->_redirect('*/*/pending');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/pending');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to approve the review(s).'));
            $this->_redirect('*/*/');
        }
    }

    public function denyAction()
    {
        if ($review_id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('alliance_katereviews/review');
            try {
                $model->load($review_id);
                if ($model->getStatus() != 'Denied') {
                    $model->setStatus('Denied');
                    $model->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully denied the review(s).'));
                $this->_redirect('*/*/pending');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/pending');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to deny the review(s).'));
            $this->_redirect('*/*/pending');
        }
    }

    public function massDeleteAction()
    {
        if ($review_ids = $this->getRequest()->getParam('review_ids')) {
            try {
                $model = Mage::getModel('alliance_katereviews/review');
                foreach ($review_ids as $id) {
                    if ($model->load($id)) {
                        $model->delete();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully deleted the review(s).'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to delete the review(s).'));
            $this->_redirect('*/*/');
        }
    }

    public function massDenyAction()
    {
        if ($review_ids = $this->getRequest()->getParam('review_ids')) {
            $model = Mage::getModel('alliance_katereviews/review');
            try {
                foreach ($review_ids as $id) {
                    $model->load($id);
                    if ($model->getStatus() != 'Denied') {
                        $model->setStatus('Denied');
                        $model->save();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully denied the review(s).'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to deny the review(s).'));
            $this->_redirect('*/*/');
        }
    }

    public function massApproveAction()
    {
        if ($review_ids = $this->getRequest()->getParam('review_ids')) {
            $model = Mage::getModel('alliance_katereviews/review');
            $customer = Mage::getModel('customer/customer');
            $product = Mage::getModel('catalog/product');
            $helper = Mage::helper('alliance_katereviews');
            try {
                foreach ($review_ids as $id) {
                    $model->load($id);
                    if ($model->getStatus() != 'Approved') {
						$previously_pending = $model->getStatus() == 'Pending';
                        $model->setStatus('Approved');
                        if ($model->save()) {
                            $customer->load($model->getCustomerId());
                            $product->load($model->getProductId());
                            $review_status = $model->getStatus();
                            $event_data = array(
                                'customer' => $customer,
								'product'  => $product,
                            );

                            // dispatch event for review approval, providing customer email and customer ID
                            if ($review_status == 'Approved' && $previously_pending && !$model->getContributed()) {
                                Mage::dispatchEvent('alliance_katereviews_approval', $event_data);
                            }

                            if ($model->getStatus() == 'Approved' && !$model->getContributed()) {
                                $contributor = Mage::getModel('alliance_katereviews/contributor');
                                $contributor->loadByCustomerId($model->getCustomerId());
                                if ($contributor->getId()) {
                                    $contributor->setReviewsCount($contributor->getReviewsCount() + 1);
                                    $contributor->save();
                                }
                                else {
                                    $new_contributor = Mage::getModel('alliance_katereviews/contributor');
                                    $new_contributor->setReviewsCount(1);
                                    $new_contributor->setCustomerId($model->getCustomerId());
                                    $new_contributor->save();
                                }
                                $model->setContributed(TRUE);
                                $model->save();
                            }

                            if ($model->getStatus() == 'Approved' && $model->getNotify() == 'Yes'
                                && !$model->getNotified() && $helper->getConfigEnableCustomerNotification()) {
                                $email_template = $helper->getConfigEmailTemplate();
                                $sender_email_identity = $helper->getConfigSenderEmailIdentity();
                                $email_recipient = $customer->getEmail();

                                $template_array = array();
                                $template_array['customer_firstname'] = $customer->getFirstname();
                                $template_array['product_name'] = $product->getName();
                                $template_array['product_link'] = $product->getProductUrl();
                                $data = new Varien_Object();
                                $data->setData($template_array);
                                if ($this->dispatchEmail($email_template, $sender_email_identity, $email_recipient, $data)) {
                                    $model->setNotified(1);
                                    $model->save();
                                }
                            }
                        }
                        else {
                            Mage::getSingleton('adminhtml/session')
                                ->addError(Mage::helper('alliance_katereviews')->__('An error occurred while trying to approve the review(s).'));
                            $this->_redirect('*/*/');
                        }
                    }
                }
                $helper->updateTopContributors();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully approved the review(s).'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to approve the review(s).'));
            $this->_redirect('*/*/');
        }
    }



    public function massPendingDeleteAction()
    {
        if ($review_ids = $this->getRequest()->getParam('review_ids')) {
            try {
                $model = Mage::getModel('alliance_katereviews/review');
                foreach ($review_ids as $id) {
                    if ($model->load($id)) {
                        $model->delete();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully deleted the review(s).'));
                $this->_redirect('*/*/pending');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/pending');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to delete the review(s).'));
            $this->_redirect('*/*/pending');
        }
    }

    public function massPendingDenyAction()
    {
        if ($review_ids = $this->getRequest()->getParam('review_ids')) {
            $model = Mage::getModel('alliance_katereviews/review');
            try {
                foreach ($review_ids as $id) {
                    $model->load($id);
                    if ($model->getStatus() != 'Denied') {
                        $model->setStatus('Denied');
                        $model->save();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully denied the review(s).'));
                $this->_redirect('*/*/pending');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/pending');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to deny the review(s).'));
            $this->_redirect('*/*/pending');
        }
    }

    public function massPendingApproveAction()
    {
        if ($review_ids = $this->getRequest()->getParam('review_ids')) {
            $model = Mage::getModel('alliance_katereviews/review');
            $customer = Mage::getModel('customer/customer');
            $product = Mage::getModel('catalog/product');
            try {
                foreach ($review_ids as $id) {
                    $model->load($id);
                    if ($model->getStatus() != 'Approved') {
						$previously_pending = $model->getStatus() == 'Pending';
                        $model->setStatus('Approved');
                        if ($model->save()) {
                            $helper = Mage::helper('alliance_katereviews');
                            $customer->load($model->getCustomerId());
                            $product->load($model->getProductId());
                            $review_status = $model->getStatus();
                            $event_data = array(
                                'customer' => $customer,
								'product'  => $product,
                            );

                            // dispatch event for review approval, providing customer email and customer ID
                            if ($review_status == 'Approved' && $previously_pending && !$model->getContributed()) {
                                Mage::dispatchEvent('alliance_katereviews_approval', $event_data);
                            }

                            if ($model->getStatus() == 'Approved' && !$model->getContributed()) {
                                $contributor = Mage::getModel('alliance_katereviews/contributor');
                                $contributor->loadByCustomerId($model->getCustomerId());
                                if ($contributor->getId()) {
                                    $contributor->setReviewsCount($contributor->getReviewsCount() + 1);
                                    $contributor->save();
                                }
                                else {
                                    $new_contributor = Mage::getModel('alliance_katereviews/contributor');
                                    $new_contributor->setReviewsCount(1);
                                    $new_contributor->setCustomerId($model->getCustomerId());
                                    $new_contributor->save();
                                }
                                $model->setContributed(TRUE);
                                $model->save();
                            }

                            if ($model->getStatus() == 'Approved' && $model->getNotify() == 'Yes'
                                && !$model->getNotified() && $helper->getConfigEnableCustomerNotification()) {
                                $email_template = $helper->getConfigEmailTemplate();
                                $sender_email_identity = $helper->getConfigSenderEmailIdentity();
                                $email_recipient = $customer->getEmail();

                                $template_array = array();
                                $template_array['customer_firstname'] = $customer->getFirstname();
                                $template_array['product_name'] = $product->getName();
                                $template_array['product_link'] = $product->getProductUrl();
                                $data = new Varien_Object();
                                $data->setData($template_array);
                                if ($this->dispatchEmail($email_template, $sender_email_identity, $email_recipient, $data)) {
                                    $model->setNotified(1);
                                    $model->save();
                                }
                            }
                        }
                        else {
                            Mage::getSingleton('adminhtml/session')
                                ->addError(Mage::helper('alliance_katereviews')->__('An error occurred while trying to approve the review(s).'));
                            $this->_redirect('*/*/');
                        }
                    }

                }
                $helper->updateTopContributors();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('alliance_katereviews')->__('You have successfully approved the review(s).'));
                $this->_redirect('*/*/pending');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/pending');
                return;
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('alliance_katereviews')->__('An error occurred while trying to approve the review(s).'));
            $this->_redirect('*/*/pending');
        }
    }

    public function messageAction()
    {
        $data = Mage::getModel('alliance_katereviews/review')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/alliance_katereviews')
            ->_title($this->__('Catalog'))
            ->_title($this->__('Product Reviews'))
            ->_title($this->__('All Product Reviews'))
            ->_addBreadcrumb($this->__('Catalog'), $this->__('Catalog'))
            ->_addBreadcrumb($this->__('Product Reviews'), $this->__('Product Reviews'));

        return $this;
    }

    protected function _initPendingAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/alliance_katereviews')
            ->_title($this->__('Catalog'))
            ->_title($this->__('Product Reviews'))
            ->_title($this->__('Pending Product Reviews'))
            ->_addBreadcrumb($this->__('Catalog'), $this->__('Catalog'))
            ->_addBreadcrumb($this->__('Product Reviews'), $this->__('Product Reviews'));

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/alliance_katereviews');
    }
}