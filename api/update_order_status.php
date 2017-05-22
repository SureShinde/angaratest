<?php	
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);


$order = Mage::getModel('sales/order')->load(25409);		//	order id in url https://www.angara.com/index.php/admin/sales_order/view/order_id/25626
//echo '<pre>'; print_r($order->getData());die;

try{
	//$order->setData('state', 'complete');
	$order->setStatus('complete');
	$history = $order->addStatusHistoryComment('Manually set the order status to Complete.', false);
	$history->setIsCustomerNotified(false);
	$order->save();
	echo 'Manually set the order status to Complete.';
}catch(Exception $e){
	echo $e->getMessage();
	Mage::logException($e);	
}