var needToConfirm = true;
window.onmousedown = focusfunc;
document.onmousedown = focusfunc;
window.onkeydown = focusfunc1;
window.onbeforeunload = confirmExit;
window.onclick = focusfunc;
function confirmExit()
{
	if(needToConfirm && hp_cartpopupflag==1)
	{
		if(typeof currentproductpriceshow_hp != 'undefined')
		{
			var valp = "";
			valp = jQuery("#pricesection .saleprice .pricered").html();
			if(!valp)
			{
				valp = jQuery(".final-price").html();
			}
			valp = valp.substr(1);
			valp = valp.replace(/,/i, "");
			valp = (valp * currentproductpriceshow_hp)/100;
			valp = Math.round(valp * 100);
			valp = valp + '';
			valp = valp.substr(0,valp.length - 2) + '.' + valp.substr(valp.length - 2);
			
			document.getElementById('spdiscamount1').innerHTML = "$" + valp;
			document.getElementById('spdiscamount2').innerHTML = "$" + valp;
			document.getElementById('spdiscamount3').innerHTML = "$" + valp;
		}
		document.getElementById('cartpopup').style.display = 'block';
		return "Wait, Press STAY ON PAGE to get a SPECIAL OFFER";
	}
	else
	{
		needToConfirm = true;
	}
}
function focusfunc()
{
	needToConfirm = false;
	setTimeout('blurfunc()',200);
}
function focusfunc1(e)
{
	var key = window.event ? e.keyCode : e.which;
	if(key==13)
	{
		needToConfirm = false;
		setTimeout('blurfunc()',200);
	}
}
function blurfunc()
{
	needToConfirm = true;
}