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

$newConfigPaths = array();
$newConfigPaths["qquoteadv_quote_frontend/catalog/redirect_to_quotation"] = "qquoteadv_advanced_settings/frontend/redirect_to_quotation";

/** @var Ophirah_Qquoteadv_Model_Mysql4_Setup $this */
$installer = $this;
$installer->startSetup();

foreach ($newConfigPaths as $oldPath => $newPath) {
    $installer->run("UPDATE {$installer->getTable('core_config_data')} SET `path` = REPLACE(`path`, '".$oldPath."', '".$newPath."') WHERE `path` = '".$oldPath."'");
}

$installer->endSetup();

