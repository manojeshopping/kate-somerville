<?php

/**
 * Class Alliance_DefaultConfigurable_Helper_Data
 */
class Alliance_DefaultConfigurable_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * default configuration attribute code
     */
    const DEFAULT_CONFIGURATION_ATTRIBUTE_CODE  = 'default_configuration_id';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return TRUE;
    }
}