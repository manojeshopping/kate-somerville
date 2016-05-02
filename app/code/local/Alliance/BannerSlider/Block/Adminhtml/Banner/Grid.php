<?php

/**
 * Class Alliance_BannerSlider_Block_Adminhtml_Banner_Grid
 */
class Alliance_BannerSlider_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('alliance_bannerslider_banner_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'alliance_bannerslider/banner_collection';
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
                'header'=> $this->__('Banner ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id',
            )
        );

        $this->addColumn('image', array(
            'header'    => Mage::helper('alliance_bannerslider')->__('Banner Image'),
            'align'     => 'left',
            'width'     => '100px',
            'index'     => 'image',
            'type'      => 'image',
            'escape'    => true,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => new Alliance_BannerSlider_Block_Adminhtml_Banner_Grid_Renderer_Image,
        ));

        $this->addColumn('title',
            array(
                'header'=> $this->__('Banner Title / Alt Text'),
                'index' => 'title',
            )
        );

        $this->addColumn('link',
            array(
                'header'=> $this->__('Banner Link'),
                'index' => 'link',
            )
        );

        $this->addColumn('new_tab',
            array(
                'header'=> $this->__('Open In New Tab'),
                'index' => 'new_tab',
                'width' => '50px',
            )
        );

        $this->addColumn('store_code',
            array(
                'header'=> $this->__('Store Code'),
                'index' => 'store_code',
                'width' => '50px',
            )
        );

        $this->addColumn('sort_order',
            array(
                'header'=> $this->__('Sort Order'),
                'index' => 'sort_order',
                'width' => '50px',
            )
        );

        $this->addColumn('status',
            array(
                'header'=> $this->__('Status'),
                'index' => 'status',
                'width' => '50px',
            )
        );

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
}