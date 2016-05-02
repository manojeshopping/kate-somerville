<?php

class Alliance_KateReviews_Block_Adminhtml_Pending_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('alliance_katereviews_pending_form');
        $this->setTitle($this->__('Review Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('alliance_katereviews');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('alliance_katereviews')->__('Product Review Information'),
            'class'     => 'fieldset-wide',
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $fieldset->addField('customer_id', 'hidden', array(
            'name' => 'customer_id',
        ));

        $fieldset->addField('product_id', 'hidden', array(
            'name' => 'product_id',
        ));

        $fieldset->addField('star_rating', 'select', array(
            'name'      => 'star_rating',
            'label'     => Mage::helper('alliance_katereviews')->__('Star Rating'),
            'title'     => Mage::helper('alliance_katereviews')->__('Star Rating'),
            'required'  => true,
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => '5',
                    'label' => '5',
                ),
                array(
                    'value' => '4',
                    'label' => '4',
                ),
                array(
                    'value' => '3',
                    'label' => '3',
                ),
                array(
                    'value' => '2',
                    'label' => '2',
                ),
                array(
                    'value' => '1',
                    'label' => '1',
                ),
            ),
        ));

        $fieldset->addField('recommended', 'select', array(
            'name'      => 'recommended',
            'label'     => Mage::helper('alliance_katereviews')->__('Recommended?'),
            'title'     => Mage::helper('alliance_katereviews')->__('Recommended?'),
            'required'  => true,
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => 'Yes',
                    'label' => 'Yes',
                ),
                array(
                    'value' => 'No',
                    'label' => 'No',
                ),
            ),
        ));

        $fieldset->addField('review_headline', 'text', array(
            'name'      => 'review_headline',
            'label'     => Mage::helper('alliance_katereviews')->__('Review Headline'),
            'title'     => Mage::helper('alliance_katereviews')->__('Review Headline'),
        ));

        $fieldset->addField('review_text', 'textarea', array(
            'name'      => 'review_text',
            'label'     => Mage::helper('alliance_katereviews')->__('Review Text'),
            'title'     => Mage::helper('alliance_katereviews')->__('Review Text'),
        ));

        $fieldset->addField('date', 'hidden', array(
            'label'     => Mage::helper('alliance_katereviews')->__('Date Written'),
        ));

        $fieldset->addField('location', 'text', array(
            'name'      => 'location',
            'label'     => Mage::helper('alliance_katereviews')->__('City and State'),
            'title'     => Mage::helper('alliance_katereviews')->__('City and State'),
        ));

        $fieldset->addField('age_range', 'select', array(
            'name'      => 'age_range',
            'label'     => Mage::helper('alliance_katereviews')->__('Age Range'),
            'title'     => Mage::helper('alliance_katereviews')->__('Age Range'),
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => '18 - 24',
                    'label' => '18 - 24',
                ),
                array(
                    'value' => '25 - 34',
                    'label' => '25 - 34',
                ),
                array(
                    'value' => '35 - 44',
                    'label' => '35 - 44',
                ),
                array(
                    'value' => '45 - 54',
                    'label' => '45 - 54',
                ),
                array(
                    'value' => 'over 54',
                    'label' => 'over 54',
                ),
            ),
        ));

        $fieldset->addField('owned_for', 'select', array(
            'name'      => 'owned_for',
            'label'     => Mage::helper('alliance_katereviews')->__('Owned For'),
            'title'     => Mage::helper('alliance_katereviews')->__('Owned For'),
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => 'Less than 1 week',
                    'label' => 'Less than 1 week',
                ),
                array(
                    'value' => '1 - 4 weeks',
                    'label' => '1 - 4 weeks',
                ),
                array(
                    'value' => '1 - 3 months',
                    'label' => '1 - 3 months',
                ),
                array(
                    'value' => '3 - 6 months',
                    'label' => '3 - 6 months',
                ),
                array(
                    'value' => '6 - 12 months',
                    'label' => '6 - 12 months',
                ),
                array(
                    'value' => 'over 1 year',
                    'label' => 'over 1 year',
                ),
            ),
        ));

        $fieldset->addField('purchased_at', 'select', array(
            'name'      => 'purchased_at',
            'label'     => Mage::helper('alliance_katereviews')->__('Purchased At'),
            'title'     => Mage::helper('alliance_katereviews')->__('Purchased At'),
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => 'Katesomerville.com',
                    'label' => 'Katesomerville.com',
                ),
                array(
                    'value' => 'Skin Health Experts Clinic',
                    'label' => 'Skin Health Experts Clinic',
                ),
                array(
                    'value' => 'Bergdorf Goodman',
                    'label' => 'Bergdorf Goodman',
                ),
                array(
                    'value' => 'Neiman Marcus',
                    'label' => 'Neiman Marcus',
                ),
                array(
                    'value' => 'Nordstrom',
                    'label' => 'Nordstrom',
                ),
                array(
                    'value' => 'Sephora',
                    'label' => 'Sephora',
                ),
                array(
                    'value' => 'QVC',
                    'label' => 'QVC',
                ),
                array(
                    'value' => 'Other',
                    'label' => 'Other',
                ),
            ),
        ));

        $fieldset->addField('skin_concern', 'select', array(
            'name'      => 'skin_concern',
            'label'     => Mage::helper('alliance_katereviews')->__('Skin Concern'),
            'title'     => Mage::helper('alliance_katereviews')->__('Skin Concern'),
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => 'Acne',
                    'label' => 'Acne',
                ),
                array(
                    'value' => 'Anti-Aging',
                    'label' => 'Anti-Aging',
                ),
                array(
                    'value' => 'Anti-Aging & Acne',
                    'label' => 'Anti-Aging & Acne',
                ),
                array(
                    'value' => 'Sensitive',
                    'label' => 'Sensitive',
                ),
                array(
                    'value' => 'Discoloration',
                    'label' => 'Discoloration',
                ),
                array(
                    'value' => 'Dry',
                    'label' => 'Dry',
                ),
                array(
                    'value' => 'Oily',
                    'label' => 'Oily',
                ),
            ),
        ));

        $fieldset->addField('often_used', 'select', array(
            'name'      => 'often_used',
            'label'     => Mage::helper('alliance_katereviews')->__('How Often Used'),
            'title'     => Mage::helper('alliance_katereviews')->__('How Often Used'),
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => 'Every day',
                    'label' => 'Every day',
                ),
                array(
                    'value' => 'Once a week',
                    'label' => 'Once a week',
                ),
                array(
                    'value' => 'A few times a month',
                    'label' => 'A few times a month',
                ),
                array(
                    'value' => 'Once a month',
                    'label' => 'Once a month',
                ),
                array(
                    'value' => 'A few times a year',
                    'label' => 'A few times a year',
                ),
                array(
                    'value' => 'Once a year',
                    'label' => 'Once a year',
                ),
                array(
                    'value' => 'Never',
                    'label' => 'Never',
                ),
            ),
        ));

        $fieldset->addField('member_status', 'select', array(
            'name'      => 'member_status',
            'label'     => Mage::helper('alliance_katereviews')->__('Membership Status'),
            'title'     => Mage::helper('alliance_katereviews')->__('Membership Status'),
            'values'    => array(
                array(
                    'value' => '',
                    'label' => '',
                ),
                array(
                    'value' => 'Yes',
                    'label' => 'Yes',
                ),
                array(
                    'value' => 'No',
                    'label' => 'No',
                ),
            ),
            'after_element_html' =>

                '<tr><td class="label"><label>Date Written</label></td>
                <td class="value">' .$this->_getDateValue($this->getRequest()->getParam('id')) . '</td></tr>

                <tr><td class="label"><label>Customer Name</label></td>
                <td class="value">' .$this->_getCustomerName($this->getRequest()->getParam('id')) . '</td></tr>

                <tr><td class="label"><label>Customer Email</label></td>
                <td class="value">' .$this->_getCustomerEmail($this->getRequest()->getParam('id')) . '</td></tr>

                <tr><td class="label"><label>Product Name</label></td>
                <td class="value">' .$this->_getProductName($this->getRequest()->getParam('id')) . '</td></tr>

                <tr><td class="label"><label>Product SKU</label></td>
                <td class="value">' .$this->_getProductSku($this->getRequest()->getParam('id')) . '</td></tr>',
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => Mage::helper('alliance_katereviews')->__('Review Status'),
            'title'     => Mage::helper('alliance_katereviews')->__('Review Status'),
            'required'  => true,
            'values'    => array(
                array(
                    'value' => 'Pending',
                    'label' => 'Pending',
                ),
                array(
                    'value' => 'Approved',
                    'label' => 'Approved',
                ),
                array(
                    'value' => 'Denied',
                    'label' => 'Denied',
                ),
            ),
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getDateValue($review_id)
    {
        $review = Mage::getModel('alliance_katereviews/review')->load($review_id);
        return $review->getDate();
    }

    protected function _getProductName($review_id)
    {
        $review = Mage::getModel('alliance_katereviews/review')->load($review_id);
        $product = Mage::getModel('catalog/product')->load($review->getProductId());
        return $product->getName();
    }

    protected function _getCustomerEmail($review_id)
    {
        $review = Mage::getModel('alliance_katereviews/review')->load($review_id);
        $customer = Mage::getModel('customer/customer')->load($review->getCustomerId());
        return $customer->getEmail();
    }

    protected function _getProductSku($review_id)
    {
        $review = Mage::getModel('alliance_katereviews/review')->load($review_id);
        $product = Mage::getModel('catalog/product')->load($review->getProductId());
        return $product->getSku();
    }

    protected function _getCustomerName($review_id)
    {
        $review = Mage::getModel('alliance_katereviews/review')->load($review_id);
        $customer = Mage::getModel('customer/customer')->load($review->getCustomerId());
        return $customer->getName();
    }
}
