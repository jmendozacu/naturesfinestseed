<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.21
 * @build     712
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



/**
 * Class Mirasvit_Advr_Block_Adminhtml_Order_Plain.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Mirasvit_Advr_Block_Adminhtml_Order_Plain extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Orders'));

        return $this;
    }

    protected function prepareChart()
    {
        $this->setChartType('column');

        $this->initChart()
            ->setXAxisType('order')
            ->setXAxisField('increment_id');

        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('base_grand_total')
            ->setDefaultDir('desc')
            ->setPagerVisibility(true)
            ->setRowUrlCallback(array($this, 'rowUrlCallback'));

        return $this;
    }

    public function _prepareCollection()
    {
        $columns = $this->getColumns();
        $filterData = clone $this->getFilterData();
        $collection = Mage::getModel('advr/report_sales')->setBaseTable('sales/order', false);
        $tableDescription = $collection->getConnection()->describeTable($collection->getTable('sales/order'));

        // Select order ID, used for filtering collection and loading orders
        $collection->getSelect()->columns(array('entity_id'));
        $this->applyFilter($collection);

        // Add every report column to collection
        foreach ($this->getVisibleColumns() as $column) {
            $data = $columns[$column];
            if (isset($tableDescription[$column])) {
                $data['expression'] = 'sales_order_table.'.$column;
                $data['table'] = 'sales/order';
                if(isset($data['type']) &&
                    $data['type'] == 'currency' &&
                    strpos($column, 'base') !== false) {
                    // please look at Mirasvit_Advr_Model_Report_Abstract::getExpression()
                    $data['expression'] = '(' . $data['expression'] . ')';
                }
            }
            $data['label'] = $data['header'];
            $collection->addColumn($column, $data);
        }

        $collection->setFilterData($filterData->unsOrders(), false, false) // Unset extra data from filters
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('increment_id');

        $this->setCollection($collection);

        return $collection;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     *
     * @return array
     */
    public function getColumns()
    {
        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->load()
            ->toOptionHash();

        $paymentMethods = Mage::getSingleton('payment/config')->getActiveMethods();
        $paymentMethodOptions = array();

        foreach (array_keys($paymentMethods) as $paymentCode) {
            $paymentMethodOptions[$paymentCode] = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
        }

        $columns = array(
            'increment_id' => array(
                'header' => Mage::helper('advr')->__('Order #'),
                'totals_label' => Mage::helper('advr')->__('Totals'),
            ),

            'invoice_id' => array(
                'header' => Mage::helper('advr')->__('Invoice #'),
                'sortable' => false,
                'filter' => false,
                'frame_callback' => array($this, 'invoice'),
                'export_callback' => array($this, 'invoice'),
                'hidden' => true,
            ),

            'customer_firstname' => array(
                'header' => Mage::helper('advr')->__('Firstname'),
                'column_css_class' => 'nobr',
            ),

            'customer_lastname' => array(
                'header' => Mage::helper('advr')->__('Lastname'),
                'column_css_class' => 'nobr',
            ),

            'customer_email' => array(
                'header' => Mage::helper('advr')->__('Email'),
                'column_css_class' => 'nobr',
            ),

            'customer_group_id' => array(
                'header' => Mage::helper('advr')->__('Customer Group'),
                'type' => 'options',
                'options' => $groups,
                'column_css_class' => 'nobr',
            ),

            'customer_taxvat' => array(
                'header' => Mage::helper('advr')->__('Tax/VAT number'),
                'hidden' => true,
            ),

            'created_at' => array(
                'header' => Mage::helper('advr')->__('Purchased On'),
                'type' => 'datetime',
                'column_css_class' => 'nobr',
                'export_callback' => array($this, 'createdAt'),
            ),

            'state' => array(
                'header' => Mage::helper('advr')->__('State'),
                'type' => 'options',
                'options' => Mage::getSingleton('sales/order_config')->getStates(),
                'hidden' => true,
            ),

            'status' => array(
                'header' => Mage::helper('advr')->__('Status'),
                'type' => 'options',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            ),

            'products' => array(
                'header' => Mage::helper('advr')->__('Item(s)'),
                'sortable' => false,
                'filter' => false,
                'frame_callback' => array($this, 'products'),
                'export_callback' => array($this, 'products'),
                'hidden' => true,
            ),

            'tracking_number' => array(
                'header' => Mage::helper('advr')->__('Tracking Number'),
                'sortable' => false,
                'filter' => false,
                'frame_callback' => array($this, 'trackingNumber'),
                'export_callback' => array($this, 'trackingNumber'),
                'hidden' => true,
            ),

            'payment_method' => array(
                'type' => 'options',
                'header' => Mage::helper('advr')->__('Payment Type'),
                'hidden' => true,
                'options' => $paymentMethodOptions,
                'expression' => 'sales_order_payment_table.method',
                'table' => 'sales/order_payment',
            ),

            'total_qty_ordered' => array(
                'header' => Mage::helper('advr')->__('Quantity Ordered'),
                'type' => 'number',
            ),

            'base_tax_amount' => array(
                'header' => Mage::helper('advr')->__('Tax'),
                'type' => 'currency',
                'hidden' => true,
            ),

            'base_shipping_amount' => array(
                'header' => Mage::helper('advr')->__('Shipping'),
                'type' => 'currency',
                'hidden' => true,
            ),

            'base_discount_amount' => array(
                'header' => Mage::helper('advr')->__('Discount'),
                'type' => 'currency',
            ),

            'base_total_refunded' => array(
                'header' => Mage::helper('advr')->__('Refunded'),
                'type' => 'currency',
            ),

            'base_total_paid' => array(
                'header' => Mage::helper('advr')->__('Paid'),
                'type' => 'currency',
                'hidden' => true,
            ),

            'base_total_invoiced' => array(
                'header' => Mage::helper('advr')->__('Total Invoiced'),
                'type' => 'currency',
                'hidden' => true,
            ),

            'base_grand_total' => array(
                'header' => Mage::helper('advr')->__('Grand Total'),
                'type' => 'currency',
                'chart' => true,
            ),

            'gross_profit' => array(
                'header' => Mage::helper('advr')->__('Gross Profit'),
                'type' => 'currency',
                'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
                'expression' => '(sales_order_table.base_subtotal_invoiced - sales_order_table.base_total_invoiced_cost)',
                'discount_from' => 'base_grand_total',
                'table' => 'sales_order_table',
                'chart' => false,
            ),
        );

        $columns['actions'] = array(
            'header' => 'Actions',
            'hidden' => true,
            'actions' => array(
                array(
                    'caption' => Mage::helper('advr')->__('View'),
                    'callback' => array($this, 'rowUrlCallback'),
                ),
            ),
        );

        return $columns;
    }

    public function createdAt($value, $row, $column)
    {
        $data = Mage::app()->getLocale()
            ->date($row->getCreatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT)->toString();
        return $data;
    }

    private function applyFilter($collection)
    {
        if ($this->getFilterData()->getOrders()) {
            $collection->addFieldToFilter('sales_order_table.entity_id', array('in' => explode(',', ($this->getFilterData()->getOrders()))));
        }
    }

    public function rowUrlCallback($row)
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getEntityId()));
    }

    public function invoice($value, $row, $column)
    {
        $adapter = Mage::getSingleton('core/resource');
        $read = $adapter->getConnection('core_read');
        $select = 'SELECT GROUP_CONCAT(increment_id) as increment_id FROM '.$adapter->getTableName('sales/invoice').' WHERE order_id = '.$row->getEntityId();

        return $read->fetchOne($select);
    }

    public function products($value, $row, $column)
    {
        $data = array();
        $row = Mage::getModel('sales/order')->load($row->getEntityId());
        $collection = $row->getAllVisibleItems();
        foreach ($collection as $item) {
            $url = $this->getUrl('adminhtml/catalog_product/edit', array('id' => $item->getProductId()));
            $data[] = '<a class="nobr" target="_blank" href="'.$url.'">'
                .$item->getSku()
                .' / '
                .Mage::helper('core/string')->truncate($item->getName(), 50)
                .' / '.intval($item->getQtyOrdered())
                .' × '.Mage::helper('core')->currency($item->getBasePrice())
                .'</a>';
        }

        return implode('<br>', $data);
    }

    public function trackingNumber($value, $row, $column)
    {
        $trackNumbers = array();

        $row = Mage::getModel('sales/order')->load($row->getEntityId());
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->setOrderFilter($row);

        foreach ($shipmentCollection as $shipment) {
            foreach ($shipment->getAllTracks() as $trackNumber) {
                $trackNumbers[] = $trackNumber->getNumber();
            }
        }

        return implode('<br>', $trackNumbers);
    }

    public function getFilterColumns()
    {
        // Restrict columns available only for this report
        return array_intersect_key($this->getCollection()->getColumns(), $this->getColumns());
    }
}
