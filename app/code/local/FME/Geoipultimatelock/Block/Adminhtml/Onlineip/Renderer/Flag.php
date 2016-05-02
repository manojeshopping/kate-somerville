<?php

/**
 * Geoip Ultimate Lock extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Geoipultimatelock
 * @author     RT <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
class FME_Geoipultimatelock_Block_Adminhtml_Onlineip_Renderer_Flag extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    public function render(Varien_Object $row) {

        $data = Mage::helper('geoipultimatelock')->getDataByRemoteAddr($row->getRemoteAddr()); 
        $flag = 'noflag';
        $cName = 'Unknown';
        
        if ($data['cc'] != '') {
            $flag = strtolower($data['cc']);
            $cName = $data['cn'];
        }
        
        $imgstr = "<img src=\"".$this->getSkinUrl('images/geoipflags/'.$flag.'.png')."\"/>  ".$cName;
        
        return $imgstr;
    }
}
