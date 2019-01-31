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

class Ophirah_Qquoteadv_Model_System_Config_Source_Scope
{
    /**
     * @var
     */
    protected $_options;

    /**
     * Returns the available options for the scope selection
     *
     * @param bool|false $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('Default Config')),
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Website')),
            array('value' => 2, 'label' => Mage::helper('qquoteadv')->__('Store'))
        );
    }
}
