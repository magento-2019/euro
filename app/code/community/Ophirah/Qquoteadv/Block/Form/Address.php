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

class Ophirah_Qquoteadv_Block_Form_Address extends Ophirah_Qquoteadv_Block_Qquoteaddress
{
    const SHIPPING = 'shipping';
    const BILLING = 'billing';

    /**
     * Return the form type, if addresstype isn't set, shipping is assumed
     *
     * @return string
     */
    public function getFormType(){
        $type = $this->getAddressType();
        if(empty($type)){
            $type = self::SHIPPING;
        }
        return $type;
    }

    /**
     * Return the main div id based on the from type
     *
     * @return string
     */
    public function getMainDiv(){
        if($this->getFormType() == self::SHIPPING){
            return 'shipDiv';
        }else{
            return 'billDiv';
        }
    }

    /**
     * Return the address description based on the form type
     *
     * @return string
     */
    public function getAddressDescription(){
        if($this->getFormType() == self::SHIPPING){
            $shippingDescription = Mage::helper('qquoteadv')->__('Enter your destination to receive a shipping quotation. Unless otherwise specified, standard shipping is quoted.');
            return $shippingDescription;
        }else{
            $billingDescription = Mage::helper('qquoteadv')->__('Enter your billing address details.');
            return $billingDescription;
        }
    }

    /**
     * Get the addres filed name based on the form type
     *
     * @return string
     */
    public function getAddedFieldName(){
        if($this->getFormType() == self::SHIPPING){
            return 'shipping';
        }else{
            return 'billing';
        }
    }

    /**
     * Return the required html for the given setting
     *
     * @param $setting
     * @return string
     */
    public function getFieldRequiredSpan($setting){
        if($setting == 2){
            //return '<span class="required">*</span>';
            return '<span class="required"></span>';
        }else{
            return '';
        }
    }

    /**
     * Return the required css class for the given setting
     *
     * @param $setting
     * @return string
     */
    public function getFieldRequiredClass($setting){
        if($setting == 2){
            return 'required-entry';
        }else{
            return '';
        }
    }

    /**
     * Get the required address based on the form type
     *
     * @return mixed
     */
    public function getRequiredAddress(){
        if($this->getFormType() == self::SHIPPING){
            return $this->getParentBlock()->getRequiredShipping();
        }
        if($this->getFormType() == self::BILLING){
            return $this->getParentBlock()->getRequiredBilling();
        }
    }

    /**
     * Get the display status of the main div based in the settings
     *
     * @return string
     */
    public function getMainDivAllowToShow(){
        $parent = $this->getParentBlock();
        $html = '';
        if($this->getFormType() == self::SHIPPING){
            if (!$parent->getRequiredShipping() ||
                ($parent->isCustomerLoggedIn() && !$parent->getRequiredShipping() && !$parent->getRequiredBilling())){
                $html = 'display: none';
            }
        }else{
            if (!$parent->getRequiredBilling() || ($this->isCustomerLoggedIn() && !$parent->getRequiredBilling())){
                $html = 'display: none';
            }
        }
        return $html;
    }

    /**
     * Check if an address is available for the current customer
     *
     * @return bool
     */
    public function isAddressAvailible(){
        $customer = $this->getCustomer();
        if(count($customer->getAddresses()) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get a list of addresses for the current customer
     *
     * @return string
     */
    public function getListOfAddresses(){
        $parent = $this->getParentBlock();
        if($parent->isCustomerLoggedIn()) {
            $customer = Mage::helper('customer')->getCustomer();
            $primaryAddressesIds = $customer->getPrimaryAddressIds();
            $qquoteadv = $parent->getCustomerSession()->getData('quoteCustomer');
            if($qquoteadv) {
                $selectedId = $qquoteadv->getData($this->getAddedFieldName() . 'mage_address_id');
            }else{
                $selectedId = '';
            }
            $html =
                '<select
            id="'.$this->getAddedFieldName() .':mage_address_id"
            name="' . $this->getAddedFieldName() . '[mage_address_id]"
            style="width: 100%;"
            onload="showNewAddress(this, \'' . $this->getFormType() . '\')"
            onchange="showNewAddress(this, \'' . $this->getFormType() . '\')">';

            foreach ($customer->getAddresses() as $address) {
                if ($address->getId() == $selectedId) {
                    $selected = 'selected';
                } else {
                    $selected = '0';
                }
                if($address->hasData('street')){
                    $html .= '<option '.$this->_getPrimaryAddressId($primaryAddressesIds, $address->getId()).' value="' . $address->getId() . '" ' . $selected . '>';
                    $html .= $this->_getPrimaryAddressText($primaryAddressesIds, $address->getId()).$address->format('html');
                    $html .= '</option>';
                }
            }
            if ($selectedId == 'new') {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $html .= '<option ' . $selected . ' value="new">';
            $html .= Mage::helper('sales')->__('Add new address');
            $html .= '</option>';

            $html .= '</select>';
            return $html;
        }
        $html = '';
        return $html;
    }

    /**
     * Get the html id of the primary address based on the available addresses and the given target address id
     *
     * @param $primaryAddressesIds
     * @param $addressId
     * @return string
     */
    private function _getPrimaryAddressId($primaryAddressesIds, $addressId){
        if(is_array($primaryAddressesIds)){
            if($primaryAddressesIds[0] == $primaryAddressesIds[1] && $primaryAddressesIds[0] == $addressId) {
                return 'id="'.$this->getAddedFieldName().'option_billing&shipping"';
            }elseif($primaryAddressesIds[0] == $addressId){
                return 'id="'.$this->getAddedFieldName().'option_billing"';
            }elseif($primaryAddressesIds[1] == $addressId){
                return 'id="'.$this->getAddedFieldName().'option_shipping"';
            }else{
                return '';
            }
        }
    }

    /**
     * Get the html text of the primary address based on the available addresses and the given target address id
     *
     * @param $primaryAddressesIds
     * @param $addressId
     * @return string
     */
    private function _getPrimaryAddressText($primaryAddressesIds, $addressId){
        if(is_array($primaryAddressesIds)){
            if($primaryAddressesIds[0] == $primaryAddressesIds[1] && $primaryAddressesIds[0] == $addressId) {
                return '['.Mage::helper('qquoteadv')->__('Default Billing & Shipping').']  - ';
            }elseif($primaryAddressesIds[0] == $addressId){
                return '['.Mage::helper('checkout')->__('Default Billing').']  - ';
            }elseif($primaryAddressesIds[1] == $addressId){
                return '['.Mage::helper('checkout')->__('Default Shipping').']  - ';
            }else{
                return '';
            }
        }
    }

}
