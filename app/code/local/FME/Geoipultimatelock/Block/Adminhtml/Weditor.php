<?php
class FME_Geoipultimatelock_Block_Adminhtml_Weditor extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    { 
        $this->setElement($element);
        $div = "<textarea id='fmedescription' name='fmedescription' class='mceEditor' style='min-height:500px; width:320px;'>".Mage::helper('geoipultimatelock')->getFmeCmsContents()."</textarea>";
        $div .= '<script language="javascript" type="text/javascript" src="'.$this->getJsUrl("tiny_mce/tiny_mce.js").'"></script>
				 <script language="javascript" type="text/javascript" src="'.$this->getJsUrl("mage/adminhtml/wysiwyg/tiny_mce/setup.js").'"></script>
                 <script type="text/javascript">
					wysiwygpage_content = new tinyMceWysiwygSetup("fmedescription",{"enabled":true,"hidden":false});
                    Event.observe(window, "load", wysiwygpage_content.setup.bind(wysiwygpage_content, "exact"));
                </script>';
                
        return $div;
    }
}
