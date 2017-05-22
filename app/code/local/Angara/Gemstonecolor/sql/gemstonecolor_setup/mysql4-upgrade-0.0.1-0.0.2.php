<?php
//$installer->startSetup();

$installer = $this;
$attribute  = array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Title Suffix',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => 1,
    'required' => 0,
    'group'  => 'General',
    //'default'=>'| Angara',
    'user_defined' => 1,
    'sort_order' => 6,
    'source'=>''
    
); 

$installer->addAttribute('catalog_category','title_suffix',$attribute); // edit categories
$installer->run("ALTER TABLE  {$this->getTable('cms_page')} ADD `title_suffix` varchar( 250 );"); //edit cms pages
/*$categories = Mage::getModel('catalog/category')
    ->getCollection()
    ->addAttributeToSelect('*')
    ->addIsActiveFilter();
foreach($categories as $category)
{
    $category->settitle_suffix('| Angara');
    $category->save();
}*/
$installer->endSetup();