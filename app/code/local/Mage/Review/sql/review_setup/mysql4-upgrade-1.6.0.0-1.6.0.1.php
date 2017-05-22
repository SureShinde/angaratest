<?php
$installer = $this;
$installer->startSetup();




$table = $installer->getConnection()
    ->newTable($installer->getTable('review/review_type_status'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,
        'primary'   => true,
        ), 'Type id')
    ->addColumn('status_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
      'nullable'  => true,
        ), 'Status code')
    ->setComment('Review Type statuses');
$installer->getConnection()->createTable($table);
  
$installer->run("
	ALTER TABLE  {$this->getTable('review/review')} ADD `type_status_id` TEXT( 50 );
	");

$installer->endSetup();