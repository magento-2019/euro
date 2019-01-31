<?php
class Ophirah_Not2OrderGroups_Model_Observer
{
    private $isEnabled = null;

   function addAdminGroupForms($observer){
        $form = $observer->getEvent()->getForm();
        $groupAllow = $form->getElement('group_allow_order');
        if ($groupAllow) {
            $groupAllow->setRenderer(
                Mage::getSingleton('core/layout')->createBlock('n2ogroups/adminhtml_catalog_product_edit_tab_n2ogroups_group_alloworder')
            );
        }
        
        $groupHidePrice = $form->getElement('group_hide_price');
        if ($groupHidePrice) {
            $groupHidePrice->setRenderer(
                Mage::getSingleton('core/layout')->createBlock('n2ogroups/adminhtml_catalog_product_edit_tab_n2ogroups_group_hideprice')
            );
        }
    }

    public function setAllowedToOrdermode( $observer ) {
        if(!Mage::app()->getStore()->isAdmin() && Mage::getDesign()->getArea() != 'adminhtml' && $this->isEnabled()) {

            $product = $observer->getEvent()->getProduct();
            $storeId = Mage::app()->getStore()->getStoreId();

            $groups = Mage::getModel('n2ogroups_mysql4/catalog_product_attribute_backend_n2ogroups_group_alloworder');
            $allowOrderGroups = $groups->loadGroupData($product->getId(), $storeId);
            //if no options are set on storeview level get default settings
            if(count($allowOrderGroups) < 1 && $storeId != 0){
                $allowOrderGroups = $groups->loadGroupData($product->getId(), 0);
            }

            $allowed = $product->getAllowedToOrdermode();
            if(is_array($allowOrderGroups)){
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                foreach($allowOrderGroups as $allow) {
                    if($customerGroupId == (int)$allow['cust_group'] && $allowed != $allow['value']){
                        $allowed = (int)$allow['value'];
                    }
                }
            }

            if (!is_null($allowed)) {
                $product->setAllowedToOrdermode($allowed);
            }
        }
    }
    
    public function setHidePrice($observer){
        if(!Mage::app()->getStore()->isAdmin() && Mage::getDesign()->getArea() != 'adminhtml' && $this->isEnabled()) {

            $product = $observer->getEvent()->getProduct();
            $storeId = Mage::app()->getStore()->getStoreId();

            $groups = Mage::getModel('n2ogroups_mysql4/catalog_product_attribute_backend_n2ogroups_group_hideprice');
            $hidePriceGroups = $groups->loadGroupData($product->getId(), $storeId);
            //if no options are set on storeview level get default settings
            if(count($hidePriceGroups) < 1 && $storeId != 0){
                $hidePriceGroups = $groups->loadGroupData($product->getId(), 0);
            }

            $hidePrice = $product->getHidePrice();

            if(is_array($hidePriceGroups)){
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                foreach($hidePriceGroups as $hide) {
                    if($customerGroupId == (int)$hide['cust_group'] && $hidePrice != $hide['value']){
                        $hidePrice = (int)$hide['value'];
                    }
                }
            }

            if (!is_null($hidePrice)) {
                $product->setHidePrice($hidePrice);
            }
        }
    }

    private function isEnabled(){
        if(is_null($this->isEnabled)){
            $this->isEnabled = Mage::getStoreConfig('qquoteadv_general/orderability_and_prices/enabled');
        }
        return $this->isEnabled;
    }
}