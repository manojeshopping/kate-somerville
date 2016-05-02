<?php
class Alliance_Kiosk_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('kioskReportGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('kiosk/report')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
        
    }

    protected function _prepareColumns()
    {
        $this->addColumn('username', array(
         'header'    => Mage::helper('kiosk/report')->__('First Name'),
         'align'     => 'left',
         'index'     => 'username',
        ));
        $this->addColumn('age', array(
         'header'    => Mage::helper('kiosk/report')->__('Age'),
         'align'     => 'left',
         'index'     => 'age',
        ));
        $this->addColumn('date_completed', array(
         'header'    => Mage::helper('kiosk/report')->__('Date Completed'),
         'align'     => 'left',
         'type'      => 'datetime',
         'index'     => 'date_completed',
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/details', array('id' => $row->getId()));
    }
}

