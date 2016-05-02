<?php
class Alliance_CustomerUnilever_Helper_Data extends Mage_Core_Helper_Abstract
{	
	const SEARCH_STRING = "@unilever.com";
	const CUSTOMER_GROUP = "Unilever Employee";
	const UNILEVER_EMAIL_TEMPLATE = 'Unilever Employee Verification';
	const UNILEVER_MODULE = 'customerunilever';
	
	public function getSearchString()
    {
		return self::SEARCH_STRING;
    }
	
	public function getUnileverCustomerGroup()
    {
		return self::CUSTOMER_GROUP;
    }

	public function getUnileverCustomerGroupId()
	{
		$targetGroup = Mage::getModel('customer/group');
		$targetGroup->load(self::CUSTOMER_GROUP, 'customer_group_code');
	
		return $targetGroup->getId();
	}
	
	public function getConfirmationEmailTemplate()
    {
        // Get template.
        $emailTemplate = Mage::getModel('core/email_template')->loadByCode(self::UNILEVER_EMAIL_TEMPLATE);

        // Set senders by default.
        $storeId = Mage::app()->getStore()->getStoreId();
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));

        return $emailTemplate;
    }
	
	public function getModuleName()
	{
		return self::UNILEVER_MODULE;
	}
	
	public function getCustomerGroupName($customer)
	{
		$groupId = $customer->getGroupId();
		$group = Mage::getModel('customer/group')->load($groupId);
		$groupName = $group->getCode();
		
		return $groupName;
	}
	
}