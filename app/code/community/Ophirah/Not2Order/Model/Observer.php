<?php
class Ophirah_Not2Order_Model_Observer {

    public function setSaleableState( $observer ) {
        $hideOrderButtonIfOutOfStockWhileBackorderIsEnabled = true;


        $product = Mage::getModel('catalog/product')->load( $observer->getEvent()->getProduct()->getId() );

        $enabled = Mage::getStoreConfig('qquoteadv_general/orderability_and_prices/enabled');

        if($enabled == '1'){
            if ($product->getAllowedToOrdermode() == 0) {
                $observer->getEvent()->getData('salable')->setData('is_salable', false);
            } else {
                if($hideOrderButtonIfOutOfStockWhileBackorderIsEnabled){

                }
            }
        }


    }
    
    public function removeFromCart( $observer ) {
        $product = $observer->getProduct(); 
        $quoteItem = $observer->getQuoteItem(); 
        if(!$product->isSaleable()){
        //remove item from quote 
            if ($quoteItem->getParentItem() == NULL) { 
                $quoteItem->getQuote()->removeItem($quoteItem->getId()); 
            }
             Mage::throwException(Mage::helper('not2order')->__('You can not add %s to your cart', $product->getName())); 
        }
    }
}