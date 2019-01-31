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

/** @var Ophirah_Qquoteadv_Model_Mysql4_Setup $this */
$installer = $this;
$read = $installer->getConnection();
$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
$installer->startSetup();

//if (!$installer->getConnection()->tableColumnExists($installer->getTable('TABLENAME'), 'COLUMNNAME)) {}
// Add edit_increment
$checkIfColumnExistResults = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$installer->getTable('quoteadv_product')}' AND COLUMN_NAME = 'use_discount' and TABLE_SCHEMA = '$dbname' ";
$rows = $read->fetchAll($checkIfColumnExistResults);

if(!count($rows)) {
    $installer->run("
        ALTER TABLE `{$installer->getTable('quoteadv_product')}` ADD `use_discount` char(1) DEFAULT NULL;
    ");
}

$installer->endSetup();