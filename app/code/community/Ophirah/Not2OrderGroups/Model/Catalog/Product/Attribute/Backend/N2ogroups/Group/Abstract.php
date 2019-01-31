<?php

abstract class Ophirah_Not2OrderGroups_Model_Catalog_Product_Attribute_Backend_N2ogroups_Group_Abstract
   extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function validate($object)
    {
        //TODO DO SOME VALIDATION
        return true;
    }

  
    public function afterLoad($object)
    {
        $storeId = 0;
        if(!$this->getAttribute()->isScopeGlobal()) {
            $storeId = $object->getStoreId();
        }
        $data = $this->_getResource()->loadGroupData($object->getId(), $storeId);

        foreach ($data as $k => $v) {
            if ($v['all_groups']) {
                $data[$k]['cust_group'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }
            //only show current storeview
            if($v['website_id'] != $storeId) {
                unset($data[$k]);
            }
        }

        $object->setData($this->getAttribute()->getName(), $data);
        $object->setOrigData($this->getAttribute()->getName(), $data);

        $valueChangedKey = $this->getAttribute()->getName() . '_changed';
        $object->setOrigData($valueChangedKey, 0);
        $object->setData($valueChangedKey, 0);

        return $this;
    }


    public function afterSave($object)
    {
        //get storeview
        $storeId = 0;
        if(!$this->getAttribute()->isScopeGlobal()) { $storeId = $object->getStoreId(); }

        //get old and new data
        $newGroupRows =  $object->getData($this->getAttribute()->getName());
        $oldGroupRows = $object->getOrigData($this->getAttribute()->getName());
        //create buffers
        $deleteGroupException = array();
        $updateRows = array();
        $deleteRows = array();
        $insertRows = array();

        //format newgrouprows and register deleted rows
        foreach($newGroupRows as $newGroupRow) {
            if ($newGroupRow['delete'] == 1) {
                $deleteGroupException[] = $newGroupRow['cust_group'];
            } else {
                $insertRows[$newGroupRow['cust_group']] = $newGroupRow['value'];
            }
        }

        //process new data if changed
        foreach($oldGroupRows as $oldGroupRow){
            //make sure to only process current storeview ( invalid overwrites )
            if($oldGroupRow['website_id'] == $storeId){
                //add to delete que
                if(in_array($oldGroupRow['cust_group'], $deleteGroupException)){
                    $deleteRows[] = $oldGroupRow['value_id'];
                }
                //add to update que
                if(array_key_exists($oldGroupRow['cust_group'], $insertRows)){
                    //check if value is changed
                    if($oldGroupRow['value'] != $insertRows[$oldGroupRow['cust_group']]) {
                        $updateRows[$oldGroupRow['value_id']] = $insertRows[$oldGroupRow['cust_group']];
                    }
                    //updated rows do not have to be added
                    unset($insertRows[$oldGroupRow['cust_group']]);
                }
            }
        } //end process ( foreach loop )

        $productId  = $object->getId();

        //delete group options
        foreach ($deleteRows as $row) { $this->_getResource()->deleteGroupData($row); }
        //insert group options
        foreach ($insertRows as $group => $value) { $this->_getResource()->insertGroupData($productId, $group, $value, $storeId); }
        //update group options
        foreach ($updateRows as $row => $value) { $this->_getResource()->updateGroupData($row, $value); }

        return $this;
    }
    
    protected function _getAdditionalUniqueFields($data){
        return array();
    }
}