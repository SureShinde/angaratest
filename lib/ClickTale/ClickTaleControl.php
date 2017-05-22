<?php
/**
 * ClickTale - PHP Integration Module
 *
 * LICENSE
 *
 * This source file is subject to the ClickTale(R) Integration Module License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.clicktale.com/Integration/0.2/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@clicktale.com so we can send you a copy immediately.
 *
 */
?>
<?php
	error_reporting(0);
	/* Copyright 2008-2009 ClickTale Ltd. */
	header("content-type: application/x-javascript");
	if (phpversion() < 5) exit("// [ClickTale] This server must be running PHP5 in order to run this script");

	define ("CT_VERSION", "1.4");
	define ("CT_VERSIION_DATE", "08.18.2009");

	require("ClickTaleControl_lib5.php");	

	if (isset($_GET['config'])) 
		$ct = new ClickTaleControl($_GET['config']);
	else  
		$ct = new ClickTaleControl();
	
	if (isset($_GET['debug'])) 
		$ct->print_status();
	
	if (!$ct->ignore_classification())
	{
		// don't record classified as not-to-record
		if (isset($_COOKIE['WRUID']) && $_COOKIE['WRUID'] == "0") 
			exit();

		// Record coming back users
		if ((isset($_COOKIE['WRUID']) && $ct->rec_repeat()) || $ct->filter_all()) 
		{
			exec_script($ct->settings['script']);
		}
	}
	else
	{
		if ( $ct->filter_all() ) 
		{
			exec_script($ct->settings['script']);
		}
	}
	
?>
