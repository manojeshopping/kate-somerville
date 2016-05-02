<?php

/**
 * Class Alliance_GlobalBanner_Block_Adminhtml_Homepage_Banner_Grid_Renderer_Image
 */
class Alliance_GlobalBanner_Block_Adminhtml_Banner_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($row->getData($this->getColumn()->getIndex()) == "") {
            return '';
        } else {
            $html = '<img ';
            $html .= 'id="' . $this->getColumn()->getId() . '" ';
            $html .= 'width="100" ';
            $html .= 'src="' . Mage::getBaseUrl("media") . $row->getData($this->getColumn()->getIndex()) . '"';
            $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';

            return $html;
        }
    }
}
