<?php

class Mango_Categorytabs_Block_Adminhtml_Categorytabs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('categorytabsGrid');
      $this->setDefaultSort('attribute_group_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      //$collection = Mage::getModel('eav/entity_attribute_group')->getCollection();
      
      $collection = Mage::getModel('categorytabs/eav_entity_attribute_group')->getCollection();
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      /*$this->addColumn('attribute_group_id', array(
          'header'    => Mage::helper('categorytabs')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'attribute_group_id',
      ));*/

      $this->addColumn('attribute_group_name', array(
          'header'    => Mage::helper('categorytabs')->__('Title'),
          'align'     =>'left',
          'index'     => 'attribute_group_name',
      ));
      
      $this->addColumn('sort_order', array(
          'header'    => Mage::helper('categorytabs')->__('Position'),
          'align'     =>'left',
          'index'     => 'sort_order',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('categorytabs')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      /*$this->addColumn('status', array(
          'header'    => Mage::helper('categorytabs')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('categorytabs')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('categorytabs')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		//$this->addExportType('*/*/exportCsv', Mage::helper('categorytabs')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('categorytabs')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}