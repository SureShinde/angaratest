jQuery(document).ready(readyfunc);
function readyfunc() {
	
	injecthtmlrcv();
}
function atwix_showPreview(prod) {
    var pid = prod.attr('prod_id');
    jQuery(".rw-preview-wrapper[prod_id='"+pid+"']").fadeIn('fast');
    prod.addClass('rw-thumb-selected');
}

function atwix_hidePreviews() {
    jQuery('.rw-thumb').removeClass('rw-thumb-selected');
    //jQuery(".rw-preview-wrapper").fadeOut('fast');
    jQuery(".rw-preview-wrapper").hide();
}

function togglePanel() {
    if (jQuery('.rw-wrapper-hidden').is(':visible')) {
        jQuery('.rw-wrapper-hidden').fadeOut('fast');
        jQuery('.rw-wrapper').fadeIn('fast');
        setPanelState('show');
    }
    else {
        jQuery('.rw-wrapper').fadeOut('fast');
        jQuery('.rw-wrapper-hidden').fadeIn('fast');
        setPanelState('hide');
    }
}

function setPanelState(state, site_url) {
    jQuery.post('/rvi_panelstate/ajax/panelstate/', {state: state});
}

function deleteRecentViewed(id)
{
	jQuery.post('/rvi_panelstate/ajax/deleterecentviewed/', {id: id});
	var ch = document.getElementById('rec_prod_' + id);
	ch.parentNode.removeChild(ch);
	ch = document.getElementById('1_rec_prod_' + id);
	ch.parentNode.removeChild(ch);
	ch = document.getElementById('2_rec_prod_' + id);
	ch.parentNode.removeChild(ch);
	
	chnodes = document.getElementById('rcvrwblock').childNodes;
	var fl = 0;
	for(var i=0;i<chnodes.length;i++)
	{
		if(chnodes.item(i).tagName == "A")
		{
			fl = 1;
		}
	}
	if(fl == 0)
	{
		var ch = document.getElementById('rvi_panel');
		ch.parentNode.removeChild(ch);
		var ch = document.getElementById('rvi_panel2');
		ch.parentNode.removeChild(ch);
		
	}
}

function injecthtmlrcv()
{
	var str = window.location.href;
	if(str.indexOf("/checkout/")>-1)
	{
		return false;
	}
	var id = '';
	if(document.getElementById('cert_id'))
	{
		id = document.getElementById('cert_id').value;
	}
	jQuery.post('/rvi_panelstate/ajax/gethtml/',{id:id},inserthtmlrcv);
}
function inserthtmlrcv(data)
{
	if(data != '')
	{
		dv = document.createElement('DIV');
		dv.innerHTML= data;
		document.body.appendChild(dv);
		bindallfuncs();
	}
}
function is_ie6(){
     return ((window.XMLHttpRequest == undefined) && (ActiveXObject != undefined));
}
function bindallfuncs()
{
	if(is_ie6())
	{
		document.getElementById('rvi_panel').style.display='none';document.getElementById('rvi_panel2').style.display='none';
		return false;
	}
    jQuery('.rw-thumb').mouseenter(function() { atwix_showPreview(jQuery(this)); });
    jQuery('.rw-thumb').mouseleave(function() { atwix_hidePreviews(); });
    jQuery('.rvi_btn').click(function() {togglePanel();} );
	jQuery('.rw-wrapper').hide();
	jQuery('.rw-wrapper-hidden').show();
	setPanelState('hide');
}
