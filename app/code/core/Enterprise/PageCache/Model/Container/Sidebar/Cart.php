<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cart sidebar container
 */
class Enterprise_PageCache_Model_Container_Sidebar_Cart extends Enterprise_PageCache_Model_Container_Advanced_Quote
{
    /**
     * @deprecated since 1.12.0.0
     */
    const CACHE_TAG_PREFIX = 'cartsidebar';

    /**
     * Get identifier from cookies
     *
     * @deprecated since 1.12.0.0
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CART, '')
            . $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        $renders = $this->_placeholder->getAttribute('item_renders');
        $block->deserializeRenders($renders);
        return $block->toHtml();
    }

    /**
     * Generate placeholder content before application was initialized and apply to page content if possible
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        if (!array_key_exists(Enterprise_PageCache_Model_Cookie::COOKIE_CART, $_COOKIE)) {
            $randomString = Enterprise_PageCache_Helper_Data::getRandomString(16);
            Enterprise_PageCache_Model_Cookie::setCartCookieValue($randomString);
            $_COOKIE[Enterprise_PageCache_Model_Cookie::COOKIE_CART] = $randomString;
        }

        return parent::applyWithoutApp($content);
    }
}
