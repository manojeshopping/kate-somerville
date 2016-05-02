<?php

class FME_Geoipultimatelock_Block_Geoipultimatelock extends FME_Geoipultimatelock_Block_Abstract {

    public function _prepareLayout() {
        
        return parent::_prepareLayout();
    }

    public function getGeoipultimatelock() {
        
        if (!$this->hasData('geoipultimatelock')) {
            $this->setData('geoipultimatelock', Mage::registry('geoipultimatelock'));
        }
        return $this->getData('geoipultimatelock');
    }

    
}