<?php	
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//set_time_limit(0);
ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes



	$read  	= 	Mage::getSingleton('core/resource')->getConnection('core_read');
	$write 	= 	Mage::getSingleton('core/resource')->getConnection('core_write');
	
	/*	S:VA	Update CMS Page	*/
	$cmsPageTN		=	Mage::getSingleton('core/resource')->getTableName('cms_page');
	$query1			=	"update $cmsPageTN set identifier = replace(identifier, '.html', '') where identifier like '%.html' order by page_id desc";
	$query2			=	"update $cmsPageTN set content = replace(content, '.html', '') where content like '%.html%' order by page_id desc";
	//echo $query1;die;
		
	try {
		$write->beginTransaction();
		$write->query($query1);
		$write->query($query2);
		$write->commit();
		echo '<br>CMS Page data updated';
	} catch (Exception $e) {
		$transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
		echo '<br>'.$e->getMessage();
	}
	/*	E:VA	Update CMS Page	*/	
	
	
	/*	S:VA	Update Category	*/
	$categoryTN		=	Mage::getSingleton('core/resource')->getTableName('catalog_category_entity_text');
	$query3			=	"update $categoryTN set value = replace(value, '.html', '') where attribute_id = (select attribute_id from eav_attribute where attribute_code='description' AND entity_type_id='3')";
	$query4			=	"update $categoryTN set value = replace(value, '.html', '') where attribute_id = (select attribute_id from eav_attribute where attribute_code='category_related_categories' AND entity_type_id='3')";
	$query5			=	"update $categoryTN set value = replace(value, '.html', '') where attribute_id = (select attribute_id from eav_attribute where attribute_code='category_buy_guides' AND entity_type_id='3')";
	$query6			=	"update $categoryTN set value = replace(value, '.html', '') where attribute_id = (select attribute_id from eav_attribute where attribute_code='category_article_posts' AND entity_type_id='3')";
	$query7			=	"update $categoryTN set value = replace(value, '.html', '') where attribute_id = (select attribute_id from eav_attribute where attribute_code='category_rel_search' AND entity_type_id='3')";
	//echo $query3;die;
		
	try {
		$write->beginTransaction();
		$write->query($query3);
		$write->query($query4);
		$write->query($query5);
		$write->query($query6);
		$write->query($query7);
		$write->commit();
		echo '<br>Category data updated';
	} catch (Exception $e) {
		$transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
		echo '<br>'.$e->getMessage();
	}
	/*	E:VA	Update Category	*/	
	
	
	/*	S:VA	Update Static Blocks	*/
	$cmsBlockTN		=	Mage::getSingleton('core/resource')->getTableName('cms_block');
	$query8			=	"update $cmsBlockTN set content = replace(content, '.html', '') where content like '%.html%'";
	//echo $query8;die;
		
	try {
		$write->beginTransaction();
		$write->query($query8);
		$write->commit();
		echo '<br>Static Blocks data updated';
	} catch (Exception $e) {
		$transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
		echo '<br>'.$e->getMessage();
	}
	/*	E:VA	Update Static Blocks	*/


	/*	S:VA	Update Banners */
	$bannerTN		=	Mage::getSingleton('core/resource')->getTableName('angara_promotional_banner');
	$query9			=	"update $bannerTN set url = replace(url, '.html', ''), description = replace(description, '.html', ''), html_content = replace(html_content, '.html', '') where url like '%.html%'";
	//echo $query9;die;
		
	try {
		$write->beginTransaction();
		$write->query($query9);
		$write->commit();
		echo '<br>Banners data updated';
	} catch (Exception $e) {
		$transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
		echo '<br>'.$e->getMessage();
	}
	/*	E:VA	Update Banners	*/


	/*	S:VA	Update Transactional Email Templates */
	$emailTemplateTN=	Mage::getSingleton('core/resource')->getTableName('core_email_template');
	$query10			=	"update $emailTemplateTN set template_text = replace(template_text, '.html', '') where template_text like '%.html%'";
	//echo $query10;die;
		
	try {
		$write->beginTransaction();
		$write->query($query10);
		$write->commit();
		echo '<br>Transactional Email Templates data updated';
	} catch (Exception $e) {
		$transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
		echo '<br>'.$e->getMessage();
	}
	/*	E:VA	Update Transactional Email Templates	*/


	/*	S:VA	Update Search Results */
	$searchResultsTN	=	Mage::getSingleton('core/resource')->getTableName('catalogsearch_query');
	$query11			=	"update $searchResultsTN set redirect = replace(redirect, '.html', '') where redirect like '%.html%'";
	//echo $query11;die;
		
	try {
		$write->beginTransaction();
		$write->query($query11);
		$write->commit();
		echo '<br>Search Results data updated';
	} catch (Exception $e) {
		$transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
		echo '<br>'.$e->getMessage();
	}
	/*	E:VA	Update Search Results	*/

?>

