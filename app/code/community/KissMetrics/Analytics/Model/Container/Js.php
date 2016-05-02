<?php

class KissMetrics_Analytics_Model_Container_Js extends Enterprise_PageCache_Model_Container_Abstract { 

    protected function _getCacheId()
    {
        return 'KISSMETRICS_ANALYITICS_JS' . md5($this->_placeholder->getAttribute('cache_id'));
    }

    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->toHtml();
    }
    
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null) { 
    
        return false; 
    }  

}