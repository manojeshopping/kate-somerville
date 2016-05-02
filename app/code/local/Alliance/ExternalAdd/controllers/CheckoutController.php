<?php

/**
 * Class Alliance_ExternalAdd_CheckoutController
 */
class Alliance_ExternalAdd_CheckoutController extends Mage_Core_Controller_Front_Action
{
    /**
     * Adds a product to the cart by SKU
     */
    public function cartAction()
    {
        $cart = Mage::getSingleton('checkout/cart');
        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_error($product);
                return;
            }

            $cart->addProduct($product->getId(), $params);

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$cart->getQuote()->getHasError()) {
                $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                $this->_getSession()->addSuccess($message);
                $this->_success();
            } else {
                $this->_error($product);
            }

        } catch (Mage_Core_Exception $e) {
            $messages = array_unique(explode("\n", $e->getMessage()));
            foreach ($messages as $message) {
                $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
            }
            $this->_error($product);
            return;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_error($product);
            return;
        }
    }

    /**
     * Load the product
     *
     * @return bool
     */
    protected function _initProduct()
    {
        $product_sku = (string) $this->getRequest()->getParam('sku');
        if ($product_sku) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByAttribute('sku', $product_sku);
            if ($product) {
                return $product;
            }
        }
        return false;
    }

    /**
     * Load the session
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Redirect on success
     */
    protected function _success()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * Redirect on error
     *
     * @param $product
     */
    protected function _error($product)
    {
        if ($product) {
            if (in_array($product->getVisibility(), array(2, 4))) {
                $this->_redirect('catalog/product/view/id/' . $product->getId());
                return;
            }
        }
        $this->_redirect('');
        return;
    }
}
