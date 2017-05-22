<?php 

/**
 *
 * Add for reduce the indexing time in catalog search
 * @author Asheesh Singh
 *
 */ 
 
$installer = $this;

$installer->startSetup();
	try{
		if(version_compare(Mage::getVersion(), '1.6', '<=')) {
			$installer->getConnection()->addColumn($installer->getTable('catalogsearch/fulltext'), 'updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP." NULL DEFAULT '' COMMENT 'Updated At'");
		} else {
			$installer->getConnection()
					  ->addColumn($installer->getTable('catalogsearch/fulltext'),'updated_at',
													array(
															'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
															'nullable' => true,
															'comment' => 'Updated At'
													)
			);
			
		}
		/* $installer->run("ALTER TABLE `{$installer->getTable('catalogsearch/fulltext')}`
			DROP INDEX `UNQ_CATALOGSEARCH_FULLTEXT_PRODUCT_ID_STORE_ID` ,
			ADD UNIQUE `UNQ_CATALOGSEARCH_FULLTEXT_PRODUCT_ID_STORE_ID` ( `product_id` , `store_id` , `updated_at` )"); */
  
	} catch(Exception $e) { 
		Mage::logException($e); 
	}	
$installer->endSetup();