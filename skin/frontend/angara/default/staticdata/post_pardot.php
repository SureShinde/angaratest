<?php 
error_reporting(0);
//echo '<pre>';print_r($_REQUEST);//exit;

if(isset($_REQUEST['submit_x']) && $_REQUEST['submit_x']!=''){
	
	$email = str_replace(" ","",$_REQUEST['email']);
	$first_name = str_replace(" ","",$_REQUEST['first_name']);
	$gender = str_replace(" ","",$_REQUEST['gender']);
	
	if($gender=='Male'){
		
		$special_birthday_month = str_replace(" ","",$_REQUEST['special_birthday_month']);
		$special_birthday_day = str_replace(" ","",$_REQUEST['special_birthday_day']);
		$special_birthday_year = str_replace(" ","",$_REQUEST['special_birthday_year']);
		
		if($special_birthday_month!='' && $special_birthday_day!='' && $special_birthday_year!=''){
			$special_birthday = $special_birthday_year.'-'.$special_birthday_month.'-'.$special_birthday_day;
		}
		else if($special_birthday_month!='' && $special_birthday_day!='' && $special_birthday_year==''){
			$special_birthday = '0000'.'-'.$special_birthday_month.'-'.$special_birthday_day;
		}
		else{
			$special_birthday = '';
		}
		//echo $special_birthday.'<br />';
		//$special_birthday = $_POST['special_someone_birthday'];
	}
	if($gender=='Female'){
		
		$birthday_month = str_replace(" ","",$_REQUEST['birthday_month']);
		$birthday_day = str_replace(" ","",$_REQUEST['birthday_day']);
		$birthday_year = str_replace(" ","",$_REQUEST['birthday_year']);
		
		if($birthday_month!='' && $birthday_day!='' && $birthday_year!=''){
			$birthday = $birthday_year.'-'.$birthday_month.'-'.$birthday_day;
		}
		else if($birthday_month!='' && $birthday_day!='' && $birthday_year==''){
			$birthday = '0000'.'-'.$birthday_month.'-'.$birthday_day;
		}
		else{
			$birthday = '';
		}
		//echo $birthday.'<br />';
		//$birthday = $_POST['birthday'];
	}	
	
    $mother_birthday_month = str_replace(" ","",$_REQUEST['mother_birthday_month']);
    $mother_birthday_day = str_replace(" ","",$_REQUEST['mother_birthday_day']);
    $mother_birthday_year = str_replace(" ","",$_REQUEST['mother_birthday_year']);
	
	if($mother_birthday_month!='' && $mother_birthday_day!='' && $mother_birthday_year!=''){
		$mother_birthday = $mother_birthday_year.'-'.$mother_birthday_month.'-'.$mother_birthday_day;
	}
	else if($mother_birthday_month!='' && $mother_birthday_day!='' && $mother_birthday_year==''){
		$mother_birthday = '0000'.'-'.$mother_birthday_month.'-'.$mother_birthday_day;
	}
	else{
		$mother_birthday = '';
	}
    
	//echo $mother_birthday.'<br />';
	//$mother_birthday = $_POST['mother_birthday'];
	
	$anniversary_month = str_replace(" ","",$_REQUEST['anniversary_month']);
    $anniversary_day = str_replace(" ","",$_REQUEST['anniversary_day']);
    $anniversary_year = str_replace(" ","",$_REQUEST['anniversary_year']);
	
	if($anniversary_month!='' && $anniversary_day!='' && $anniversary_year!=''){
		$anniversary_date = $anniversary_year.'-'.$anniversary_month.'-'.$anniversary_day;
	}
	else if($anniversary_month!='' && $anniversary_day!='' && $anniversary_year==''){
		$anniversary_date = '0000'.'-'.$anniversary_month.'-'.$anniversary_day;
	}
	else{
		$anniversary_date = '';
	}
	
	//echo $anniversary_date;//exit;
	//$anniversary_date = $_POST['anniversary_date'];
	

	$pardot_email = "nagendra.nukala@angara.com";
	$pardot_pass = "Angara1!";
	$pardot_user_key = "beaa6610d5ddaaa0dbafb8f155084916";
	
	// Get Api Key
	$url = "https://pi.pardot.com/api/login/version/3&email=".$pardot_email."&password=".$pardot_pass."&user_key=".$pardot_user_key;
	$getApi = simplexml_load_file($url);
	$apiKey = $getApi->api_key;
	
	// Read Customer
	$readUrls="https://pi.pardot.com//api/prospect/version/3/do/read/email/".$email."?api_key=".$apiKey."&user_key=".$pardot_user_key;
	$readUrl=simplexml_load_file($readUrls);
	
	$node = $readUrl->children();
	$email_user=$node[0]->email;
	
	if(!empty($email_user)){
		if($gender=='Male'){
			$updateUrls="https://pi.pardot.com//api/prospect/version/3/do/update/email/".$email_user."?first_name=".$first_name."&Gender=".$gender."&special_someone_birthday=".$special_birthday."&mother_birthday=".$mother_birthday."&anniversary_date=".$anniversary_date."&api_key=".$apiKey."&user_key=".$pardot_user_key;
			$updateUrl=simplexml_load_file($updateUrls);
		}		
		else if($gender=='Female'){
			$updateUrls="https://pi.pardot.com//api/prospect/version/3/do/update/email/".$email_user."?first_name=".$first_name."&Gender=".$gender."&birthday=".$birthday."&mother_birthday=".$mother_birthday."&anniversary_date=".$anniversary_date."&api_key=".$apiKey."&user_key=".$pardot_user_key;
			$updateUrl=simplexml_load_file($updateUrls);
		}		
	}
	else{
		$createUrls="https://pi.pardot.com//api/prospect/version/3/do/create/email/".$email."&api_key=".$apiKey."&user_key=".$pardot_user_key;
		$createUrl=simplexml_load_file($createUrls);
			
		if($gender=='Male'){
			$updateUrls="https://pi.pardot.com//api/prospect/version/3/do/update/email/".$email."?first_name=".$first_name."&Gender=".$gender."&special_someone_birthday=".$special_birthday."&mother_birthday=".$mother_birthday."&anniversary_date=".$anniversary_date."&api_key=".$apiKey."&user_key=".$pardot_user_key;
			$updateUrl=simplexml_load_file($updateUrls);
		}		
		else if($gender=='Female'){
			$updateUrls="https://pi.pardot.com//api/prospect/version/3/do/update/email/".$email."?first_name=".$first_name."&Gender=".$gender."&birthday=".$birthday."&mother_birthday=".$mother_birthday."&anniversary_date=".$anniversary_date."&api_key=".$apiKey."&user_key=".$pardot_user_key;
			$updateUrl=simplexml_load_file($updateUrls);
		}
	}
	header('Location: /skin/frontend/angara/default/staticdata/email-signup-thank-you.html');exit;
}
?>