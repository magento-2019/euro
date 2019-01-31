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
 * Class Ophirah_Fakepro_Block_Quote_Product
 * @template fakepro/quote/product.phtml
 */
class Ophirah_Fakepro_Block_Quote_Product extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Special fake product type
     * @var string
     */
    const FAKE_PRODUCT_TYPE = 'fake_product';

    /**
     * Get item row html
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  string
     * @since 1.0.5
     */
    public function getItemHtml(Mage_Sales_Model_Quote_Item $item)
    {
        $renderer = $this->getItemRenderer(self::FAKE_PRODUCT_TYPE)->setItem($item);
        return $renderer->toHtml();
    }

    public function getProductImage(){
        $productInformation = $this->getProductInformation();
        $file = $productInformation->getFullpath();
        $ioAdapter = new Varien_Io_File();
        if (!$ioAdapter->fileExists($file)) {
            Mage::throwException(Mage::helper('core')->__('File not found'));
        }
        $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
        $ioAdapter->streamOpen($file, 'r');
        while ($buffer = $ioAdapter->streamRead()) {
            print $buffer;
        }
        $ioAdapter->streamClose();
        if (!empty($content['rm'])) {
            $ioAdapter->rm($file);
        }
    }

    public function getProductInformation(){
        $productInformation = Mage::helper('fakepro')->getProductOptionValues($this->getItem()->getAttribute());
        return $productInformation;
    }
}