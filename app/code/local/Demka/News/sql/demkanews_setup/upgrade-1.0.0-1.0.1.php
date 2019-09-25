<?php

$installer = $this;
$tableNews = $installer->getTable('demkanews/table_news');

$installer->startSetup();

$installer->getConnection()
    ->addColumn($tableNews, 'priority', array(
        'comment' => 'News Priority',
        'default' => '0',
        'nullable' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    ));

$installer->getConnection()
    ->addColumn($tableNews, 'created', array(
        'comment' => 'News Creation Date',
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'nullable' => false,
    ));

$installer->endSetup();