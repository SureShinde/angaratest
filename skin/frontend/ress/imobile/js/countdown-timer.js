function showtimer(difference, ShowDivElement, HideDivElement){
	//console.log(difference);
	//var difference = totalMiliSeconds;
	// ticker work
	var ticker;
	jQuery(function(){
		ticker = jQuery('#' +ShowDivElement);
		convertTicker();
	})
	function zeroPad (number) {
		// Pad a number with a zero, to make it 2 digits
		return ((number < 10) ? "0" : "") + String(number);
	}
	function convertTicker(){
		markupTicker();
	}
	function markupTicker(){
		ticker.days = jQuery('<span class="daydigits"></span>');
		ticker.hours = jQuery('<span class="hourdigits"></span>');
		ticker.minutes = jQuery('<span class="minutedegites"></span>');
		ticker.seconds = jQuery('<span class="secondigits"></span>');
		
		//console.log(ticker.hours);
		//if(difference > 86400 * 1000){
			ticker.append(ticker.days);
			ticker.append(jQuery('<span>: </span>'));
		//}
		
		//ticker.append(jQuery(' : '));
		ticker.append(ticker.hours);
		ticker.append(jQuery('<span>:</span>'));
		//ticker.append(jQuery(':'));
		ticker.append(ticker.minutes);
		ticker.append(jQuery('<span>:</span>'));
		//ticker.append(jQuery(':'));
		ticker.append(ticker.seconds);
		updateTicker();
	}
	function updateTicker(){
		difference-=1000;
		if(difference > 0){
			
			var days		=	difference/(1000*24*60*60);
			var daysToHours	=	0;
			if( days > 1 ){
				var days	=	parseInt(days, 10);
				var daysToHours	=	days * 24;
				ticker.days.text( zeroPad(parseInt(difference/(1000*24*60*60))) );
			}
			//console.log(days);
			//console.log(daysToHours);			
			
			//ticker.hours.text( zeroPad(parseInt(difference/(1000*60*60) - daysToHours)) );
			//	S:VA	Showing ticker more than 24 hours
			ticker.hours.text( zeroPad(parseInt(difference/(1000*60*60))));	
			ticker.minutes.text( zeroPad(parseInt(difference/(1000*60))%60) );
			ticker.seconds.text( zeroPad(parseInt(difference/(1000))%60) );
			setTimeout(function(){updateTicker()},1000);
		}
		else{
			//ticker.hide();
			jQuery('#' +HideDivElement).hide();
		}
	}
}