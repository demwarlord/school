<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->addAttributeGroup('catalog_product', 'Default', 'Demka News Options', 1000);
$installer->addAttribute('catalog_product', 'tsg_news', array(
    'group' => 'Demka News Options',
    'input' => 'multiselect',
    'type' => 'text', // varchar doesn't work
    'source' => 'demkanews/attribute_source_tsgnews',
    'label' => 'News Binding',
    'backend' => 'eav/entity_attribute_backend_array',
    'frontend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->addAttribute('catalog_product', 'tsg_main_news', array(
    'group' => 'Demka News Options',
    'input' => 'select',
    'type' => 'int',
    'source' => 'demkanews/attribute_source_tsgnewsmain',
    'label' => 'Main News',
    'backend' => '',
    'frontend' => '',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();