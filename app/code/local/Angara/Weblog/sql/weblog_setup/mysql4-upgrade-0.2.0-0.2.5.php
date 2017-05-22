<?php
echo 'upgrade script start';
$installer = $this;
$installer->startSetup();
 
$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
 
$installer->addAttribute('catalog_category', 'category_article_posts',  array(
    'type'     => 'varchar',
    'label'    => 'Article Posts',
    'input'    => 'textarea',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => 0
));
 
$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'category_article_posts',
    '11'                    //last Magento's attribute position in General tab is 10
);
 
$attributeId = $installer->getAttributeId($entityTypeId, 'category_article_posts');
echo 'upgrade script before run';

$installer->run("
INSERT INTO `{$installer->getTable('catalog_category_entity_varchar')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;
");
echo 'upgrade script after run';

//this will set data of your custom attribute for root category
Mage::getModel('catalog/category')
    ->load(1)
    ->setImportedCatId(0)
    ->setInitialSetupFlag(true)
    ->save();
 echo 'upgrade script after root category save';
    
//this will set data of your custom attribute for default category
Mage::getModel('catalog/category')
    ->load(2)
    ->setImportedCatId(0)
    ->setInitialSetupFlag(true)
    ->save();
 echo 'upgrade script after default category save';
    
$installer->endSetup();
 echo 'upgrade script end';

?>