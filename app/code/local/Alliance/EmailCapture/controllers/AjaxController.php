<?php

/**
 * Class Alliance_EmailCapture_CaptureController
 */
class Alliance_EmailCapture_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Handles Email Capture subscription AJAX and returns JSON response
     *
     * @return bool
     */
    public function subscribeAction()
    {
        $post_data = $this->getRequest()->getPost();
        if (@$email = $post_data['email']) {
            $api = Mage::helper('alliance_emailcapture/mailchimp');
            $response = $api->call('lists/subscribe', array(
                'id' => $api->getListId(),
                'email' => array('email' => $email),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
            ));
            if (@$response['status'] !== 'error' && @isset($response['email'])) {
                echo json_encode(array('status' => 'success'));
                return true;
            }
        }
        echo json_encode(array('status' => 'error'));
        return false;
    }
}