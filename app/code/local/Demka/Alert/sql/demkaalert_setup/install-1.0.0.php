<?php

$installer = $this;
$tableAlertPrice = $installer->getTable('demkaalert/table_alert_price');
$tableAlertStock = $installer->getTable('demkaalert/table_alert_stock');

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

/**
 * Create table 'demkaalert/table_alert_price'
 */
$installer->getConnection()->dropTable($tableAlertPrice);
$table = $installer->getConnection()
    ->newTable($tableAlertPrice)
    ->addColumn('alert_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Product alert price id')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, '250', array(
        'nullable' => false,
    ), 'Anonymous Customer Email')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
    ), 'Product id')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable' => false,
        'default' => '0.0000',
    ), 'Price amount')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
    ), 'Website id')
    ->addColumn('add_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Product alert add date')
    ->addColumn('last_send_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Product alert last send date')
    ->addColumn('send_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
    ), 'Product alert send count')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
    ), 'Product alert status')
    ->addIndex($installer->getIdxName($tableAlertPrice, array('customer_email', 'product_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('customer_email', 'product_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName($tableAlertPrice, array('product_id')),
        array('product_id'))
    ->addIndex($installer->getIdxName($tableAlertPrice, array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName($tableAlertPrice, 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName($tableAlertPrice, 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Demka_Product Alert Price_For_Anonymous');

$installer->getConnection()->createTable($table);

/**
 * Create table 'demkaalert/table_alert_stock'
 */
$installer->getConnection()->dropTable($tableAlertStock);
$table = $installer->getConnection()
    ->newTable($installer->getTable($tableAlertStock))
    ->addColumn('alert_stock_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Product alert stock id')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, '250', array(
        'nullable' => false,
    ), 'Anonymous Customer Email')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Product id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Website id')
    ->addColumn('add_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Product alert add date')
    ->addColumn('send_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Product alert send date')
    ->addColumn('send_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Send Count')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Product alert status')
    ->addIndex($installer->getIdxName($tableAlertStock, array('customer_email', 'product_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('customer_email', 'product_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName($tableAlertStock, array('product_id')),
        array('product_id'))
    ->addIndex($installer->getIdxName($tableAlertStock, array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName($tableAlertStock, 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName($tableAlertStock, 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Demka_Product Alert Stock_For_Anonymous');
$installer->getConnection()->createTable($table);

$installer->endSetup();