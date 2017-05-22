<?php 
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('newsletter/thanksgivingdata'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'id')
    ->addColumn('subscriber_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Subscriber Email')
    ->addColumn('first_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'First Name')
    ->addColumn('last_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Last Name')
    ->addColumn('street_address', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Street Address')
    ->addColumn('apartment', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Apartment')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'City')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'State')
    ->addColumn('country', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Country')
    ->addColumn('zip_code', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Zip Code')
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_TEXT, 30, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Phone');
$installer->getConnection()->createTable($table);
