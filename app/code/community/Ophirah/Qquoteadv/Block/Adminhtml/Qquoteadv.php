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
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (http://www.cart2quote.com)
 * @license     http://www.cart2quote.com/ordering-licenses
 */

class Ophirah_Qquoteadv_Block_Adminhtml_Qquoteadv extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_qquoteadv';
        $this->_blockGroup = 'qquoteadv';
        $this->_headerText = Mage::helper('qquoteadv')->__('Quotations');
        $this->_addButtonLabel = Mage::helper('sales')->__('Create New Quote');
        parent::__construct();

        // Removing top button
        // Button is added to the grid view.
        $this->_removeButton('add');

    }

    /**
     * Get the Magento create order url
     *
     * @return mixed
     */
    public function getCreateUrl()
    {
        return $this->getUrl('adminhtml/sales_order_create/start');
    }
}