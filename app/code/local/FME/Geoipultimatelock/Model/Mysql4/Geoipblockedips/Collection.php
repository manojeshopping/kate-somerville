<?php

class FME_Geoipultimatelock_Model_Mysql4_Geoipblockedips_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('geoipultimatelock/geoipblockedips');
    }

    /**
     * Add Customer data to collection
     *
     * @return Mage_Log_Model_Resource_Visitor_Online_Collection
     */
    public function addCustomerData() {
        $customer = Mage::getModel('customer/customer');
        // alias => attribute_code
        $attributes = array(
            'customer_lastname' => 'lastname',
            'customer_firstname' => 'firstname',
            'customer_email' => 'email'
        );

        foreach ($attributes as $alias => $attributeCode) {
            $attribute = $customer->getAttribute($attributeCode);
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */

            if ($attribute->getBackendType() == 'static') {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $this->getSelect()->joinLeft(
                        array($tableAlias => $attribute->getBackend()->getTable()), sprintf('%s.entity_id=main_table.customer_id', $tableAlias), array($alias => $attribute->getAttributeCode())
                );

                $this->_fields[$alias] = sprintf('%s.%s', $tableAlias, $attribute->getAttributeCode());
            } else {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $joinConds = array(
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    $this->getConnection()->quoteInto($tableAlias . '.attribute_id=?', $attribute->getAttributeId())
                );

                $this->getSelect()->joinLeft(
                        array($tableAlias => $attribute->getBackend()->getTable()), join(' AND ', $joinConds), array($alias => 'value')
                );

                $this->_fields[$alias] = sprintf('%s.value', $tableAlias);
            }
        }

        $this->setFlag('has_customer_data', true);
        return $this;
    }

}