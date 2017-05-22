<?php
class Angara_Customerreview_Helper_Data extends Mage_Core_Helper_Abstract {

	// this rule should exist in followup rules
    const Order_Completion_Rule_Id = '5';
	// this rule should exist in followup rules
    const Thankyou_Rule_Id = '6';
	//launched date
	const Launched_Date = '2020-12-31';
	//OrderReviewMailRule - create this rule in admin panel 
	const Order_Review_Mail_Rule_Id = '7';
	//Followup mails to be scheduled for earlier orders - 	
	const Followup_Mails_Count = '2';
	
	// following templates need to be created in admin panel 
	const Thankyou_Mail1_Template_Id = 'email:Thankyoumail1';
	const Thankyou_Mail2_Template_Id = 'email:Thankyoumail2';
	const Thankyou_Mail3_Template_Id = 'email:Thankyoumail3';
	const Thankyou_Mail4_Template_Id = 'email:Thankyoumail4';
	const Thankyou_Mail5_Template_Id = 'email:Thankyoumail5';
	
	//Queue mails sender name and emails -
	const Sender_Name = 'Angara.com';
	const Sender_Email = 'customer.service@angara.com';
	
	//EmailTemplate for older orders
	//const Order_Review_Mail_Template_Id = 'email:OldOrderReview';
	const Order_Review_Mail_Template_Id = 'email:OrderReview';
	
	public function getThankyouMailRuleId() {
		return self::Thankyou_Rule_Id;
	}
	public function getThankyouMail1TemplateId() {
		return self::Thankyou_Mail1_Template_Id;
	}
	public function getThankyouMail2TemplateId() {
		return self::Thankyou_Mail2_Template_Id;
	}
	public function getThankyouMail3TemplateId() {
		return self::Thankyou_Mail3_Template_Id;
	}
	public function getThankyouMail4TemplateId() {
		return self::Thankyou_Mail4_Template_Id;
	}
	public function getThankyouMail5TemplateId() {
		return self::Thankyou_Mail5_Template_Id;
	}
	public function getOrderReviewMailRuleId()	{
		return self::Order_Review_Mail_Rule_Id;
	}
	public function getOrderCompletionRuleId()	{
		return self::Order_Completion_Rule_Id;
	}
	public function getFollowupMailsCount()	{
		return self::Followup_Mails_Count;
	}
	public function getOrderReviewMailTemplateId()	{
		return self::Order_Review_Mail_Template_Id;
	}
	public function getOrderReviewEmailsSchedule()	{
		 $followupSchedule[] = '+7 days';
		 $followupSchedule[] = '+10 days';
		 $followupSchedule[] = '+13 days';
		 $followupSchedule[] = '+16 days';
		 $followupSchedule[] = '+19 days';
		 
		 return $followupSchedule;
	}
	public function getSenderName()	{
		return self::Sender_Name;
	}
	
	public function getSenderEmail()	{
		return self::Sender_Email;
	}
	public function getLaunchedDate()	{
		return self::Launched_Date;
	}
}
