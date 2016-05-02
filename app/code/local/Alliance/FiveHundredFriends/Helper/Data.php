<?php

/**
 * Class Alliance_FiveHundredFriends_Helper_Data
 *
 * General helper, used primarily for fetching values from system configuration
 */
class Alliance_FiveHundredFriends_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Fetches the 500 Friends "User Id", located in System Configuration under 'alliance_fivehundredfriends/access_credentials/user_id'
     *
     * @return string
     */
    public function getUserId()
    {
        return Mage::getStoreConfig('alliance_fivehundredfriends/access_credentials/user_id');
    }

    /**
     * Fetches the 500 Friends "Widget ID", located in System Configuration under 'alliance_fivehundredfriends/access_credentials/widget_id'
     *
     * @return string
     */
    public function getWidgetId()
    {
        return Mage::getStoreConfig('alliance_fivehundredfriends/access_credentials/widget_id');
    }

    /**
     * Fetches the 500 Friends "Token Key", located in System Configuration under 'alliance_fivehundredfriends/access_credentials/token_key'
     *
     * @return string
     */
    public function getTokenKey()
    {
        return Mage::getStoreConfig('alliance_fivehundredfriends/access_credentials/token_key');
    }
	/**
     * Fetches the 500 Friends "Relations", located in System Configuration under 'alliance_fivehundredfriends/rewards/relations'
     *
     * @return string
     */
	public function getRelationsConfiguration()
	{
		return unserialize(Mage::getStoreConfig('alliance_fivehundredfriends/rewards/relations'));
	}

    /**
     * Fetches the 500 Friends "Share Widget ID", located in System Configuration under 'alliance_fivehundredfriends/access_credentials/share_widget_id'
     *
     * @return string
     */
    public function getShareWidgetId()
    {
        return Mage::getStoreConfig('alliance_fivehundredfriends/access_credentials/share_widget_id');
    }

    /**
     * Fetches the 500 Friends "Surprise and Delight Widget ID", located in System Configuration under 'alliance_fivehundredfriends/access_credentials/surprisedelight_widget_id'
     *
     * @return string
     */
    public function getSurprisedelightWidgetId()
    {
        return Mage::getStoreConfig('alliance_fivehundredfriends/access_credentials/surprisedelight_widget_id');
    }

    /**
     * Fetches the 500 Friends "Enable Debug Logging" value, located in System Configuration under 'alliance_fivehundredfriends/developer/debug_log'
     *
     * @return boolean
     */
    public function getDebugLog()
    {
        return Mage::getStoreConfig('alliance_fivehundredfriends/developer/debug_log');
    }

    public function currentCustomerEnrollmentStatus()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer           = Mage::getSingleton('customer/session')->getCustomer();
            $email              = $customer->getEmail();
            $request_parameters = array(
                'email' => $email,
            );
            $customer_show      = Mage::helper('alliance_fivehundredfriends/api')->customerShow($request_parameters);

            if ($customer_show['success'] && $customer_show['data']['status'] == 'active') {
                return 'Enrolled';
            } else {
                return 'Not Enrolled';
            }
        } else {
            return 'Not Logged In';
        }
    }

	public function getCustomerRestriction()
	{
		$restrictedCustomerGroups = array(
			"Employee",
			"Friends & Family",
			"Unilever Employee"		
		); 
	
		if( Mage::getSingleton('customer/session')->isLoggedIn() ){
			$customergroup = Mage::getModel('customer/group')->load( Mage::getSingleton('customer/session')->getCustomerGroupId() )->getCustomerGroupCode();
			if(in_array($customergroup, $restrictedCustomerGroups) ){
				return TRUE;
			}
		}
	}

	
	public function getCustomerRestrictionBackend($customerid)
	{
		$restrictedCustomerGroups = array(
			"Employee",
			"Friends & Family",
			"Unilever Employee"		
		);

		$customer = Mage::getModel('customer/customer')->load($customerid);		
		$customergroup = Mage::getModel('customer/group')->load( $customer->getGroupId() )->getCustomerGroupCode();
	
		if(in_array($customergroup, $restrictedCustomerGroups) ){
			return TRUE;
		}
	}
}

