<?php


$installer = $this;

$installer->startSetup();
try{

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('digger_grammar_rule')};
CREATE TABLE IF NOT EXISTS {$this->getTable('digger_grammar_rule')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `grammar_rule` varchar(255) NOT NULL default '',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('digger_keyword_synonym')};
CREATE TABLE IF NOT EXISTS {$this->getTable('digger_keyword_synonym')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `master_keyword` varchar(255) NOT NULL default '',
  `synonym` varchar(255) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    "
	
	);
}
catch(Exception $e) { 
	Mage::logException($e); 
}	
	
	
$installer->endSetup(); 

?>