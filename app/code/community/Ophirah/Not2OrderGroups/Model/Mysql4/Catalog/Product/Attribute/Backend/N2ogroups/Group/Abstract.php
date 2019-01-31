<?php
abstract class Ophirah_Not2OrderGroups_Model_Mysql4_Catalog_Product_Attribute_Backend_N2ogroups_Group_Abstract
    extends Mage_Core_Model_Resource_Db_Abstract
{

    public function loadGroupData($productId, $websiteId = null)
    {
        $adapter = $this->_getReadAdapter();

        $columns = array(
            'value_id'      => $this->getIdFieldName(),
            'website_id'    => 'website_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'value'         => 'value',
        );

        $select  = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $productId);

        if (!is_null($websiteId)) {
            $select->where('website_id = ?', $websiteId);
        }
        
        return $adapter->fetchAll($select);
    }


    public function deleteGroupData($id)
    {
        $adapter = $this->_getWriteAdapter();

        $where   = array(
            $adapter->quoteInto('value_id = ?', $id)
        );

        return $adapter->delete($this->getMainTable(), $where);
    }


    public function insertGroupData($entity, $group, $value, $store, $all = 0)
    {
        $adapter = $this->_getWriteAdapter();

        $conditions = array(
            'entity_id' => $entity,
            'all_groups' => $all,
            'customer_group_id' => $group,
            'value' => $value,
            'website_id' => $store
        );

        return $adapter->insert($this->getMainTable(), $conditions);
    }

    public function updateGroupData($id, $value)
    {
        $adapter = $this->_getWriteAdapter();

        $data = array('value' => $value);
        $where   = array($adapter->quoteInto('value_id = ?', $id));

        return $adapter->update($this->getMainTable(), $data, $where);
    }

}
