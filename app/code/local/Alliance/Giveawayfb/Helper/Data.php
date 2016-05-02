<?php

class Alliance_Giveawayfb_Helper_Data extends Mage_Core_Helper_Abstract
{
    // Set constants.
    const SAMPLE_KIT_SKUS = "35233,35234,35235";
    const SKIN_CONCERN_ATTRIBUTES = 'primary_skin_concern';
    const SKIN_CONCERN_ATTRIBUTES2 = 'secondary_skin_concern';
    const VERIFIACTION_EMAIL_TEMPLATE_NAME = 'Facebook Samples Verification';
    const CONFIRMATION_EMAIL_TEMPLATE_NAME = 'Facebook Samples Confirmation';
    const SECRET_VERIFICATION_KEY = 'a4GvtT';
    const CUSTOMER_PASSWORD_LENGTH = 8;
    const CUSTOMER_GROUP_NAME = 'Facebook Sample';
    const DEFAULT_TELEPHONE_NUMBER = '555-555-5555';
    const DEFAULT_SHIPPING_METHOD = 'alliance_shipping_standard_shipping';
    const DEFAULT_PAYMENT_METHOD = 'checkmo';
    const CREDIT_AMOUNT = 10;
    const CREDIT_COMMENT = '$[credit_amount] Giveawayfb Credit';
    const INVOICE_COMMENT = 'Facebook Samples Invoice';
    const MAIL_CHIMP_API_KEY = '9a149701806a79902db7ca1bd59ae3cc-us2';
    const MAIL_CHIMP_LIST_ID = '20ad0b9d6e';


    // Get Module URL.
    public function getModuleURL()
    {
        $routname = Mage::app()->getRequest()->getRouteName();
        return Mage::getUrl($routname, array('_nosid' => true));
    }

    // Get Customer URL.
    public function getCustomerURL()
    {
        $moduleURL = self::getModuleURL();
        return $moduleURL . 'customer/';
    }

    // Get Sorry URL.
    public function getSorryURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'sorry/';
    }

    // Get Verification email URL.
    public function getVerificationURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'verification/';
    }

    // Get post account creation URL.
    public function getCreationURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'creation/';
    }

    // Get creation post URL.
    public function getCreationPostURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'creationpost/';
    }

    // Get creation samples URL.
    public function getCreationSamplesURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'creationsamples/';
    }

    // Get creation samples post URL.
    public function getCreationSamplesPostURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'creationsamplespost/';
    }

    // Get creation address URL.
    public function getCreationAddressURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'creationaddress/';
    }

    // Get creation address post URL.
    public function getCreationAddressPostURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'creationaddresspost/';
    }

    // Get Creation customer URL.
    public function getCreationOkURL()
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'success/';
    }

    // Get Creation customer URL.
    public function getEmailVerifactionURL($confirmid = null)
    {
        $customerURL = self::getCustomerURL();
        $url = $customerURL . 'emailverifaction/';

        if (!empty($confirmid)) $url .= "?id=" . $confirmid;

        return $url;
    }

    // Get resend URL.
    public function getResendURL($confirmid = null)
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'resend/';
    }

    // Get resend post URL.
    public function getResendPostURL($confirmid = null)
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'resendpost/';
    }

    // Get resend ok URL.
    public function getResendOkURL($confirmid = null)
    {
        $customerURL = self::getCustomerURL();
        return $customerURL . 'resendsuccess/';
    }


    // Get Data from session.
    public function getSessionData($key = null)
    {
        $sessionData = Mage::getSingleton('core/session')->getData(Mage::app()->getRequest()->getModuleName());

        if (!is_null($key)) {
            if (is_array($sessionData) && array_key_exists($key, $sessionData)) return $sessionData[$key];
            return "";
        }

        return $sessionData;
    }

    // Set data session.
    public function setSessionData($keyValue)
    {
        if (!is_array($keyValue)) return;

        $prevSession = $this->getSessionData();
        if (is_array($prevSession)) $data = array_merge($prevSession, $keyValue);
        else $data = $keyValue;

        Mage::getSingleton('core/session')->setData(Mage::app()->getRequest()->getModuleName(), $data);
    }

    // unset  all data session.
    public function unsetAllSessionData()
    {
        Mage::getSingleton('core/session')->unsetData(Mage::app()->getRequest()->getModuleName());
    }


    // Get captcha config - Public or Private key
    public function getCaptchaConfig($type)
    {
        return Mage::getStoreConfig('giveawayfb_recaptcha/recaptcha_keys/' . $type . '_key');
    }


    // Get product list from sample kit category.
    public function getProductList()
    {
        $_productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('sku', array('in' => explode(',', self::SAMPLE_KIT_SKUS)))
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)// ->addAttributeToSort('name', 'ASC')
        ;
        $backendModel = $_productCollection->getResource()->getAttribute('media_gallery')->getBackend();

        $productslist = array();
        foreach ($_productCollection as $_product) {
            $backendModel->afterLoad($_product);
            $galleryImages = $_product->getMediaGalleryImages();

            $productslist[$_product->getId()] = array(
                'title' => $_product->getTitle(),
                'popups' => array(
                    $_product->getData('pop_up_description_1'),
                    $_product->getData('pop_up_description_2'),
                    $_product->getData('pop_up_description_3')
                ),
                'galleryImages' => $galleryImages,
            );
        }

        return $productslist;
    }

    // Get concern list.
    public function getConcernsList($code = null)
    {
        if (is_null($code)) $code = self::SKIN_CONCERN_ATTRIBUTES;

        $_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('customer', $code);
        $_attributes = Mage::getModel('catalog/resource_eav_attribute')->load($_attributeId);
        $_concerns = $_attributes->getSource()->getAllOptions();

        return $_concerns;
    }

    // Get USA states.
    public function getUSAStates()
    {
        $states = Mage::getResourceModel('directory/region_collection')->addCountryFilter('US')->loadData()->toOptionArray(false);

        return $states;
    }


    // Get encripted confirm id.
    public function getConfirmId($id, $email)
    {
        return md5($id . $email . self::SECRET_VERIFICATION_KEY);
    }

    // Get verification email template.
    public function getVerificationEmailTemplate()
    {
        // Get template.
        $emailTemplate = Mage::getModel('core/email_template')->loadByCode(self::VERIFIACTION_EMAIL_TEMPLATE_NAME);

        // Set senders by default.
        $storeId = Mage::app()->getStore()->getStoreId();
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));

        return $emailTemplate;
    }

    // Get confirmation email template.
    public function getConfirmationEmailTemplate()
    {
        // Get template.
        $emailTemplate = Mage::getModel('core/email_template')->loadByCode(self::CONFIRMATION_EMAIL_TEMPLATE_NAME);

        // Set senders by default.
        $storeId = Mage::app()->getStore()->getStoreId();
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));

        return $emailTemplate;
    }


    // Return to creation form when error.
    public function returnToCreationForm($msg)
    {
        Mage::getSingleton('core/session')->addError($this->__($msg));
        session_write_close();

        $url = $this->getCreationURL();
        Mage::app()->getResponse()->setRedirect($url);
    }

    // Return password length constant.
    public function getPasswordLength()
    {
        return self::CUSTOMER_PASSWORD_LENGTH;
    }

    // Get group for the customer.
    public function getCustomerGroupId()
    {
        $groupModel = Mage::getModel('customer/group');
        $group = $groupModel->load(self::CUSTOMER_GROUP_NAME, 'customer_group_code');
        return $group->getId();
    }

    // Generate Date of Birthday from month and day in format Y-m-d.
    public function generateDob($month, $day)
    {
        return date('Y') . '-' . $month . '-' . $day;
    }

    // Get if for concern 2, from concern1 id.
    public function getConcernId2($concern1Id)
    {
        $concern1List = self::getConcernsList();
        foreach ($concern1List as $oneConcern) {
            if ($concern1Id == $oneConcern['value']) {
                $concern1Label = $oneConcern['label'];
                break;
            }
        }


        $concern2List = self::getConcernsList(self::SKIN_CONCERN_ATTRIBUTES2);
        foreach ($concern2List as $oneConcern) {
            if ($concern1Label == $oneConcern['label']) {
                return $oneConcern['value'];
            }
        }


        return $concern2Id;
    }

    public function getConcernLabel($concernId)
    {
        $concernList = self::getConcernsList();
        foreach ($concernList as $oneConcern) {
            if ($concernId == $oneConcern['value']) {
                return $oneConcern['label'];
            }
        }

        return false;
    }

    // Generate custom password.
    public function generateNewPassword()
    {
        $customer = Mage::getModel("customer/customer");
        return $customer->generatePassword($this->getPasswordLength());
    }


    // Get default telephone for order.
    public function getDefaultTelephone()
    {
        return self::DEFAULT_TELEPHONE_NUMBER;
    }

    // Get shipping method for order.
    public function getShippingMethod()
    {
        return self::DEFAULT_SHIPPING_METHOD;
    }

    // Get shipping method for order.
    public function getPaymentMethod()
    {
        return self::DEFAULT_PAYMENT_METHOD;
    }

    // Get comment for order.
    public function getOrderComment()
    {
        return "Order automatically by Giveawayfb module.";
    }

    // Get credit amount.
    public function getCreditAmount()
    {
        return self::CREDIT_AMOUNT;
    }

    // Get credit amount.
    public function getCreditComment()
    {
        return str_replace("[credit_amount]", self::CREDIT_AMOUNT, self::CREDIT_COMMENT);
    }

    // Get invoice amount.
    public function getInvoiceComment()
    {
        return self::INVOICE_COMMENT;
    }

    // Get mailchimp API key.
    public function getMailchimpApiKey()
    {
        return self::MAIL_CHIMP_API_KEY;
    }

    // Get mailchimp List id.
    public function getMailchimpListId()
    {
        return self::MAIL_CHIMP_LIST_ID;
    }

    // Get Giveaway Skus
    public function getSampleKitSkus()
    {
        return explode(',', self::SAMPLE_KIT_SKUS);
    }

    // Check if customer have SKUs orders.
    public function checkCustomerOrders($customerId, $sampleSkus)
    {
        $customerOrders = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('base_grand_total', 0);
        $order_id = "";
        $isSku = false;
        if ($customerOrders->count() > 0) {
            // Check order items.
            foreach ($customerOrders as $_order) {
                $items = $_order->getAllItems();
                foreach ($items as $_item) {
                    $isSku = in_array($_item->getProduct()->getSku(), $sampleSkus);
                    if ($isSku) break;
                }

                if ($isSku) {
                    $order_id = $_order->getId();
                    break;
                }
            }
        }

        return $order_id;
    }
}

