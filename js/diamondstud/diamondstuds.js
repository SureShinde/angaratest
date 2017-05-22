
window.onload=callout;
//var ajaxroot='http://192';


		
function callout()
{
	//alert("efwe");
	ds_metalchange('1051');
}

function ds_metalchange(style)
{
	//ds_injecthtml('p',metal,style);
	
	var metal= document.getElementById("ds_ddl_metal").value;
	
		//case 1 14 k whilte gold
		if(metal=="130")
		{
			if(document.getElementById("ddl_dshaper").checked== true)
	        {
				// 3 show 
				document.getElementById("dv_ss1").style.display="block";
				document.getElementById("dv_ss2").style.display="block";
				document.getElementById("dv_ss3").style.display="block";
				document.getElementById("dv_ss4").style.display="block";
				
				document.getElementById("dv_ss1").className ="studstype1";
				document.getElementById("dv_ss2").className ="studstype2";
				document.getElementById("dv_ss3").className ="studstype3";
				document.getElementById("dv_ss4").className ="studstype4";
				
				
				ds_injecthtml('r',metal,style);
				
				
			}
			else
			{
				// 1 show 
				document.getElementById("dv_ss1").style.display="block";
				document.getElementById("dv_ss2").style.display="none";
				document.getElementById("dv_ss3").style.display="none";
				document.getElementById("dv_ss4").style.display="none";
				
				document.getElementById("dv_ss1").className ="studstype1p";
				//document.getElementById('dvajaresult').className  ='pricevariation4';
				
				ds_injecthtml('p',metal,style);
			} 
			
		}

		//case 2 14 k yellow gold
		if(metal=="131")
		{
			if(document.getElementById("ddl_dshaper").checked== true)
	        {
				//2 show
				document.getElementById("dv_ss1").style.display="block";
				document.getElementById("dv_ss2").style.display="block";
				document.getElementById("dv_ss3").style.display="none";
				document.getElementById("dv_ss4").style.display="none";
				
				document.getElementById("dv_ss1").className ="studstypeY1";
				document.getElementById("dv_ss2").className ="studstypeY2";
				
				
				ds_injecthtml('r',metal,style);
			}
			else
			{
				// 1
				document.getElementById("dv_ss1").style.display="block";
				document.getElementById("dv_ss2").style.display="none";
				document.getElementById("dv_ss3").style.display="none";
				document.getElementById("dv_ss4").style.display="none";
				
				document.getElementById("dv_ss1").className ="studstypeY1p";
				//document.getElementById('dvajaresult').className  ='pricevariation4';
				ds_injecthtml('p',metal,style);
			} 
			
		}
		
		
		//case 3 Platinum
		if(metal=="229")
		{
			if(document.getElementById("ddl_dshaper").checked== true)
	        {
			//2 show
				document.getElementById("dv_ss1").style.display="block";
				document.getElementById("dv_ss2").style.display="block";
				document.getElementById("dv_ss3").style.display="none";
				document.getElementById("dv_ss4").style.display="none";
				
				document.getElementById("dv_ss1").className ="studstypeP1";
				document.getElementById("dv_ss2").className ="studstypeP2";
				
				
			    ds_injecthtml('r',metal,style);
			}
			else
			{
				// 1
				document.getElementById("dv_ss1").style.display="block";
				document.getElementById("dv_ss2").style.display="none";
				document.getElementById("dv_ss3").style.display="none";
				document.getElementById("dv_ss4").style.display="none";
				
				document.getElementById("dv_ss1").className ="studstypeP1p";
				
				ds_injecthtml('p',metal,style);
			} 
		}
}




var g_shape;
function ds_injecthtml(shape,metal,style)
{
	    //dvwaiting
		
		 g_shape = shape; 
		
		document.getElementById('dvwaiting').style.display='block';
		a = jQuery.get('/diamondstud/index/getratelist/',{'shape':shape,'metal':metal,'style':style},ds_responseinject);
	
}
function ds_responseinject(data)
{

	document.getElementById('dvajaresult').innerHTML=data;
	document.getElementById('dvwaiting').style.display='none';
	if(g_shape=='p')
	{
	
	  document.getElementById('dvajaresult').className  ='pricevariation4';
	}
	if(g_shape=='r')
	{
	
	  document.getElementById('dvajaresult').className  ='pricevariation';
	}
	
	jQuery("#various2").fancybox({
		}).click(function(){
			return false;
		});		
		
}


function viewproduct(sku)
{
	
		a = jQuery.get('/diamondstud/index/viewproduct/',{'sku':sku});
	
}

