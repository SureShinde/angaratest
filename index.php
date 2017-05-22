<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (version_compare(phpversion(), '5.3.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.3.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
    exit;
} 
/**
 * Error reporting
 */ 
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}

if (file_exists($maintenanceFile)) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

/* if(isset($_GET['devang086']) && $_GET['devang086'] == 'mycondition') {
	
} else {
	include_once dirname(__FILE__) . '/errors/503.php';
    exit;
} */

require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $mageFilename;

# country configs-nishant
$countryConfigFile = 'app/Countrymapping.php';
if (file_exists($countryConfigFile)) {
    include_once $countryConfigFile;
}
# country configs-nishant


if (isset($_SERVER['MAGE_PROFILER_ENABLED']) && $_SERVER['MAGE_PROFILER_ENABLED']) {
	Varien_Profiler::enable();
}

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

if (isset($_SERVER['MAGE_IS_DISPLAY_ERRORS'])) {
	ini_set('display_errors', 1);
}

#ini_set('display_errors', 1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
if(!isset($_COOKIE['returningvisitor'])){
	/* set visitor as returning for 30 days */
	setcookie('returningvisitor', '1', time() + (30*24*60* 60), '/', '.angara.com');
}

if($_REQUEST['state']==''){
	if(!isset($_COOKIE['popupvisitor'])){
		setcookie('popupvisitor', '1', time() + (3*24*60* 60), '/', '.angara.com');
	}
}

if(!isset($_COOKIE['bottompopupvisitor']) || (isset($_COOKIE['bottompopupvisitor']) && !$_COOKIE['bottompopupvisitor'])){	
	if(0){
		$time = 2;
		setcookie('bottompopupvisitor', strtotime("+".$time." minutes"), time() + (10*365*24*60* 60), '/', '.angara.com');
	} else {
		setcookie('bottompopupvisitor', strtotime("+1 day"), time() + (10*365*24*60* 60), '/', '.angara.com');
	}
}
Mage::run($mageRunCode, $mageRunType);
