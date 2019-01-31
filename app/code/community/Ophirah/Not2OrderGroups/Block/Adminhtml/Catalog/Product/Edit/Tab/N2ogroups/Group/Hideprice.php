<?php
class Ophirah_Not2OrderGroups_Block_Adminhtml_Catalog_Product_Edit_Tab_N2ogroups_Group_Hideprice
    extends Ophirah_Not2OrderGroups_Block_Adminhtml_Catalog_Product_Edit_Tab_N2ogroups_Group_Abstract    
{
    /**
     * Initialize block
     */
    public function __construct()
    {
       $this->setTemplate('not2ordergroups/catalog/product/edit/group.phtml');
       $this->setGroupName("hidePrice");
    }
}