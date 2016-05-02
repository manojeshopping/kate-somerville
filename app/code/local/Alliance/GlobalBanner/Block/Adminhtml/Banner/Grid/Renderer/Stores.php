<?php

/**
 * Class Alliance_GlobalBanner_Block_Adminhtml_Banner_Grid_Renderer_Stores
 */
class Alliance_GlobalBanner_Block_Adminhtml_Banner_Grid_Renderer_Stores extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $stores = $row->getData('stores');
        if (!$stores) {
            return Mage::helper('alliance_globalbanner')->__('None');
        }

        $html = '';
        $data = Mage::getSingleton('adminhtml/system_store')->getStoresStructure(false, explode(',', $stores));
        foreach ($data as $website) {
            $html .= $website['label'] . '<br/>';
            foreach ($website['children'] as $group) {
                $html .= str_repeat('&nbsp;', 3) . $group['label'] . '<br/>';
                foreach ($group['children'] as $store) {
                    $html .= str_repeat('&nbsp;', 6) . '<strong>' . $store['label'] . '</strong><br/>';
                }
            }
        }

        return $html;
    }
}
