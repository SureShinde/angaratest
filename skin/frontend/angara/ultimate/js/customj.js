var i_hp=0;
var optionid;
var curstone = '';
var defaultQualityGrade = '';
var defaultStoneSize = '';
var defaultMetalType = '';
var curQualityGrade = '';
var curStoneSize = '';
var curMetalType = '';
var curAngle = 1;
var curSizePX = 350;

var qualityArr = new Array();
var stoneSizeArr = new Array();
var stoneSizeArr1=new Array();
var stoneVariationCarats = new Array();
var stoneCaratArr = new Array();
var metalTypeArr = new Array();

var metalTextShort = new Array();
metalTextShort['Silver'] = 'SL';
metalTextShort['Platinum'] = 'PT';
metalTextShort['White Gold'] = 'WG';
metalTextShort['Yellow Gold'] = 'YG';
metalTextShort['Rose Gold'] = 'RG';

var gradeAndText = new Array();
var gradeAndSmallText = new Array();
gradeAndSmallText['A'] = 'Good';
gradeAndSmallText['AA'] = 'Better';
gradeAndSmallText['AAA'] = 'Best';
gradeAndSmallText['AAAA'] = 'Heirloom';
gradeAndSmallText['LC'] = 'Lab created';

gradeAndText['Blue Sapphire'] = new Array();
gradeAndText['Ruby'] = new Array();
gradeAndText['Emerald'] = new Array();
gradeAndText['Tanzanite'] = new Array();
gradeAndText['Aquamarine'] = new Array();

gradeAndText['Blue Sapphire']['A'] = '<strong>Quality Grade - Good (A): </strong> Top 75% of sapphires in terms of quality.  Dark blue and opaque.  This quality is comparable to that used by mall jewelry and chain stores.';
gradeAndText['Blue Sapphire']['AA'] = '<strong>Quality Grade - Better (AA): </strong> Top 33% of sapphires in terms of quality.  Medium blue and moderately included.  This quality is comparable to that used by leading independent/family jewelers.';
gradeAndText['Blue Sapphire']['AAA'] = '<strong>Quality Grade - Best (AAA): </strong> Top 10% of sapphires in terms of quality.  Medium blue, slightly included and exhibits high brilliance.  This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
gradeAndText['Blue Sapphire']['AAAA'] = '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of sapphires in terms of quality.  Truly exceptional deep rich blue, very slightly included and exhibits high brilliance.  This quality can be found only at the top boutiques in the world.';
gradeAndText['Blue Sapphire']['LC'] = '';

gradeAndText['Ruby']['A'] = '<strong>Quality Grade - Good (A): </strong> Top 75% of rubies in terms of quality. &nbsp;Dark pinkish red and opaque. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
gradeAndText['Ruby']['AA'] = '<strong>Quality Grade - Better (AA): </strong> Top 33% of rubies in terms of quality. &nbsp;Medium pinkish red and moderately included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
gradeAndText['Ruby']['AAA'] = '<strong>Quality Grade - Best (AAA): </strong> Top 10% of rubies in terms of quality. &nbsp;Medium red, slightly included and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
gradeAndText['Ruby']['AAAA'] = '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of rubies, in terms of quality. &nbsp;Truly exceptional deep rich red, very slightly included and exhibits high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
gradeAndText['Ruby']['LC'] = '';

gradeAndText['Emerald']['A'] = '<strong>Quality Grade - Good (A): </strong> Top 75% of emeralds in terms of quality. &nbsp;Dark green and opaque. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
gradeAndText['Emerald']['AA'] = '<strong>Quality Grade - Better (AA): </strong> Top 33% of emeralds in terms of quality. &nbsp;Medium green and heavily included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';

gradeAndText['Emerald']['AAA'] = '<strong>Quality Grade - Best (AAA): </strong> Top 10% of emeralds in terms of quality. &nbsp;Rich medium green, moderately included and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
gradeAndText['Emerald']['AAAA'] = '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of emeralds in terms of quality. &nbsp;Truly exceptional rich green, moderately to slightly included and exhibits high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
gradeAndText['Emerald']['LC'] = '';

gradeAndText['Tanzanite']['A'] = '<strong>Quality Grade - Good (A): </strong> Top 75% of tanzanites in terms of quality. &nbsp;Light violet blue and slightly included. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
gradeAndText['Tanzanite']['AA'] = '<strong>Quality Grade - Better (AA): </strong> Top 33% of tanzanites in terms of quality. &nbsp;Medium violet blue and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
gradeAndText['Tanzanite']['AAA'] = '<strong>Quality Grade - Best (AAA): </strong> Top 10% of tanzanites in terms of quality. &nbsp;Rich violet blue, eye clean and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
gradeAndText['Tanzanite']['AAAA'] = '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of tanzanites in terms of quality. &nbsp;Truly exceptional rich violet blue, eye clean and exhibits very high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
gradeAndText['Tanzanite']['LC'] = '';

gradeAndText['Aquamarine']['A'] = '<strong>Quality Grade - Good (A): </strong> Top 75% of aquamarines in terms of quality. &nbsp;Very light sea blue and moderately included. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
gradeAndText['Aquamarine']['AA'] = '<strong>Quality Grade - Better (AA): </strong> Top 33% of aquamarines in terms of quality. &nbsp;Light sea blue and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
gradeAndText['Aquamarine']['AAA'] = '<strong>Quality Grade - Best (AAA): </strong> Top 10% of aquamarines in terms of quality. &nbsp;Medium sea blue, eye clean and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
gradeAndText['Aquamarine']['AAAA'] = '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of aquamarines in terms of quality. &nbsp;Truly exceptional medium sea blue, eye clean and exhibits very high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
gradeAndText['Aquamarine']['LC'] = '';


var metalTypeAndText = new Array();
metalTypeAndText['Silver'] = "<strong>Silver:</strong> The same great look as white gold at a lower price.  Angara uses the highest standard available, .925 sterling silver.  Silver is lighter to wear than gold.";
metalTypeAndText['White Gold'] = "<strong>14K White Gold:</strong> The standard for fine jewelry.  14K White gold has been the most popular choice for fine jewelry over the last twenty years as it blends well with diamonds.";
metalTypeAndText['Yellow Gold'] = "<strong>14K Yellow Gold:</strong> Glowing and rich, yellow gold adds more color to the jewelry piece.  If buying as a gift, please note that women generally have a clear preference for Yellow or White Gold - see what other jewelry she has to determine her preference.";
metalTypeAndText['Rose Gold'] = "<strong>Rose-gold:</strong> Glowing and rich, rose gold adds more color to the jewelry piece. If buying as a gift, please note that women generally have a clear preference for Rose or Yellow Gold - see what other jewelry she has to determine her preference. ";
metalTypeAndText['Platinum'] = "<strong>Platinum:</strong> The most durable and premium metal for fine jewelry.  Platinum feels more substantial to wear due to its greater weight.";


function cj_metalshorttofull(code)
{
	switch(code){
		case "SL":
		return "Silver";
		case "PT":
		return "Platinum";
		case "YG":
		return "14k Yellow Gold";
		case "WG":
		return "14k White Gold";
		case "RG":
		return "14k Rose Gold";
	}
}
function cj_metalcodetofull(code)
{
	switch(code){
		case "SL":
		return "Silver";
		case "PT":
		return "Platinum";
		case "YG":
		return "Yellow Gold";
		case "WG":
		return "White Gold";
		case "RG":
		return "Rose Gold";
	}
}
function cj_getMetalCodeForImage(txt)
{
	if(txt.indexOf('Platinum') > -1)
	{
		return 'WG';
	}
	else if(txt.indexOf('Rose Gold') > -1)
	{
		return 'RG';
	}
	else if(txt.indexOf('Silver') > -1)
	{
		return 'WG';
	}
	else if(txt.indexOf('White Gold') > -1)
	{
		return 'WG';
	}
	else if(txt.indexOf('Yellow Gold') > -1)
	{
		return 'YG';
	}
}
function cj_getMetalCodeForPrice(txt)
{
	if(txt.indexOf('Platinum') > -1)
	{
		return 'PT';
	}
	else if(txt.indexOf('Rose Gold') > -1)
	{
		return 'WG';
	}
	else if(txt.indexOf('Silver') > -1)
	{
		return 'SL';
	}
	else if(txt.indexOf('White Gold') > -1)
	{
		return 'WG';
	}
	else if(txt.indexOf('Yellow Gold') > -1)
	{
		return 'WG';
	}
}
function isIEcheck()
{
	browser = navigator.appName;
	if(browser == 'Microsoft Internet Explorer')
	{
		return true;
	}
	return false;
}
function getoptionindex(name)
{
	var i_hp=-1;
	for(i_hp=0; i_hp<arroptions.length;i_hp++)
	{
		if(arroptions[i_hp][1]==name)
		{
			return i_hp;
		}
	}
	return -1;
}
function ringsizetransfer()
{
	i_hp = getoptionindex('Ring Size');
	if(i_hp != -1)
	{
		optionid = arroptions[i_hp][2];
		var html = document.getElementById('select_' + optionid).parentNode.innerHTML;
		document.getElementById('select_' + optionid).parentNode.innerHTML = '';
		document.getElementById('ringsizediv').innerHTML=html;	
	}
	else
	{
		if(document.getElementById('ringsizemaindiv'))
			document.getElementById('ringsizemaindiv').style.display = 'none';
	}
}
function cj_generatehtml()
{
	defaultMetalType = curmetal;
	fillQualityArr();
	fillStoneSizeArr();
	fillMetalTypeArr();
	fillStoneCaratArr();
	cj_generateGradeHtml();
	cj_generateMetalTypeHtml();
	cj_generateStoneSizeHtml();
	cj_stoneSizeSync();
	cj_StoneSizeTextSync();
	//changeOptions('','');
}

jQuery(function(){
	cj_urls = Array();
	cj_urlData = Array();
	//cj_preloadImages();
	if(typeof(s) != 'undefined'){
		s.eVar18 = curStoneSize;
		if(curQualityGrade == 'A'){
			s.eVar19 = 'good';
		}
		else if(curQualityGrade == 'AA'){
			s.eVar19 = 'better';
		}
		else if(curQualityGrade == 'AAA'){
			s.eVar19 = 'best';
		}
		else if(curQualityGrade == 'AAAA'){
			s.eVar19 = 'heirloom';
		}
		s.eVar20 = curMetalType;
	}
})

function cj_preloadImages(){
	for(i=0;i<qualityArr.length;i++){
		//console.log(qualityArr[i])
		//changeOptions('quality', qualityArr[i], true);
		//changeOptions('metaltype','White Gold', true);
		//changeOptions('metaltype','Yellow Gold', true);
		/*jQuery.post('/hprcv/index/getimages/',{id:curid,sku:cursku,metaltype:"White Gold",grade:qualityArr[i]},function(data){
			updateimageaction(data,ajaxqueue,true)
		});*/
		//cj_getImagesOnce("White Gold",qualityArr[i], true);
		//cj_getImagesOnce("Yellow Gold",qualityArr[i], true);
	}
}

function cj_getImagesOnce(metal,grade,size, angle, behindTheScene){
	if(metal == "Silver" || metal == "Platinum"){
		metal = "White Gold";
	}
	
	changeProductVariation(metal, grade, size, angle);
	/*if(jQuery.inArray(metal+grade,cj_urls) != -1){
		updateimageaction(cj_urlData[metal+grade],ajaxqueue,behindTheScene);
	}
	else{
		jQuery.post('/hprcv/index/getimages/id/'+curid+'/sku/'+cursku+'/metaltype/'+metal+'/grade/'+grade,function(data){
			cj_urls.push(metal+grade);
			cj_urlData[metal+grade] = data;
			updateimageaction(data,ajaxqueue,behindTheScene);
		});
	}*/
}

function cj_generateGradeHtml()
{
	var str = "";
	for(i=0;i<qualityArr.length;i++)
	{		
		str = str + "<div class=\"customize-block\" id=\"quality-" + i + "\" onclick=\"changeOptions('quality','" + qualityArr[i] + "')\"><span class=\"custom-option-img\"><img src='"+generateImagePath('', qualityArr[i], '', 1, 'stone-quality')+"' /></span><span class='custom-option-value'>" + gradeAndSmallText[qualityArr[i]] + "</span></div>";
	}
	str = str + "<div style=\"clear:both\"></div>";
	document.getElementById('qualitybox').innerHTML = str;
	//<div class="qualitytype qualitytype-selected" id="good-qualitytype"><div><span class="qualitytype-image good-stone-sapphire"></span></div>Good</div>
	if(qualityArr.length<2)
	{
		document.getElementById('qualitymaindiv').style.display = 'none';
		curQualityGrade = defaultQualityGrade;
		curStoneSize = defaultStoneSize;
		selectOptionGradeAndStoneSize(defaultQualityGrade,defaultStoneSize);
		return false;
	}
	else
	{
		//default selection
		for(i=0;i<qualityArr.length;i++)
		{
			if(qualityArr[i] == defaultQualityGrade)
			{
				document.getElementById('quality-' + i).className = "customize-block-active";
			}
		}
		curQualityGrade = defaultQualityGrade;
		curStoneSize = defaultStoneSize;
		selectOptionGradeAndStoneSize(defaultQualityGrade,defaultStoneSize);
	}
}
function cj_generateStoneSizeHtml()
{
	
	var str = "";
	var imgsrc = "";
	for(var i=0;i<stoneSizeArr.length;i++)
	{
		var crtWt = '';
		for(stoneIndex in stoneVariationCarats){
			if(stoneVariationCarats[stoneIndex]['index'] == 'Emb2' && stoneVariationCarats[stoneIndex]['size'] == stoneSizeArr[i]){
				crtWt = stoneVariationCarats[stoneIndex]['cts'];
				crtWt = (crtWt == '1')? parseFloat(crtWt).toFixed(2) + ' carat': parseFloat(crtWt).toFixed(2) + ' carats';
			}
		}
		//str = str + "<div class=\"customize-block\" id=\"stonesize-" + i + "\" onclick=\"changeOptions('stonesize','" + stoneSizeArr[i] + "')\"><span class=\"custom-option-img\"><img class=\"stone-size-option\" align=\"center\" src='"+generateImagePath(curMetalType, curQualityGrade, stoneSizeArr[i], 1, 'thumb', 'jpg')+"' id='stonesizeimg" + i + "' /></span><span class='stoneinmm custom-option-value'>" + stoneSizeArr[i] + " mm</span></div>";
		str = str + "<div class=\"customize-block\" id=\"stonesize-" + i + "\" onclick=\"changeOptions('stonesize','" + stoneSizeArr[i] + "')\"><span class=\"custom-option-img\"><img class=\"stone-size-option\" align=\"center\" src='"+generateImagePath(curMetalType, curQualityGrade, stoneSizeArr[i], 1, 'thumb', 'jpg')+"' id='stonesizeimg" + i + "' /></span><span class='stoneinmm custom-option-value'>" + crtWt + "</span></div>";
	}

	str = str + "<div style=\"clear:both\"></div>";
	document.getElementById('stonesizebox').innerHTML = str;
	
	if(stoneSizeArr.length<2)
	{
		document.getElementById('stonesizemaindiv').style.display = 'none';
		return false;
	}
	//default selection
	for(i=0;i<stoneSizeArr.length;i++)
	{
		if(stoneSizeArr[i] == defaultStoneSize)
		{
			document.getElementById('stonesize-' + i).className = "customize-block-active";
		}
	}
}
function cj_generateMetalTypeHtml()
{
	var str = "";
	for(i=0;i<metalTypeArr.length;i++)
	{
		str = str + "<div class=\"customize-block\" id=\"metaltype-" + i + "\" onclick=\"changeOptions('metaltype','" + metalTypeArr[i] + "')\"><span class=\"custom-option-img metaltype-" + metalTextShort[metalTypeArr[i]] + "\"></span><span class='custom-option-value'>" + metalTypeArr[i] + "</span></div>";
	}
	str = str + "<div style=\"clear:both\"></div>";
	document.getElementById('metaltypebox').innerHTML = str;
	//<div class="metaltype" id="silver-metal"><div><span class="metaltype-image"></span></div>Silver</div>
	if(metalTypeArr.length < 2)
	{
		document.getElementById('metaltypemaindiv').style.display = 'none';
		//default selection
		for(i=0;i<metalTypeArr.length;i++)
		{
			if(metalTypeArr[i] == defaultMetalType)
			{
				document.getElementById('metaltype-' + i).className = "customize-block-active";
			}
		}
		curMetalType = defaultMetalType;
		selectOptionMetalType(defaultMetalType,defaultStoneSize);
		
		return false;
	}
	else
	{
		//default selection
		for(i=0;i<metalTypeArr.length;i++)
		{
			if(metalTypeArr[i] == defaultMetalType)
			{
				document.getElementById('metaltype-' + i).className = "customize-block-active";
			}
		}
		curMetalType = defaultMetalType;
		selectOptionMetalType(defaultMetalType,defaultStoneSize);
	}
}
function fillQualityArr()
{
	var str = '';
	i_hp = getoptionindex('Stone Quality');
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			str = arroptions[i_hp][parseInt(j) + 2];
			var arr = new Array();
			arr = str.split('|');
			var fl = 0;
			for(i=0;i<qualityArr.length;i++)
			{
				if(qualityArr[i] == arr[0])
				{
					fl = 1;
				}
			}
			if(fl != 1)
			{
				qualityArr[qualityArr.length] = arr[0];
			}
			if(arroptions[i_hp][parseInt(j) + 4] == "0")
			{
				defaultStoneSize = arr[1];
				defaultQualityGrade = arr[0];
			}
		}
	}
	qualityArr.sort();
}
function fillStoneSizeArr()
{
	var str = '';
	i_hp = getoptionindex('Stone Size');
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			stoneSizeArr[stoneSizeArr.length] = arroptions[i_hp][parseInt(j) + 2];
			//console.dir()
		}
	}
}
function fillStoneCaratArr()
{
	var str = '';
	i_hp = getoptionindex('weight-variation');
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			//stoneSizeArr[stoneSizeArr.length] = 
			var str=arroptions[i_hp][parseInt(j) + 2];
			var arr=new Array();
			arr=str.split('|');
			var arr1=new Array();
			arr1=arr[1].split('-');
			
			var tmpStoneArr = new Array();
			tmpStoneArr['index'] = arr1[0];
			tmpStoneArr['size'] = arr[0];
			tmpStoneArr['cts'] = arr1[1];
			stoneVariationCarats.push(tmpStoneArr);
			if(arr1[0] == 'Emb2'){
				stoneCaratArr[stoneCaratArr.length] = arr1[1];
				stoneSizeArr1[stoneSizeArr1.length] = arr[0];
			}
			//console.dir()
		}
	}
}

function fillMetalTypeArr()
{
	var str = '';
	i_hp = getoptionindex('Metal Type');
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			str = arroptions[i_hp][parseInt(j) + 2];
			var arr = new Array();
			arr = str.split('|');
			var fl = 0;
			for(i=0;i<metalTypeArr.length;i++)
			{
				if(metalTypeArr[i] == arr[0])
				{
					fl = 1;
				}
			}
			if(fl != 1)
			{
				metalTypeArr[metalTypeArr.length] = arr[0];
			}
		}
	}
}
function selectOptionGradeAndStoneSize(cur_quality,cur_stone_size)
{
	var str = cur_quality + '|' + cur_stone_size;
	i_hp = getoptionindex('Stone Quality');
	if(qualityArr.length > 0)	// its taken as greater than one because if there is only one option available than that should not be selected
	{
		optionid = arroptions[i_hp][2];
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			if(arroptions[i_hp][parseInt(j) + 2] == str)
			{
				document.getElementById('select_' + optionid).value = arroptions[i_hp][j];
			}
		}
	}
	if(stoneSizeArr.length > 0)// its taken as greater than one because if there is only one option available than that should not be selected
	{
		str = cur_stone_size;
		i_hp = getoptionindex('Stone Size');
		optionid = arroptions[i_hp][2];
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			if(arroptions[i_hp][parseInt(j) + 2] == str)
			{
				document.getElementById('select_' + optionid).value = arroptions[i_hp][j];
			}
		}
	}
	if(gradeAndText[curstone]){
		if(document.getElementById('qualitydescription'))
			document.getElementById('qualitydescription').innerHTML = gradeAndText[curstone][curQualityGrade];
	}
	//document.getElementById('gemstonesizedesc').innerHTML = cur_stone_size;
	
	cj_gradeSync();
}
function selectOptionMetalType(cur_metal_type,cur_stone_size)
{
	var str = cur_metal_type + '|' + cur_stone_size;
	i_hp = getoptionindex('Metal Type');
	if(metalTypeArr.length > 1)// its taken as greater than one because if there is only one option available than that should not be selected
	{
		optionid = arroptions[i_hp][2];
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			if(arroptions[i_hp][parseInt(j) + 2] == str)
			{
				document.getElementById('select_' + optionid).value = arroptions[i_hp][j];
			}
		}
	}
	if(document.getElementById('metaldescription'))
		document.getElementById('metaldescription').innerHTML = metalTypeAndText[curMetalType];
	if(document.getElementById('metal-desc'))
	{
		document.getElementById('metal-desc').innerHTML = cj_metalshorttofull(metalTextShort[curMetalType]);
	}
	if(document.getElementById('cert_metal'))
	{
		document.getElementById('cert_metal').value = cj_metalshorttofull(metalTextShort[curMetalType]);
	}
}
function changeOptions(type,value)
{
	if(type == 'quality')
	{
		curQualityGrade = value;
		for(i=0;i<qualityArr.length;i++)
		{
			if(qualityArr[i] == curQualityGrade)
			{
				document.getElementById('quality-' + i).className = "customize-block-active";
			}
			else
			{
				document.getElementById('quality-' + i).className = "customize-block";
			}
		}
		
		if(typeof(s) != 'undefined'){
			gemstoneQualitySelect(curQualityGrade);
		}
		
		selectOptionGradeAndStoneSize(curQualityGrade,curStoneSize);
		loadStoneReviews(curQualityGrade)
	}
	else if(type == 'stonesize')
	{
		curStoneSize = value;
		for(i=0;i<stoneSizeArr.length;i++)
		{
			if(stoneSizeArr[i] == curStoneSize)
			{
				document.getElementById('stonesize-' + i).className = "customize-block-active";
			}
			else
			{
				document.getElementById('stonesize-' + i).className = "customize-block";
			}
		}
		
		if(typeof(s) != 'undefined'){
			gemstoneSizeSelect(curStoneSize);
		}
		
		selectOptionGradeAndStoneSize(curQualityGrade,curStoneSize);
		selectOptionMetalType(curMetalType,curStoneSize);
		cj_stoneSizeSync();
	}
	else if(type == 'metaltype')
	{
		curMetalType = value;
		// alert(curMetalType);
		switch(curMetalType)
		{
			case 'Silver':
				jQuery('#pendantmsg').html('Silver');
				break;
			case 'White Gold':
				jQuery('#pendantmsg').html('14K White Gold');				
				break;
			case 'Yellow Gold':
				jQuery('#pendantmsg').html('14K Yellow Gold');
				break;
			case 'Platinum':
				jQuery('#pendantmsg').html('Platinum');
				break;
			default:
				jQuery('#pendantmsg').html('');
		}		
		for(i=0;i<metalTypeArr.length;i++)
		{
			if(metalTypeArr[i] == curMetalType)
			{
				document.getElementById('metaltype-' + i).className = "customize-block-active";
			}
			else
			{
				document.getElementById('metaltype-' + i).className = "customize-block";
			}
		}
		
		if(typeof(s) != 'undefined'){
			metalTypeSelect(curMetalType);
		}
		
		selectOptionMetalType(curMetalType,curStoneSize);
	}
	
	cj_updateimage();
	cj_variationSync();
	cj_pricefactorsync();
	cj_shipdateSync();
	opConfig.reloadPrice();
}
function cj_shipdateSync()
{
	if(defaultMetalType == curMetalType)
	{
		document.getElementById('expected-shipping-date-original').style.display = 'block';
		document.getElementById('expected-shipping-date-extended').style.display = 'none';
	}
	else
	{
		document.getElementById('expected-shipping-date-original').style.display = 'none';
		document.getElementById('expected-shipping-date-extended').style.display = 'block';
	}
}
function cj_pricefactorsync()
{
	var mt = cj_getMetalCodeForPrice(curMetalType);
	// start code added by anil due to curQualityGrade and curStoneSize value empty
	if(curQualityGrade==''){
		curQualityGrade	= defaultQualityGrade;
	}
	if(curStoneSize==''){
		curStoneSize = defaultStoneSize;
	}	
	// end code added by anil due to curQualityGrade and curStoneSize value empty	
	var str = mt + '-' + curQualityGrade + '-' + curStoneSize;
	i_hp = getoptionindex('Price Factor');
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			if(str == arroptions[i_hp][parseInt(j) + 2])
			{
				document.getElementById('select_' + optionid).value = arroptions[i_hp][j];
			}
		}
	}
}

var ajaxqueue = 1;
function cj_updateimage()
{
	if(curQualityGrade==''){
		curQualityGrade	= defaultQualityGrade;
	}
	if(curStoneSize==''){
		curStoneSize = defaultStoneSize;
	}	

	cj_getImagesOnce(curMetalType, curQualityGrade, curStoneSize, curAngle, false);
}
function updateimageaction(data,ifl,behindTheScene)
{
	if(ifl != ajaxqueue)
	{
		return;
	}
	var arr = new Array();
	arr = data.split('|');
	for(var i=1;i<arr.length;i++)
	{
		if(behindTheScene){
			if(arr[i].indexOf("angle_1") > -1){
				jQuery('<img src="'+arr[i]+'">').appendTo(jQuery('.no-display'));
			}
		}
		else{
			if(i==curAngle)
			{
				$('image').src = arr[i];
			}
		}
		
		if(behindTheScene){
			jQuery('<img src="/image_thumb.php?width=56&height=56&image=' + arr[i] + '">').appendTo(jQuery('.no-display'));
		} else {
			jQuery('#angle_' + i)
				.attr('src','/image_thumb.php?width=56&height=56&image=' + arr[i])
				.attr('onclick','$("image").src = "' + arr[i] + '";changecurrentview("' + i + '");return false;');
		}
		
		/*var par = document.getElementById('angle_' + i).parentNode;
		var str = "<img onclick=\"$('image').src = '" + arr[i] + "';changecurrentview('" + i + "');return false;\" src=\"/image_thumb.php?width=56&height=56&image=" + arr[i] + "\" width=\"56\" height=\"56\" alt=\"\" id=\"angle_" + i + "\"/>";
		par.innerHTML = str;*/
	}
	cnti = stoneSizeArr.length - 1;
	wd = 23;
	while(document.getElementById('stonesizeimg' + cnti))
	{
		if(behindTheScene){
			jQuery('<img src="/image_thumb.php?width='+ wd +'&height='+ wd +'&image=' + arr[1] + '">').appendTo(jQuery('.no-display'));
		}else{
			document.getElementById('stonesizeimg' + cnti).src = "/image_thumb.php?width="+ wd +"&height="+ wd +"&image=" + arr[1];
			if(document.getElementById('cert_image'))
				document.getElementById('cert_image').value = arr[1];
		}
		wd= wd-4;
		cnti--;
	}
}
function changecurrentview(i)
{
	curAngle = i;
}
function cj_stoneSizeSync()
{
	//curStoneSize;
}
function cj_countSync()
{
	var str = '';
	var strcert = '';
	i_hp = getoptionindex('count-variation');
	
	if(jQuery('#Emb1-stone-detail-box').length > 0){
		jQuery('#Emb1-stone-detail-box').hide();
	}
	if(jQuery('#Emb2-stone-detail-box').length > 0){
		jQuery('#Emb2-stone-detail-box').hide();
	}
	if(jQuery('#Emb3-stone-detail-box').length > 0){
		jQuery('#Emb3-stone-detail-box').hide();
	}
	
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			str = arroptions[i_hp][parseInt(j) + 2];
			var arr = new Array();
			arr = str.split('|');
			if(arr[0]==curStoneSize)
			{
				var arr1 = new Array();
				arr1 = arr[1].split('-');
				
				jQuery('#'+arr1[0]+'-stone-detail-box').show();
				
				if(document.getElementById(arr1[0] + '-count')){
				document.getElementById(arr1[0] + '-count').innerHTML = arr1[1];}
				if(strcert=="")
				{
					strcert = arr1[0] + ',' + arr1[1];
				}
				else
				{
					strcert = strcert + "|" + arr1[0] + ',' + arr1[1];
				}
			}
		}
	}
	if(document.getElementById('cert_sync_count'))
		document.getElementById('cert_sync_count').value = strcert;
}
function cj_sizeSync()
{
	var str = '';
	var strcert = '';
	i_hp = getoptionindex('size-variation');
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			str = arroptions[i_hp][parseInt(j) + 2];
			var arr = new Array();
			arr = str.split('|');
			if(arr[0]==curStoneSize)
			{
				var arr1 = new Array();
				arr1 = arr[1].split('-');
				if(document.getElementById(arr1[0] + '-size')){
				document.getElementById(arr1[0] + '-size').innerHTML = arr1[1] + ' mm';}
				if(strcert=="")
				{
					strcert = arr1[0] + ',' + arr1[1];
				}
				else
				{
					strcert = strcert + "|" + arr1[0] + ',' + arr1[1];
				}
			}
		}
	}
	if(document.getElementById('cert_sync_size'))
		document.getElementById('cert_sync_size').value = strcert;
}
function cj_weightSync()
{
	var str = '';
	var strcert = '';
	i_hp = getoptionindex('weight-variation');
	if(i_hp > -1)
	{
		optionid = arroptions[i_hp][2];
		
		for(j=3;j<arroptions[i_hp].length;j=j+5)
		{
			str = arroptions[i_hp][parseInt(j) + 2];
			var arr = new Array();
			arr = str.split('|');
			if(arr[0]==curStoneSize)
			{
				var arr1 = new Array();
				arr1 = arr[1].split('-');
				if(document.getElementById(arr1[0] + '-weight')){
				document.getElementById(arr1[0] + '-weight').innerHTML = arr1[1] + ' cts';}
			}
		}
	}
	if(document.getElementById('cert_sync_weight'))
		document.getElementById('cert_sync_weight').value = strcert;
}
function cj_gradeSync()
{
	var qlty = curQualityGrade;
	if(qlty=='LC')
	{
		qlty = 'Lab Created';
	}
	
	if(document.getElementById('Emb1-grade'))
	{
		if(qlty=='A' || qlty=='AA' || qlty=='AAA' || qlty=='AAAA'){
			document.getElementById('Emb1-grade').innerHTML = 'Natural '+qlty;
		}else{	
			document.getElementById('Emb1-grade').innerHTML = qlty;
		}
	}
	if(document.getElementById('Emb2-grade'))
	{
		if(qlty=='A' || qlty=='AA' || qlty=='AAA' || qlty=='AAAA'){
			document.getElementById('Emb2-grade').innerHTML = 'Natural '+qlty;
		}else{	
			document.getElementById('Emb2-grade').innerHTML = qlty;
		}
	}
	if(document.getElementById('Emb3-grade'))
	{
		if(qlty=='A' || qlty=='AA' || qlty=='AAA' || qlty=='AAAA'){
			document.getElementById('Emb3-grade').innerHTML = 'Natural '+qlty;
		}else{	
			document.getElementById('Emb3-grade').innerHTML = qlty;
		}
	}
	if(document.getElementById('cert_quality'))
	{
		document.getElementById('cert_quality').value = qlty;
	}
}
function cj_StoneSizeTextSync()
{
	var str = "<strong>Approximate Gemstone Carat Weight: </strong>";
	var mainStoneText = "";
	var sideStoneText = "";
	
	for(stoneIndex in stoneVariationCarats){
		var crtWt = '';
		if(stoneVariationCarats[stoneIndex]['index'] == 'Emb2' && stoneVariationCarats[stoneIndex]['size'] == curStoneSize){
			crtWt = stoneVariationCarats[stoneIndex]['cts'];
			crtWt = (crtWt == '1')? parseFloat(crtWt).toFixed(2) + ' carat': parseFloat(crtWt).toFixed(2) + ' carats';
			mainStoneText = parseFloat(crtWt).toFixed(2) + ' carats of ' + embStoneName2;
		}
		if(stoneVariationCarats[stoneIndex]['index'] == 'Emb1' && stoneVariationCarats[stoneIndex]['size'] == curStoneSize){
			crtWt = stoneVariationCarats[stoneIndex]['cts'];
			crtWt = (crtWt == '1')? parseFloat(crtWt).toFixed(2) + ' carat': parseFloat(crtWt).toFixed(2) + ' carats';
			sideStoneText += ', '+ parseFloat(crtWt).toFixed(2) + ' carats of ' + embStoneName;
		}
		if(stoneVariationCarats[stoneIndex]['index'] == 'Emb3' && stoneVariationCarats[stoneIndex]['size'] == curStoneSize){
			crtWt = stoneVariationCarats[stoneIndex]['cts'];
			crtWt = (crtWt == '1')? parseFloat(crtWt).toFixed(2) + ' carat': parseFloat(crtWt).toFixed(2) + ' carats';
			sideStoneText += ', ' + parseFloat(crtWt).toFixed(2) + ' carats of ' + embStoneName3;
		}
	}
	
	if(document.getElementById('gemstonesizedesc'))
		document.getElementById('gemstonesizedesc').innerHTML = str + mainStoneText + sideStoneText + ".";
}
function cj_variationSync()
{
	cj_countSync();
	cj_sizeSync();
	cj_weightSync();
	cj_StoneSizeTextSync();
	i_hp = getoptionindex('VariationInfoToOrder');
	if(i_hp>-1 && document.getElementById('cert_sync_size')){
		optionid = arroptions[i_hp][2];
		var str = "cert_sync_count!" + document.getElementById('cert_sync_count').value + "#cert_sync_size!" + document.getElementById('cert_sync_size').value + "#cert_sync_weight!" + document.getElementById('cert_sync_weight').value;
		document.getElementById('options_' + optionid + '_text').value = str;
	}
}
function qualitypopupclick(curstone,curshape){
	rnd_num = parseInt(Math.random()*9999999);
	popupstonetype = curstone;
	popupstoneshape = curshape;
	jQuery('.view-more-popup-btn').show();
	jQuery('.view-more-popup').hide();
	jQuery('.popupboxnew').hide().html('');
	jQuery('#qualitypopup').show();
	jQuery.ajax({
	url:'/hprcv/qualitycompare/get/?stonetype='+popupstonetype+'&stoneshape=' + popupstoneshape + '&rnd=' + rnd_num,
	success: function(matter){
			jQuery('#qualitypopup').html(matter);
			jQuery('#qualitypopup').data('loaded','yes');
			jQuery('.popupboxcross').click(function(){
				jQuery('.popupboxnew').hide().html('');
			})
			
			jQuery('.view-more-popup-btn').click(function(){
				jQuery(this).parent().find('.view-more-popup').show();
				jQuery(this).hide();
			})
		}
	})	
}
function weightpopupclick(curstone,curshape){
	rnd_num = parseInt(Math.random()*9999999);
	popupstonetype = curstone;
	popupstoneshape = curshape;
	jQuery('.popupboxnew').hide().html('');
	jQuery('#roundstonepopup').show();
	//if(jQuery('#roundstonepopup').data('loaded')!='yes'){
		jQuery.ajax({
		url:'/hprcv/qualitycompare/getweightchart/?stonetype='+popupstonetype+'&stoneshape=' + popupstoneshape + '&rnd=' + rnd_num,
		success: function(matter){
				jQuery('#roundstonepopup').html(matter);
				jQuery('#roundstonepopup').data('loaded','yes');
				
				jQuery('.popupboxcross').click(function(){
					jQuery('.popupboxnew').hide().html('');
					
				}
				)
				
			}
		})
	//}	
}
function transferupWeightPopup(stonename,stoneshape)
{
	//document.getElementById('sizechartupperlink').innerHTML = "<div onclick=\"weightpopupclick('" + stonename + "','" + stoneshape + "')\">View Size Chart</div>";
}



/* Added functions for ultimate theme */

function generateImagePath(metal, grade, size, angle, imageType, fileType){
	if(!imageType){
		imageType = 'main';
	}
	if(!fileType){
		fileType = 'png';
	}
	if(metal == "Silver" || metal == "Platinum" || metal == "White Gold"){
		metal = "WG";
	}
	else if(metal == 'Yellow Gold'){
		metal = "YG";
	}
	
	size = size.split(' ').join('');
	if(typeof(cursku)!='undefined'){
		if(imageType == 'stone-quality'){
			return '/media/catalog/product/images/ultimate/'+cursku+'/'+imageType+'/'+grade+'.png';
		}
		return '/media/catalog/product/images/ultimate/'+cursku+'/'+imageType+'/'+cursku+'-angle'+angle+'-'+metal+'-'+grade+'-'+size+'.'+fileType;
	}else{
		if(typeof(console)!='undefined')
			console.log('Eror in image processing.');
		return '/media/catalog/product/placeholder/default/ANGARA-place-holder.jpg';
	}
}

function changeProductVariation(metal, grade, size, angle){
	
	// main image change
	if(jQuery('#image').attr('src') != generateImagePath(metal, grade, size, angle)){
		jQuery('#big-product-img-loader').show();
		jQuery('#image').attr('src',generateImagePath(metal, grade, size, angle)).load(function(){
			jQuery('#big-product-img-loader').hide();
		});
	}
	
	
	// stone size option image change
	jQuery('')
	for(var i=0;i<stoneSizeArr.length;i++)
	{
		jQuery('#stonesizeimg' + i).attr('src',generateImagePath(curMetalType, curQualityGrade, stoneSizeArr[i], 1, 'thumb', 'jpg'));
	}
	
	jQuery('.product-angle-view-image').each(function(i){
		var angleNum = jQuery(this).attr('angle');
		jQuery(this).find('img').attr('src',generateImagePath(metal, grade, size, angleNum, 'thumb', 'jpg'));
	});
}

jQuery(function(){
	jQuery('.product-angle-view-image').each(function(i){
		var angleNum = jQuery(this).attr('angle');
		jQuery(this).find('img').click(function(){
			var angularImage = jQuery(this).attr('src');
			angularImage = angularImage.replace(/\/thumb\//,'\/main\/').replace(/\.jpg/,'.png');
			if(jQuery('#image').attr('src') != angularImage){
				jQuery('#big-product-img-loader').show();
				jQuery('#image').attr('src',angularImage).load(function(){
					jQuery('#big-product-img-loader').hide();
				});
			}
		});
	});
})

/* End functions for ultimate theme */