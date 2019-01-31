<?php

class Ophirah_Not2OrderGroups_Model_Mysql4_Catalog_Product_Attribute_Backend_N2ogroups_Group_Hideprice
    extends Ophirah_Not2OrderGroups_Model_Mysql4_Catalog_Product_Attribute_Backend_N2ogroups_Group_Abstract //Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('n2ogroups/product_attribute_group_hide_price', 'value_id');
    }

}
