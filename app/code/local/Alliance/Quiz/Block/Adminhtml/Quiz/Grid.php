<?php

class Alliance_Quiz_Block_Adminhtml_Quiz_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('quizGrid');
      $this->setDefaultSort('quiz_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('quiz/quiz')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('quiz_id', array(
          'header'    => Mage::helper('quiz')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'quiz_id',
      ));

      $this->addColumn('qname', array(
          'header'    => Mage::helper('quiz')->__('Name'),
          'align'     =>'left',
          'index'     => 'qname',
      ));
      $this->addColumn('qage', array(
          'header'    => Mage::helper('quiz')->__('Age'),
          'align'     =>'left',
          'index'     => 'qage',
      ));
      $this->addColumn('qgender', array(
          'header'    => Mage::helper('quiz')->__('Gender'),
          'align'     =>'left',
          'index'     => 'qgender',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('quiz')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('quiz')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('quiz')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('quiz')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('quiz')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('quiz')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('quiz_id');
        $this->getMassactionBlock()->setFormFieldName('quiz');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('quiz')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('quiz')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('quiz/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('quiz')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('quiz')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}