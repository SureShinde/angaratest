<?php
class Demo_Practice_Model_Observer{
 
    public function cronStatus(){ 
	
		//	Let the cron run only twice a day
		if(Mage::helper('function')->canRunCron('followup') === false){
			return false;
		}
		 
		//$yesterdayDate 	= 	date('Y-m-d H:m:s', strtotime(' -1 day'));
		//$yesterdayDate 	= 	'2015-03-02 02:03:07';
		//echo '<br>todayDate->'.$todayDate;		echo '<br>yesterdayDate->'.$yesterdayDate;
		/*$schedule = Mage::getModel('cron/schedule')->getCollection()
					//->addFieldToFilter('created_at', array('from' => $yesterdayDate, 'to' => $todayDate))
					//->getSelect()->limit('10')
					->setOrder('schedule_id', 'desc')
					->setPageSize(10)
           			->setCurPage(1)
					//->load(1);die;
					;
		//prd($schedule);
		if(count($schedule)>0){
			$emailBody	=	'Below crons have been run on '.$baseUrl;
			foreach($schedule as $sch){
				$jobCode[]	=	$sch->getJobCode();
				$status[]	=	$sch->getStatus();
				$emailBody.=	'<br>Job Code <b>'.$sch->getJobCode().'</b> and Status <b>'.$sch->getStatus().'</b>';
			}
			//pr($jobCode);			prd($status);	
		}else{
			$emailBody	=	'Crons are having some issues on '.$baseUrl;	
		}*/
		$todayDateDB	= 	$this->getCurrentServerDateTime();			//	this will o/p date time same as db created_at
		$todayDate 		= 	$this->getCurrentServerDateTimeForCron();	//	'2015-12-10 16:11:35';
		$logMsg			=	"cron_status cron running now. The admin orders time is ".$todayDate." and DB created_at time is ".$todayDateDB;
		Mage::log($logMsg, null, 'cron.log', true);	
		Mage::helper('function')->sendEmail( 'Cron Status Check on '.$baseUrl, $logMsg , $to = 'vaseem.ansari@angara.com', $name = 'Vaseem Angara');
    } 
	
	
	public function cronStatusTest(){  
		
		$todayDate 	= 	Mage::helper('function')->getCurrentServerDateTime();		//	'2015-12-10 16:11:35';
		
		$date 		= 	strtotime($todayDate);
		$currentHour=	date('H', $date);
		
		if($currentHour>1 && $currentHour<2){
			Mage::log("Cron running at ".$todayDate." and hour is ".$currentHour, null, 'cron.log', true);
		}else{
			Mage::log("Cron running at ".$todayDate." and hour is ".$currentHour, null, 'cron.log', true);
		}
		$emailBody	=	"Cron running at ".$todayDate." and hour is ".$currentHour;
		Mage::log("Cron running on ".$baseUrl.' at '.$todayDate, null, 'cron.log', true);
		Mage::helper('function')->sendEmail( 'Test Cron running ', $emailBody , $to = 'vaseem.ansari@angara.com', $name = 'Vaseem Angara');
    } 
	
}