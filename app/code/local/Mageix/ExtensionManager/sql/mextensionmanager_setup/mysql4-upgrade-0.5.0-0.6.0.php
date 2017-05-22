<?php

$this->startSetup();

try {
    $this->run("ALTER TABLE `ixlic_licenses` ADD COLUMN `extension_version` varchar(255) NOT NULL");
} catch (Exception $e) {
    // column already exists
}
$this->endSetup();