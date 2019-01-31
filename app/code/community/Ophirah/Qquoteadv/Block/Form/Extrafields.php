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

class Ophirah_Qquoteadv_Block_Form_Extrafields extends Ophirah_Qquoteadv_Block_Qquoteaddress
{
    const PATH_TO_EXTRA_FIELD_CONFIG     = "qquoteadv_quote_form_builder/quote_form_customization/extrafield_";
    const LABEL_TAG     = "_label";
    const REQUIRED      = 2;

    /**
     * Checks if extra fields are available
     * @return bool
     */
    public function extraFieldsAreAvailable(){
        for($fieldNumber = 1; $fieldNumber <= Mage::helper('qquoteadv')->getNumberOfExtraFields(); $fieldNumber++){
            if($this->isExtraFieldSet($fieldNumber)){
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if a specific extra field is set
     * @param $fieldId
     * @return bool
     */
    public function isExtraFieldSet($fieldId)
    {
        if(Mage::getStoreConfig( self::PATH_TO_EXTRA_FIELD_CONFIG . $fieldId )){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Retrieves an label of a specific extra field
     * @param $fieldId
     * @return bool
     */
    public function getFieldLabel($fieldId)
    {
        if(Mage::getStoreConfig( self::PATH_TO_EXTRA_FIELD_CONFIG . $fieldId . self::LABEL_TAG )){
            return Mage::getStoreConfig( self::PATH_TO_EXTRA_FIELD_CONFIG . $fieldId . self::LABEL_TAG );
        } else{
            return false;
        }
    }

    /**
     * Checks if a specific extra field is required
     * @param $fieldId
     * @return bool
     */
    public function isRequiredField($fieldId){
        if(Mage::getStoreConfig( self::PATH_TO_EXTRA_FIELD_CONFIG . $fieldId ) == self::REQUIRED){
            return true;
        } else{
            return false;
        }
    }

    /**
     * Retrieves the row class depending on the count
     * @param $fieldCount
     * @return string
     */
    public function getRowClass($fieldCount){
        if($fieldCount % 2 == 0){
            return "p5";
        }else{
            return "left";
        }
    }

    /**
     * Retrieves the row class depending on the count
     * @param $fieldCount
     * @return string
     */
    public function getRowStart($fieldCount){
        if($fieldCount % 2 != 0){
            return "<tr>";
        }else{
            return "";
        }
    }

    /**
     * Retrieves the row class depending on the count
     * @param $fieldCount
     * @return string
     */
    public function getRowEnd($fieldCount){
        if($fieldCount % 2 == 0){
            return "</tr>";
        }else{
            return "";
        }
    }

    /**
     * Retrieves the field data of a specific extra field
     * @param $fieldId
     * @param bool $error
     * @return string
     */
    public function getFieldData($fieldId, $error = true){
        $quote = $this->getParentBlock()->getQuoteData();
        $fieldData = $quote->getData('extra_field_'.$fieldId);
        if($error && is_null($fieldData)){
            $fieldData = "None";
        }
        return $fieldData;
    }

}
