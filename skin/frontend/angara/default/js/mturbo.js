var mturboloader = {
		
	url: '',
    blocks: new Array(),
    complete: false,
    cartLinkCss: '',
    blocksContents: new Array(),
    
    addBlockRequest: function(blockIdentifier) {
    	mturboloader.blocks.push(blockIdentifier);
    },
    
    getBlock: function (blockIdentifier) {
		return mturboloader.blocksContents[blockIdentifier];
    },
        
    loadBlocks: function(url, referer) {
    	new Ajax.Request(url, {
    		method: "get",
    		parameters: {"identifier[]": mturboloader.blocks },
    		onSuccess: 
    			function(transport) {

    				mturboloader.blocksContents = transport.responseText.evalJSON();

      				mturboloader.complete = true;
      				setTimeout('updateCartLink()', 100);
      			}
    	});
    }
    
}

function updateCartLink() {
	
	if (mturboloader.cartLinkCss)
	{
		if (mturboloader.blocksContents['cartlink'])
		{
			var cssSelectors = mturboloader.cartLinkCss.split(',');
			
			cssSelectors.each(function(cssSelector) {
				$$(cssSelector).each(function(element) {
					element.innerHTML = mturboloader.blocksContents['cartlink'];
				});
			});
		} 
		else
		{
			setTimeout('updateCartLink()', 100);
	        return;
		}
	}
	
}