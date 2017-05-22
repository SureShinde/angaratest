<?php
class Angara_Popup_Model_Ringsizer extends Mage_Core_Model_Abstract {
	
	public function requestRingsizer($request) {
		
		if(trim($request["rs-fname"])!='' && trim($request["rs-lname"])!='' && trim($request["rs-emailId"])!='' && trim($request["rs-contact"])!='') {	
			
			$html='<table width="600" border="1" cellspacing="0" cellpadding="0" style="font:12px Arial, Helvetica, sans-serif;">
			<tr>
			  <td colspan="4" height="30" style="font:bold 13px/18px Arial, Helvetica, sans-serif; padding:5px 10px;">
			  <span style="font:bold 16px/30px Arial, Helvetica, sans-serif;">Hi Admin,</span><br />
			  This is a request mail from <span style="color:#B72629">'.$request["rs-fname"].' '.$request["rs-lname"].'</span> about the ringsizer.<br />
		His/Her contact Details are as follows:</td>
			</tr>';
			
			$html.='<tr>
					<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Address</td>
					<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Phone Number</td>
					<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Email</td>';
					
			if($request["rs-getintouch"]=='ON') {
				$html.='<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Preffered Date and Time: </td>';
			}
			$html.='</tr>';
					
			$html.='
			 <tr>
				<td height="30" valign="top" style="font:12px/18px Arial, Helvetica, sans-serif; padding-left:10px;padding-bottom:10px;"> '.$request["rs-addressI"].'<br />'.$request["rs-addressII"].'<br />'.$request["rs-zipcode"].', '.$request["rs-city"].'<br />'.$request["rs-state"].', US</td>
				<td height="30" valign="top" style="font:12px/18px Arial, Helvetica, sans-serif; padding-left:10px;padding-bottom:10px;">'.$request["rs-contact"].'</td>
				<td height="30" valign="top" style="font:12px/18px Arial, Helvetica, sans-serif; padding-left:10px;color:#000;padding-bottom:10px;">'.$request["rs-emailId"].'</td>';
				
			if($request["rs-getintouch"]=='ON') {
				$html.='<td height="30" valign="top" style="font:12px/20px Arial, Helvetica, sans-serif; padding-left:10px;padding-bottom:10px;">
				<b>The customer has demanded a consultant.</b><br />
				<b>Date:</b> '.$request["rs-Preferreddate"].'<br />
				<b>Time:</b> '.$request["rs-Preferredtime"].'<br />
				<b>TimeZone:</b> '.$request["rs-timezone"].'
				</td>';
			}
			   
			$html.='</tr>';		
			$html.='</table>';
			$mail = Mage::getModel('core/email');
			$mail->setToName('Angara Sales');
			$mail->setToEmail('customer.service@angara.com');
			$mail->setBody($html);
			$mail->setSubject('Ring Sizer Request - '.date('F jS, Y'));
			$mail->setFromEmail('techsupport@angara.com');
			$mail->setFromName("Angara INC");
			$mail->setType('html');// can use Html or text as Mail format
			try {
				$mail->send();
				//Mage::getSingleton('core/session')->addSuccess('Your request has been sent');		
				//echo 'The ring sizer will be mailed to your address in 5 working days.';
				echo '<span style="color:#070;font:12px/18px Arial, Helvetica, sans-serif;">The ring sizer will be mailed to your address in 5 working days.</span>';
			}
			catch (Exception $e) {
				//Mage::getSingleton('core/session')->addError('Unable to send.');
				//echo 'Unable to process your request currently.';
				echo '<span style="color:#f00;font:12px/18px Arial, Helvetica, sans-serif;">Error processing your request this time.</span>';
			}
		}
		else {
			echo '<span style="color:#f00;font:12px/18px Arial, Helvetica, sans-serif;">Required fields are empty.</span>';
		}
	}	
}
?>