<?php
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
/**
 * Add 'vendor_lead_time' attribute for entities
 */
$entities = array(
    //'quote',
    //'quote_address',
    'quote_item',
   // 'quote_address_item',
    //'order',
    'order_item'
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'visible'  => true,
    'required' => false
);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'vendor_lead_time', $options);
}
$installer->endSetup();
?>