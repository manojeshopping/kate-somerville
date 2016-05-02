<?php

/**
 * Class Alliance_GlobalBanner_Block_Adminhtml_Banner_Grid
 */
class Alliance_GlobalBanner_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('alliance_globalbanner_banner_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'alliance_globalbanner/banner_collection';
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header' => $this->__('Banner ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'id',
            )
        );

        $this->addColumn('image', array(
            'header'   => Mage::helper('alliance_globalbanner')->__('Banner Image'),
            'align'    => 'left',
            'width'    => '100px',
            'index'    => 'image',
            'type'     => 'image',
            'escape'   => true,
            'sortable' => false,
            'filter'   => false,
            'renderer' => new Alliance_GlobalBanner_Block_Adminhtml_Banner_Grid_Renderer_Image,
        ));

        $this->addColumn('image_link',
            array(
                'header' => $this->__('Banner Link'),
                'index'  => 'image_link',
            )
        );

        $this->addColumn('image_alt',
            array(
                'header' => $this->__('Banner Alt Text'),
                'index'  => 'image_alt',
            )
        );

        $this->addColumn('priority',
            array(
                'header' => $this->__('Priority'),
                'index'  => 'priority',
                'width'  => '50px',
            )
        );

        $this->addColumn('status',
            array(
                'header' => $this->__('Status'),
                'index'  => 'status',
                'width'  => '50px',
            )
        );

        $this->addColumn('from_date', array(
            'header' => Mage::helper('alliance_globalbanner')->__('Start Date'),
            'align'  => 'left',
            'width'  => '120px',
            'type'   => 'date',
            'index'  => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'  => Mage::helper('alliance_globalbanner')->__('End Date'),
            'align'   => 'left',
            'width'   => '120px',
            'type'    => 'date',
            'default' => '--',
            'index'   => 'to_date',
        ));

        $this->addColumn('stores', array(
            'header'                    => Mage::helper('alliance_globalbanner')->__('Store Views'),
            'index'                     => 'stores',
            'renderer'                  => 'alliance_globalbanner/adminhtml_banner_grid_renderer_stores',
            'sortable'                  => false,
            'type'                      => 'store',
            'filter_condition_callback' => array($this, '_filterStoreCondition'),
        ));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        foreach ($collection as $key => $banner) {
            if (!in_array($value, explode(',', $banner->getStores()))) {
                $collection->removeItemByKey($key);
            }
        }

        $this->setCollection($collection);
    }
}
