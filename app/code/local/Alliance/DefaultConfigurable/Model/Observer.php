<?php

/**
 * Class Alliance_DefaultConfigurable_Model_Observer
 */
class Alliance_DefaultConfigurable_Model_Observer
{
    /**
     * add column to simple products grid
     * @access public
     * @param $observer
     * @return Alliance_DefaultConfigurable_Model_Observer
     */
    public function addDefaultColumn($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid) {
            if (Mage::helper('alliance_defaultconfigurable')->isEnabled()) {
                if (!$block->isReadonly()) {
                    $block->addColumnAfter('default_combination', array(
                        'header'           => Mage::helper('alliance_defaultconfigurable')->__('Default'),
                        'header_css_class' => 'a-center',
                        'type'             => 'radio',
                        'name'             => 'default_combination',
                        'values'           => $this->_getDefaultConfigurationId(),
                        'align'            => 'center',
                        'index'            => 'entity_id',
                        'html_name'        => 'default_combination',
                        'sortable'         => false,
                        'filter'           => false,
                    ), 'in_products');
                }
            }
        }
        return $this;
    }

    /**
     * get the default configuration
     * @access protected
     * @return array|string
     */
    protected function _getDefaultConfigurationId()
    {
        $product = Mage::registry('current_product');
        if ($product) {
            return array($product->getData(Alliance_DefaultConfigurable_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE));
        }
        return '';
    }
}