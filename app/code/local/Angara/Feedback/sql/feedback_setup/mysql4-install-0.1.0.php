<?php
$installer = $this;
$installer->startSetup();

$installer->run(" 
	CREATE TABLE IF NOT EXISTS {$this->getTable('ang_feedback')} (
		feedback_id int NOT NULL auto_increment, 
		email varchar(30) NOT NULL,
		contact_number varchar(15),
		category_id int(4) NOT NULL,
		sub_category_id int(4),
		message TEXT NOT NULL,
		status SMALLINT(6) NOT NULL DEFAULT '1',
		primary key(feedback_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS {$this->getTable('ang_feedback_category')}(
		category_id INT(11) NOT NULL AUTO_INCREMENT,
		name varchar(30) NOT NULL,
		description TEXT NOT NULL,
		parent_category_id int(4) NOT NULL,
		status SMALLINT(6) NOT NULL DEFAULT '1',
		primary key(category_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	INSERT INTO `{$this->getTable('ang_feedback_category')}` (`category_id`, `name`, `description`, `parent_category_id`, `status`) VALUES
	(1, 'Improve this page', 'Improve this page', 0, 1),
	(2, 'Suggest new features/ideas', 'Suggest new features/ideas', 0, 1),
	(3, 'Shopping Experience', 'Shopping Experience', 0, 1),
	(4, 'I love Angara', 'I love Angara', 0, 1),
	(5, 'Others - General feedback', 'Others - General feedback', 0, 1),
	(6, 'Angara Offers Zone feedback', 'Angara Offers Zone feedback', 0, 1);

");
$installer->endSetup();