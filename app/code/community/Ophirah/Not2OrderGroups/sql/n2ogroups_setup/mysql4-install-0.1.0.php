<?php


$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('n2ogroups/product_attribute_group_allow_order')}");
$installer->run("DROP TABLE IF EXISTS {$installer->getTable('n2ogroups/product_attribute_group_hide_price')}");

$table = $installer->getConnection()
    ->newTable($installer->getTable('n2ogroups/product_attribute_group_allow_order'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('all_groups', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Applicable To All Customer Groups')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Group ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
         'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Value')
        ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',    
        ), 'Website ID')
    ->addIndex($installer->getIdxName('n2ogroups/product_attribute_group_allow_order', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('n2ogroups/product_attribute_group_allow_order', array('customer_group_id')),
        array('customer_group_id'))
     ->addIndex($installer->getIdxName('n2ogroups/product_attribute_group_allow_order', array('website_id')),
        array('website_id'))
   

    ->setComment('Catalog Product Allow Order Backend Table');


$installer->getConnection()->createTable($table);


$table = $installer->getConnection()
    ->newTable($installer->getTable('n2ogroups/product_attribute_group_hide_price'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('all_groups', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Applicable To All Customer Groups')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Group ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
         'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Value')
        ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',    
        ), 'Website ID')
    ->addIndex($installer->getIdxName('n2ogroups/product_attribute_group_hide_price', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('n2ogroups/product_attribute_group_hide_price', array('customer_group_id')),
        array('customer_group_id'))
     ->addIndex($installer->getIdxName('n2ogroups/product_attribute_group_hide_price', array('website_id')),
        array('website_id'))
   

    ->setComment('Catalog Product Group Hide Price Backend Table');


$installer->getConnection()->createTable($table);

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'group_hide_price', array(
        'group'     	=> 'Prices',
	'input'         => 'text',
        'type'          => 'int',	
        'label'         => Mage::helper('n2ogroups')->__('Hide price for groups'),
	'backend'       => 'n2ogroups/catalog_product_attribute_backend_n2ogroups_group_hideprice',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'       => true,
	'required'	=> false,	
	'default_value' => '0'
));

$setup->addAttribute('catalog_product', 'group_allow_order', array(
        'group'     	=> 'General',
	'input'         => 'text',
        'type'          => 'int',	
        'label'         => Mage::helper('n2ogroups')->__('Enable Orders'),
	'backend'       => 'n2ogroups/catalog_product_attribute_backend_n2ogroups_group_alloworder',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'       => true,
	'required'	=> false,	
	'default_value' => '0'
));

 
$installer->endSetup();