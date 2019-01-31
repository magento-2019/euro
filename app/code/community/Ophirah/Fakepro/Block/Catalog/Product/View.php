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
 * @category    Cart2Quote
 * @package     Fakepro
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (http://www.cart2quote.com)
 * @license     http://www.cart2quote.com/ordering-licenses
 * @version     1.0.5
 */


/**
 */
class Ophirah_Fakepro_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{

    /**
     * Add meta information from product to head block
     * @return Mage_Catalog_Block_Product_View
     * @since 1.0.5
     */
    protected function _prepareLayout()
    {
        // do nothing
    }

    /**
     * Retrieve current product model
     * @return Mage_Catalog_Model_Product
     * @since 1.0.5
     */
    public function getProduct()
    {
        $product = Mage::registry('fake_product');
        if (!$product && $this->getProductId()) {
            $product = Mage::helper('fakepro')->getFakeProduct();
            Mage::register('fake_product', $product);
        }
        return $product;
    }
}
