<?php	
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);

$transaction 	= 	Mage::getSingleton('core/resource')->getConnection('core_write');
try {
	$transaction->beginTransaction();
	$transaction->query('SET foreign_key_checks = 0;
						TRUNCATE dataflow_batch_export;
						TRUNCATE dataflow_batch_import;
						TRUNCATE log_customer;
						TRUNCATE log_quote;
						TRUNCATE log_summary;
						TRUNCATE log_summary_type;
						TRUNCATE log_url;
						TRUNCATE log_url_info;
						TRUNCATE log_visitor;
						TRUNCATE log_visitor_info;
						TRUNCATE log_visitor_online;
						TRUNCATE report_viewed_product_index;
						TRUNCATE report_compared_product_index;
						TRUNCATE report_event;
						TRUNCATE index_event;
						TRUNCATE catalog_compare_item;
						SET foreign_key_checks = 1;');
	$transaction->commit();
	echo '<br>Following Tables Truncated Successfully:
		 <ol>
			<li>dataflow_batch_export</li>
			<li>dataflow_batch_export</li>
			<li>dataflow_batch_import</li>
			<li>log_customer</li>
			<li>log_quote</li>
			<li>log_summary</li>
			<li>log_summary_type</li>
			<li>log_url</li>
			<li>log_url_info</li>
			<li>log_visitor</li>
			<li>log_visitor_info</li>
			<li>log_visitor_online</li>
			<li>report_viewed_product_index</li>
			<li>report_compared_product_index</li>
			<li>report_event</li>
			<li>index_event</li>
			<li>catalog_compare_item</li>
		 </ol>
			';
} catch (Exception $e) {
	$transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
	echo '<br>'.$e->getMessage();
}