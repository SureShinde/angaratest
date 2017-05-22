// JavaScript Document
function getbredcrumsajax(category,product,backurl)
{
	jq.post(hp_bred_baseurl + 'bredcrums/ajax/bred/',{category:category,product:product,backurl:backurl},getbredcrums);
}
function getbredcrums(data)
{
	document.getElementById('bredcrumsdiv').innerHTML = data;
}