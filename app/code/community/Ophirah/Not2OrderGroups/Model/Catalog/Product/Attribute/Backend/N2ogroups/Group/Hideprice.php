<?php

class Ophirah_Not2OrderGroups_Model_Catalog_Product_Attribute_Backend_N2ogroups_Group_Hideprice
    extends Ophirah_Not2OrderGroups_Model_Catalog_Product_Attribute_Backend_N2ogroups_Group_Abstract
{
    protected function _getResource()
    {
        return Mage::getResourceSingleton('n2ogroups/catalog_product_attribute_backend_n2ogroups_group_hideprice');
    }
}
