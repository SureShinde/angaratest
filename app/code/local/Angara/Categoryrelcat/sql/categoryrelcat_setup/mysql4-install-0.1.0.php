<?php
echo 'Running This Upgrade: '.get_class($this)."<br />";
$installer = $this;
$installer->startSetup();
 
$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
 
$installer->addAttribute('catalog_category', 'category_related_categories',  array(
    'type'     => 'varchar',
    'label'    => 'Related Categories',
    'input'    => 'textarea',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false//,
//    'default'           => ''
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'category_related_categories',
    '11'                    //last Magento's attribute position in General tab is 10
);
 
$attributeId = $installer->getAttributeId($entityTypeId, 'category_customer_reveiws');
echo 'upgrade script before run <br />';

//$installer->run("
//INSERT INTO `{$installer->getTable('catalog_category_entity_varchar')}`
//(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
//    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, ''
//        FROM `{$installer->getTable('catalog_category_entity')}`;
//");
echo 'upgrade script after run <br />';

//this will set data of your custom attribute for root category
Mage::getModel('catalog/category')
    ->load(1)
    ->setImportedCatId(0)
    ->setInitialSetupFlag(true)
    ->save();
 echo 'upgrade script after root category save <br />';
    
//this will set data of your custom attribute for default category
Mage::getModel('catalog/category')
    ->load(2)
    ->setImportedCatId(0)
    ->setInitialSetupFlag(true)
    ->save();
 echo 'upgrade script after default category save <br />';
    
$installer->endSetup();
echo 'Upgrade complete <br />';
//die("Exit for now");
?>