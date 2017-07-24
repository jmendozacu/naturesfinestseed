<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Ophirah
 * @package     Qquoteadv
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

/**
 * Class Ophirah_Qquoteadv_Model_Mysql4_Requestitem_Collection
 */
class Ophirah_Qquoteadv_Model_Mysql4_Requestitem_Collection extends Mage_Sales_Model_Resource_Quote_Item_Collection
{
    /**
     * Construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('qquoteadv/requestitem');
    }

    /**
     * Function to add qty from postdata
     *
     * @param $paramsProduct
     * @param $quoteadvProductId
     * @param $paramsQuoteId
     * @param array $qtys
     * @return $this
     */
    public function addPostDataQty($paramsProduct, $quoteadvProductId, $paramsQuoteId, array $qtys){
        $quoteadvProductId = (int)$quoteadvProductId;
        //Remove the item by quoteadv_product_id before adding again
        Mage::getModel('qquoteadv/requestitem')->getResource()->removeByQuoteadvProductId($quoteadvProductId);

        foreach($qtys as $qty){
            if(!isset($paramsProduct[$quoteadvProductId]) || !isset($paramsProduct[$quoteadvProductId]['product_id'])){
                continue;
            }

            if (!empty($qty)){
                $this->_addItem(
                    Mage::getModel('qquoteadv/requestitem')
                        ->setRequestQty($qty)
                        ->setQuoteadvProductId($quoteadvProductId)
                        ->setQuoteId($paramsQuoteId)
                        ->setProductId($paramsProduct[$quoteadvProductId]['product_id'])
                );
            }
        }
        $this->_setIsLoaded(true);
        $this->save();
        return $this;
    }

    /**
     * Overwrite for _assignOptions
     *
     * @return $this
     */
    protected function _assignOptions()
    {
        foreach ($this as $item) {
            $item->setOptions(array());
        }
        return $this;

    }

    /**
     * Overwrite for _afterLoad
     */
    protected function _afterLoad()
    {
        //do nothing
    }
}
