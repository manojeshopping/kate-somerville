<?php
require_once('lib/recaptcha/recaptchalib.php');

/**
 * Class Alliance_Giveawayfb_CustomerController
 */
class Alliance_Giveawayfb_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sorryAction()
    {
        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }

    public function creationAction()
    {
        // Check if there is an email loaded.
        $email = Mage::helper('giveawayfb')->getSessionData('email');
        if (empty($email)) {
            $url = Mage::helper('giveawayfb')->getModuleURL();
            $this->_redirectUrl($url);
            return;
        }

        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }

    public function creationsamplesAction()
    {
        // Check if there is an id loaded.
        $id = Mage::helper('giveawayfb')->getSessionData('id');
        if (empty($id)) {
            $url = Mage::helper('giveawayfb')->getModuleURL();
            $this->_redirectUrl($url);
            return;
        }

        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }

    public function creationaddressAction()
    {
        // Load helper.
        $helper = Mage::helper('giveawayfb');

        // Check if there is an id an sample loaded.
        $id = $helper->getSessionData('id');
        $samplekit = $helper->getSessionData('samplekit');
        if (empty($id) || empty($samplekit)) {
            $url = $helper->getModuleURL();
            $this->_redirectUrl($url);
            return;
        }

        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }

    public function successAction()
    {
        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }

    public function resendAction()
    {
        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }

    public function resendsuccessAction()
    {
        // just print layout.
        $this->loadLayout();
        $this->renderLayout();
    }


    public function emailverifactionAction()
    {
        $helper = Mage::helper('giveawayfb');
        $model = Mage::getModel('giveawayfb/giveawayfb');

        // Check link validation.
        $confirmid = $this->getRequest()->getParam('id');
        $customer = $model->checkConfirmid($confirmid);

        if ($customer) {
            // Generate new temp password.
            $newPassword = $helper->generateNewPassword();

            // Create Magento Account.
            $magentoCustomerId = $model->createMangentoCustomer($customer, $newPassword);
            if ($magentoCustomerId) {

                // Set customer_id and time.
                $updated = $model->updateData($customer['giveawayfb_id'], array(
                    'confirm_creation' => date('Y-m-d H:i:s'),
                    'customer_id' => $magentoCustomerId,
                    'customer_password' => $newPassword,
                ));
                if (!$updated) {
                    Mage::getSingleton('core/session')->addError($this->__("Error when inserting the order has occurred. Please contact support."));
                    session_write_close();
                    return;
                }

                /*
                *** Now, the creation order is processed by a script. ***

                // Create new Order.
                $magentoOrderId = $model->createNewOrder($magentoCustomerId, $customer['samplekit']);
                if(! $magentoOrderId) {
                    Mage::getSingleton('core/session')->addError($this->__("Error when inserting the order has occurred. Please contact support."));
                    session_write_close();
                } else {
                    // Add $10 credit to customer.
                    $creditAdded = $model->addInitialCredit($magentoCustomerId);

                    $emailSended = $model->sendConfirmationEmail($magentoOrderId, $newPassword);
                }
                */
            } else {

                /*
                $addressExists = $model->addressExists($customer);
                if($addressExists) {
                    $addressId = $model->getAddressExistsId($customer);
                    $url = $helper->getSorryURL();
                    $this->_redirectUrl($url);
                    return;
                }
                */
                // Print Layout
                $this->loadLayout();
                $this->getLayout()->getBlock('giveawayfb')->assign('confirmed', ($customer)); // Assign var to template. To know if customer exists.
                $this->renderLayout();
                return;
            }
        }

        // Print Layout
        $this->loadLayout();
        $this->getLayout()->getBlock('giveawayfb')->assign('confirmed', ($customer)); // Assign var to template. To know if customer exists.
        $this->renderLayout();
    }


    public function verificationAction()
    {
        // Get form data.
        $email = $this->getRequest()->getPost('email');

        // Get helper.
        $helper = Mage::helper('giveawayfb');
        $model = Mage::getModel('giveawayfb/giveawayfb');

        // Check email validation.
        if (empty($email) || !Zend_Validate::is($email, 'EmailAddress')) {
            Mage::getSingleton('core/session')->addError($this->__('Please enter a valid email.'));
            session_write_close();

            $url = $helper->getModuleURL();
            $this->_redirectUrl($url);
            return;
        }

        // Check if email exists in customer.
        $customer = $model->customerExists($email);
        if ($customer !== false) {
            $url = $helper->getSorryURL();
            $this->_redirectUrl($url);
            return;
        }

        // Store email in session.
        $helper->setSessionData(array('email' => $email));

        // Check if email exists in giveawayfb.
        $sended = $model->emailSended($email);
        if ($sended) {
            $url = $helper->getResendURL();
            $this->_redirectUrl($url);
            return;
        }

        // Redirect to creation form.
        $url = $helper->getCreationURL();
        $this->_redirectUrl($url);
        return;
    }

    public function creationpostAction()
    {
        // Load modules.
        $helper = Mage::helper('giveawayfb');
        $model = Mage::getModel('giveawayfb/giveawayfb');

        // Check if there is an email loaded.
        $email = $helper->getSessionData('email');
        if (empty($email)) {
            $url = $helper->getModuleURL();
            $this->_redirectUrl($url);
            return;
        }

        // Get form data.
        $params = $this->getRequest()->getParams();
        if (empty($params['birthdate_year']) || !is_numeric($params['birthdate_year'])) $params['birthdate_year'] = date('Y');

        // Save params in session.
        $helper->setSessionData($params);

        // Revalidate data.
        if (
            empty($params['name']) ||
            empty($params['lastname']) ||
            empty($params['birthdate_month']) || !is_numeric($params['birthdate_month']) ||
            empty($params['birthdate_day']) || !is_numeric($params['birthdate_day'])
        ) {
            // Redirect.
            $helper->returnToCreationForm('Please enter all required data.');
            return;
        }

        // Validate captcha.
        $recaptcha_challenge_field = $this->getRequest()->getPost('recaptcha_challenge_field');
        $recaptcha_response_field = $this->getRequest()->getPost('recaptcha_response_field');
        $privateKey = $helper->getCaptchaConfig('private');
        $resp = recaptcha_check_answer($privateKey, $_SERVER["REMOTE_ADDR"], $recaptcha_challenge_field, $recaptcha_response_field);
        if (!$resp->is_valid) {
            // Redirect.
            $helper->returnToCreationForm('Unable to submit your request. Please try to submit your input again with a correct Security Code.');
            return;
        }

        // Store Data in temp table.
        $insertId = $model->insertData($params);
        if (!$insertId) {
            $helper->returnToCreationForm('Unable to submit your request.');
            return;
        }
        $helper->setSessionData(array('id' => $insertId));


        // Redirect to samples page.
        $url = $helper->getCreationSamplesURL();
        $this->_redirectUrl($url);
        return;
    }

    public function creationsamplespostAction()
    {
        $helper = Mage::helper('giveawayfb');
        $model = Mage::getModel('giveawayfb/giveawayfb');

        // Check if there is any data stored.
        $id = $helper->getSessionData('id');
        if (empty($id)) {
            $url = $helper->getCreationURL();
            $this->_redirectUrl($url);
            return;
        }

        // Get form data.
        $params = $this->getRequest()->getParams();
        // Save params in session.
        $helper->setSessionData($params);

        // Validate data.
        if (empty($params['samplekit']) || !is_numeric($params['samplekit'])) {
            // Save error.
            Mage::getSingleton('core/session')->addError($this->__("Please select a sample Kit."));
            session_write_close();

            // Redirect.
            $url = Mage::helper('giveawayfb')->getCreationSamplesURL();
            Mage::app()->getResponse()->setRedirect($url);
            return;
        }

        // Update custom table.
        $updated = $model->updateData($id, $params);
        if (!$updated) {
            $helper->returnToCreationForm('Unable to submit your request.');
            return;
        }


        // Redirect to address page.
        $url = $helper->getCreationAddressURL();
        $this->_redirectUrl($url);
        return;
    }

    public function creationaddresspostAction()
    {
        $helper = Mage::helper('giveawayfb');
        $model = Mage::getModel('giveawayfb/giveawayfb');

        // Check if there is any data stored.
        $id = $helper->getSessionData('id');
        if (empty($id)) {
            $url = $helper->getCreationURL();
            $this->_redirectUrl($url);
            return;
        }

        // Get form data.
        $params = $this->getRequest()->getParams();
        // Save params in session.
        $helper->setSessionData($params);

        // Validate data.
        if (
            empty($params['address1']) ||
            empty($params['city']) ||
            empty($params['state']) ||
            empty($params['zip'])
        ) {
            // Save error.
            Mage::getSingleton('core/session')->addError($this->__("Please select a sample Kit."));
            session_write_close();

            // Redirect.
            $url = $helper->getCreationAddressURL();
            Mage::app()->getResponse()->setRedirect($url);
            return;
        }

        // Check if customer exists by address.
        $addressExists = $model->addressExists($params);
        if ($addressExists) {
            Mage::log("Creation Magento customer error: customer address already exists.", null, 'giveawayfb.log');

            $url = $helper->getSorryURL();
            Mage::app()->getResponse()->setRedirect($url);
            return;
        }

        // Generate confirm id.
        $email = $helper->getSessionData('email');
        $params['confirmid'] = $helper->getConfirmId($id, $email);

        // Get default telephone.
        $params['telephone'] = $helper->getDefaultTelephone();

        // Save data.
        $updated = $model->updateData($id, $params);
        if (!$updated) {
            $helper->returnToCreationForm('Unable to submit your request.');
            return;
        }

        // Send verification email.
        $emailTemplate = $helper->getVerificationEmailTemplate();
        $confirmlink = $helper->getEmailVerifactionURL($params['confirmid']);
        try {
            $sessionData = $helper->getSessionData();
            $sessionData['confirmlink'] = $confirmlink;
            // Mage::log("Sending email - Email: ".$email." - name: ".$sessionData['name'].' - SenderEmail: '.$emailTemplate->getSenderEmail().' - SenderName: '.$emailTemplate->getSenderName().' - Subject: '.$emailTemplate->getTemplateSubject(), null, 'giveawayfb.log');
            $emailTemplate->send($email, $sessionData['name'], $sessionData);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $helper->returnToCreationForm($errorMessage);
            return;
        }

        // Unset session vars.
        $helper->unsetAllSessionData();


        // Redirect to thank page.
        $url = $helper->getCreationOkURL();
        $this->_redirectUrl($url);
        return;
    }

    public function resendpostAction()
    {
        // Load modules.
        $helper = Mage::helper('giveawayfb');
        $model = Mage::getModel('giveawayfb/giveawayfb');

        // Get Email.
        $email = $helper->getSessionData('email');

        // Check email validation.
        if (empty($email) || !Zend_Validate::is($email, 'EmailAddress')) {
            Mage::getSingleton('core/session')->addError($this->__('Please enter a valid email.'));
            session_write_close();

            $url = $helper->getResendURL();
            $this->_redirectUrl($url);
            return;
        }


        // Check if email exists in customer.
        $customer = $model->customerExists($email);
        if ($customer !== false) {
            $url = $helper->getSorryURL();
            $this->_redirectUrl($url);
            return;
        }

        // Check if email exists in giveawayfb.
        $sendedData = $model->emailSended($email);
        if (!$sendedData) {
            Mage::getSingleton('core/session')->addError($this->__("We were unable to verify your email."));
            session_write_close();

            $url = $helper->getResendURL();
            $this->_redirectUrl($url);
            return;
        }

        // Resend verification email.
        $emailTemplate = $helper->getVerificationEmailTemplate();
        try {
            $sendedData['confirmlink'] = $helper->getEmailVerifactionURL($sendedData['confirmid']);
            $emailTemplate->send($email, $sendedData['name'], $sendedData);
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__("Error sending email has occurred."));
            session_write_close();

            $url = $helper->getResendURL();
            $this->_redirectUrl($url);
            return;
        }

        // Unset session vars.
        $helper->unsetAllSessionData();


        // Redirect to thank page.
        $url = $helper->getResendOkURL();
        $this->_redirectUrl($url);
        return;
    }

    // Ajax handler for address verifiction
    public function verifyAddressAction()
    {
        $params = $this->getRequest()->getParams();
        if (empty($params)) {
            echo "Error";
            return false;
        }
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        if ($params['address2'] !== '') {
            $query = "SELECT * FROM  `giveawayfb` WHERE  `zip` LIKE  '" . $params['zip'] . "' AND  `address1` LIKE  '" . $params['address1'] . "' AND  `address2` LIKE  '" . $params['address2'] . "' AND  `city` LIKE  '" . $params['city'] . "' AND  `state` = '" . $params['state'] . "'";
        } else {
            $query = "SELECT * FROM  `giveawayfb` WHERE  `zip` LIKE  '" . $params['zip'] . "' AND  `address1` LIKE  '" . $params['address1'] . "' AND  `city` LIKE  '" . $params['city'] . "' AND  `state` = '" . $params['state'] . "'";
        }
        $results = $read->fetchAll($query);
        if (count($results) > 0) {
            echo 0;
        } else {
            echo 1;
        }

    }
}

