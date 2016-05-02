<?php

class FME_Geoipultimatelock_Model_Mysql4_Geoipblockedips extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        // Note that the id refers to the key field in your database table.
        $this->_init('geoipultimatelock/geoipblockedips', 'geoipblockedips_id');
    }

}