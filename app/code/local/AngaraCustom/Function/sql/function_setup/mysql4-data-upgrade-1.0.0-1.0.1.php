<?php
$this->startSetup();

$this->_conn->addColumn($this->getTable('cms_page'), 'custom_title', 'varchar(255)');

$this->endSetup();
?>