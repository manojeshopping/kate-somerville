<?php
class Alliance_Kiosk_Block_Adminhtml_Question_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('kioskQuestionGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('kiosk/question')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('sort_order', array(
         'header'    => Mage::helper('kiosk/question')->__('Sort Order'),
         'align'     => 'left',
         'index'     => 'sort_order',
        ));
        $this->addColumn('text', array(
         'header'    => Mage::helper('kiosk/question')->__('Text'),
         'align'     => 'left',
         'index'     => 'text',
        ));
        $this->addColumn('hint_title', array(
         'header'    => Mage::helper('kiosk/question')->__('Hint Title'),
         'align'     => 'left',
         'index'     => 'hint_title',
        ));
        $this->addColumn('hint_text', array(
         'header'    => Mage::helper('kiosk/question')->__('Hint Content'),
         'align'     => 'left',
         'index'     => 'hint_text',
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}

