<?php

$dataTable 	= 'sales_flat_shipment_track';
$fieldsSql 	= 'SHOW COLUMNS FROM ' . $dataTable;
$cols 		= $this->getConnection()->fetchCol($fieldsSql);

if (!in_array('is_new', $cols)){
    $this->run("
        ALTER TABLE `{$dataTable}` ADD `is_new` INT(2) NOT NULL AFTER `carrier_code`
    ");
}
$this->endSetup();
?>