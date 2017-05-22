<?php
$directory    	= 	$_SERVER['DOCUMENT_ROOT'].'/media/reviews/';
$siteUrl		=	$_SERVER['HTTP_HOST'].'/media/reviews/';
$scannedFiles 	= 	array_diff(scandir($directory,1), array('..', '.','review_full_feed.xml'));
//echo '<pre>';print_r($scannedFiles);
if(count($scannedFiles)){
	foreach($scannedFiles as $file){
		//echo $siteUrl.$file.'<br>';
		echo "<a href='http://".$siteUrl.$file."'>".$file."</a>".'<br>';	
	}	
}
?>
