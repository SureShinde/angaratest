/* Ticker on offer strip */
		var today1 = new Date();
		var dd1 = today1.getDate() + 1;
		var mm1 = '0' + today1.getMonth(); //January is 0!	
		var yy1 = today1.getFullYear();	
		var counter_time2 = new Date(yy1,mm1,dd1,15,50,0); //new Date(2013,03,17,2,0,0); 
		var client_time2 = new Date();
		var difference2 = counter_time2 - client_time2;
		var difference3 = counter_time2 - client_time2;
		
		// ticker work
		var ticker2;
		var ticker3;
		
		jQuery(function(){
			ticker2 = jQuery('#countdownticker2');
			ticker3 = jQuery('#countdownticker3');
			convertTicker2();
			convertTicker3();
		});	
		function zeroPad (number) {
			// Pad a number with a zero, to make it 2 digits
			return ((number < 10) ? "0" : "") + String(number);
		}		
		
		function convertTicker2(){
			markupTicker2();
		}
		function convertTicker3(){
			markupTicker3();
		}			
			
		function markupTicker2(){
			//console.log("df");			
			ticker2.hours = jQuery('<span class="hourdigits2"></span>');
			ticker2.hours = jQuery('<span class="hourdigits2"></span>');
			ticker2.minutes = jQuery('<span class="minutedegites2"></span>');
			ticker2.seconds = jQuery('<span class="secondigits2"></span>');			
			ticker2.append(jQuery('<span class="timeleftxt2">Time Left</span><span class="colondigit2">: </span>'));
			ticker2.append(ticker2.hours);
			ticker2.append(jQuery('<span class="colondigit2">:</span>'));
			ticker2.append(ticker2.minutes);
			ticker2.append(jQuery('<span class="colondigit2">:</span>'));
			ticker2.append(ticker2.seconds);			
			updateTicker2();		
		}
		
		function updateTicker2(){
			difference2-=1000;
			if(difference2 > 0){
				ticker2.hours.text( zeroPad(parseInt(difference2/(1000*60*60))) );
				ticker2.minutes.text( zeroPad(parseInt(difference2/(1000*60))%60) );
				ticker2.seconds.text( zeroPad(parseInt(difference2/(1000))%60) );	
				setTimeout(function(){updateTicker2()},1000);
			}
			else{
				ticker2.hide();
			}
		}	
		
		function markupTicker3(){
			//console.log("df");			
			ticker3.hours = jQuery('<span class="hourdigits2"></span>');
			ticker3.hours = jQuery('<span class="hourdigits2"></span>');
			ticker3.minutes = jQuery('<span class="minutedegites2"></span>');
			ticker3.seconds = jQuery('<span class="secondigits2"></span>');			
			ticker3.append(jQuery('<span class="timeleftxt2">Time Left</span><span class="colondigit2">: </span>'));
			ticker3.append(ticker3.hours);
			ticker3.append(jQuery('<span class="colondigit2">:</span>'));
			ticker3.append(ticker3.minutes);
			ticker3.append(jQuery('<span class="colondigit2">:</span>'));
			ticker3.append(ticker3.seconds);			
			updateTicker3();		
		}
		
		function updateTicker3(){
			difference3-=1000;
			if(difference3 > 0){
				ticker3.hours.text( zeroPad(parseInt(difference3/(1000*60*60))) );
				ticker3.minutes.text( zeroPad(parseInt(difference3/(1000*60))%60) );
				ticker3.seconds.text( zeroPad(parseInt(difference3/(1000))%60) );	
				setTimeout(function(){updateTicker3()},1000);
			}
			else{
				ticker3.hide();
			}
		}	