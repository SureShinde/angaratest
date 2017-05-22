<?php

/*
 * imgurl.php allows you to return image url for imgquery.
 * http://www.example.com/imgurl.php?imgurl=/l/p/lp_5009.jpg
 */

ini_set('memory_limit','256M');

require_once 'app/Mage.php';
umask(0);
Mage::app()->setCurrentStore('1');
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(0);


$product  = "0";
$imgurl   = "";

if (isset($_GET["imgquery"])) {  
    $string = filter_input(INPUT_GET, 'imgquery', FILTER_SANITIZE_URL);
    $imgquery = trim($string);
    $imgurl = Mage::helper('catalog/image')->init($product, 'image', $imgquery)->resize(250, 250);
}
if ($imgurl) {
    echo '<img src="' . $imgurl . '"/>';
}
?>