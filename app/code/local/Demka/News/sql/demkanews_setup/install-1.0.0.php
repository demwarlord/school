<?php
$installer = $this;
$tableNews = $installer->getTable('demkanews/table_news');

$installer->startSetup();

$installer->getConnection()->dropTable($tableNews);
$table = $installer->getConnection()
    ->newTable($tableNews)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, '50', array(
        'nullable'  => false,
    ))
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ))
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, '250', array(
        'nullable'  => false,
    ));
$installer->getConnection()->createTable($table);

$installer->endSetup();