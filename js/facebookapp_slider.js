
(function() {
    
// EM.tools {{{
    
if (typeof BLANK_IMG == 'undefined') 
    var BLANK_IMG = '';

// declare namespace() method
String.prototype.namespace = function(separator) {
  this.split(separator || '.').inject(window, function(parent, child) {
    var o = parent[child] = { }; return o;
  });
};


'EM.tools'.namespace();



    
function decorateSlideshow() {
    var $$li = $$('#slideshow ul li');
    if ($$li.length > 0) {
        
        // reset UL's width
        var ul = $$('#slideshow ul')[0];
        var w = 0;
        $$li.each(function(li) {
            w += li.getWidth();
        });
        ul.setStyle({'width':w+'px'});
        
        // private variables
        var previous = $$('#slideshow a.previous')[0];
        var next = $$('#slideshow a.next')[0];
        var num = 1;
        var width = ul.down().getWidth() * num;
        var slidePeriod = 1; // seconds
        var manualSliding = false;
        
        // next slide
        function nextSlide() {
            new Effect.Move(ul, { 
                x: -width,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                afterFinish: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ bottom: ul.down() });
                    ul.setStyle('left:0');
                }
            });
        }
        
        // previous slide
        function previousSlide() {
            new Effect.Move(ul, { 
                x: width,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                beforeSetup: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ top: ul.down('li:last-child') });
                    ul.setStyle({'position': 'relative', 'left': -width+'px'});
                }
            });
        }
        
        function startSliding() {
            sliding = true;
        }
        
        function stopSliding() {
            sliding = false;
        }
        
        // bind next button's onlick event
        next.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            nextSlide();
        });
        
        // bind previous button's onclick event
        previous.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            previousSlide();
        });
        
        
        // auto run slideshow
        new PeriodicalExecuter(function() {
            if (!manualSliding) previousSlide();
            manualSliding = false;
        }, slidePeriod);
        
    }
}
function decorateSlideshow4() {
    var $$li = $$('#slideshow4 ul li');
    if ($$li.length > 0) {
        
        // reset UL's width
        var ul = $$('#slideshow4 ul')[0];
        var w = 0;
        $$li.each(function(li) {
            w += li.getWidth();
        });
        ul.setStyle({'width':w+'px'});
        
        // private variables
        var previous = $$('#slideshow4 a.previous')[0];
        var next = $$('#slideshow4 a.next')[0];
        var num = 1;
        var width = ul.down().getWidth() * num;
        var slidePeriod = 1; // seconds
        var manualSliding = false;
        
        // next slide
        function nextSlide() {
            new Effect.Move(ul, { 
                x: -width-15,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                afterFinish: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ bottom: ul.down() });
                    ul.setStyle('left:0');
                }
            });
        }
        
        // previous slide
        function previousSlide() {
            new Effect.Move(ul, { 
                x: width,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                beforeSetup: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ top: ul.down('li:last-child') });
                    ul.setStyle({'position': 'relative', 'left': -width+'px'});
                }
            });
        }
        
        function startSliding() {
            sliding = true;
        }
        
        function stopSliding() {
            sliding = false;
        }
        
        // bind next button's onlick event
        next.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            nextSlide();
        });
        
        // bind previous button's onclick event
        previous.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            previousSlide();
        });
        
        
        /*// auto run slideshow
        new PeriodicalExecuter(function() {
            if (!manualSliding) previousSlide();
            manualSliding = false;
        }, slidePeriod);*/
        
    }
}
function decorateSlideshow1() {
    var $$li = $$('#slideshow1 ul li.item');
    if ($$li.length > 0) {
        
        // reset UL's width
        var ul = $$('#slideshow1 ul')[0];
        var w = 0;
        $$li.each(function(li) {
            w += li.getWidth();
        });
        ul.setStyle({'width':w+'px'});
        
        // private variables
        var previous = $$('#slideshow1 a.previous')[0];
        var next = $$('#slideshow1 a.next')[0];
        var num = 1;
        var width = ul.down().getWidth() * num;
        var slidePeriod = 1; // seconds
        var manualSliding = false;
        
        // next slide
        function nextSlide() {
            new Effect.Move(ul, { 
                x: -width-15,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                afterFinish: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ bottom: ul.down() });
                    ul.setStyle('left:0');
                }
            });
        }
        
        // previous slide
        function previousSlide() {
            new Effect.Move(ul, { 
                x: width,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                beforeSetup: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ top: ul.down('li.item:last-child') });
                    ul.setStyle({'position': 'relative', 'left': -width+'px'});
                }
            });
        }
        
        function startSliding() {
            sliding = true;
        }
        
        function stopSliding() {
            sliding = false;
        }
        
        // bind next button's onlick event
        next.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            nextSlide();
        });
        
        // bind previous button's onclick event
        previous.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            previousSlide();
        });
        
        
        // auto run slideshow
    /*  new PeriodicalExecuter(function() {
            if (!manualSliding) previousSlide();
            manualSliding = false;
        }, slidePeriod);*/
        
        
    }
}
function decorateSlideshow2() {

    var $$li = $$('#slideshow2 ul li.item');
    
    if ($$li.length > 0) {
        var ul = $$('#slideshow2 ul')[0];
        var w = 0;
        $$li.each(function(li) {
            w += li.getHeight();
        });
        ul.setStyle({'height':w+'px'});
        
        // private variables
        var previous = $$('#slideshow2 a.previous')[0];
        var next = $$('#slideshow2 a.next')[0];
        var num = 1;
        //alert(ul.down().getHeight()+4);
        var height = (ul.down().getHeight()+8) * num;
        //alert("aaaa"+height);
        var slidePeriod = 1; // seconds
        var manualSliding = false;
    
        function nextSlide() {
            //alert("aaaa"+height);
            new Effect.Move(ul, { 
                //x: -width,
                y:-height-11,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                afterFinish: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ bottom: ul.down() });
                    ul.setStyle('top:0');
                }
            });
        }
        
        // previous slide
        function previousSlide() {
            new Effect.Move(ul, { 
                //x: width,
                y:height,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                beforeSetup: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ top: ul.down('li.item:last-child') });
                    ul.setStyle({'position': 'relative', 'top': -height+'px'});
                }
            });
        }
        
        function startSliding() {
            sliding = true;
        }
        
        function stopSliding() {
            sliding = false;
        }
        
        // bind next button's onlick event
        next.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            nextSlide();
        });
        
        // bind previous button's onclick event
        previous.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            previousSlide();
        });
    }
}
function decorateSlideshow3() {
    var $$li = $$('#slideshow3 ul li');
    if ($$li.length > 5) {
        
        // reset UL's width
        var ul = $$('#slideshow3 ul')[0];
        var w = 0;
        $$li.each(function(li) {
            w += li.getWidth();
        });
        ul.setStyle({'width':w+'px'});
        
        // private variables
        var previous = $$('#slideshow3 a.previous')[0];
        var next = $$('#slideshow3 a.next')[0];
        var num = 1;
        var width = ul.down().getWidth() * num;
        var slidePeriod = 1; // seconds
        var manualSliding = false;
        
        // next slide
        function nextSlide() {
            new Effect.Move(ul, { 
                x: -width-15,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,

                //transition: Effect.Transitions.sinoidal,
                afterFinish: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ bottom: ul.down() });
                    ul.setStyle('left:0');
                }
            });
        } 
        
        // previous slide
        function previousSlide() {
            new Effect.Move(ul, { 
                x: width,
                mode: 'relative',
                queue: 'end',
                duration: 1.0,
                //transition: Effect.Transitions.sinoidal,
                beforeSetup: function() {
                    for (var i = 0; i < num; i++)
                        ul.insert({ top: ul.down('li:last-child') });
                    ul.setStyle({'position': 'relative', 'left': -width+'px'});
                }
            });
        }
        
        function startSliding() {
            sliding = true;
        }
        
        function stopSliding() {
            sliding = false;
        }
        
        // bind next button's onlick event
        next.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            nextSlide();
        });
        
        // bind previous button's onclick event
        previous.observe('click', function(event) {
            Event.stop(event);
            manualSliding = true;
            previousSlide();
        });
        
        
        // auto run slideshow
    /*  new PeriodicalExecuter(function() {
            if (!manualSliding) previousSlide();
            manualSliding = false;
        }, slidePeriod);*/
        
        
    }
}
function menu()
{
    var Width_li=0;
    var Width_before=0;
    var Width_div=0;
    var Width=0;
    var w = 960;
    $$(".menu").each(function(elem) {
        elem.childElements().each(function(li) {
            li.addClassName('submenu');
            Width_li=li.getWidth();
            Width=w-Width_before;
            Width_before+=Width_li;
            $div=li.select('div')[0];
            if(typeof $div != 'undefined'){
                Width_div=$div.getWidth();
                sub=Width_div-Width;
                if(sub>0){
                    $div.addClassName(' position-right')
                    li.addClassName('position-right-li')
                }
            }
        });
        
    });
}
//get Height
function setHeightAutoGridCatagoryDefault($class){
    $$ul=$$($class);
    if($$ul.length>0){
         for($i=0;$i<$$ul.length;$i++){
                $ul=$$ul[$i];
                $height=0;
                $margin_li_top=0;
                $margin_li_bottom=0;
                $padding_li_top=0;
                $padding_li_bottom=0;
                $ul.select("li.item").each(function(li){
                    $li_height=li.getHeight()-6;                    
                    $margin_li_top=li.getStyle('margin-top');
                    $margin_li_bottom=li.getStyle('margin-bottom');
                    $padding_li_top=li.getStyle('padding-top');
                    $padding_li_bottom=li.getStyle('padding-bottom');
                    li.select(".price-box").each(function(p){
                        $p_height=p.getHeight();
                    }); 
                    $li_height=parseInt($li_height)+parseInt($p_height);
                    if($height<$li_height)
                        $height=$li_height;                         
                });             
                $padding=parseInt($padding_li_top)+parseInt($padding_li_bottom);
                $height=$height-$padding;               
                $ul.select("li.item").each(function(li){        
                li.setStyle('height:'+$height+"px");
                });
        }
    }
}


function setHeightBestseller($class){
    $$ul=$$($class);
    $$margin_top=0;
    $$padding_top=0;
    if($$ul.length>0){
         for($i=0;$i<$$ul.length;$i++){
                $ul=$$ul[$i];
                $height=0;
                $ul.select("li.item").each(function(li){
                    if($height<li.getHeight())
                        $height=li.getHeight()-2;
                    li.select(".price-box").each(function(p){
                        $p_height=p.getHeight();
                    }); 
                    $$margin_top=li.getStyle('margin-top');
                    $$padding_top=li.getStyle('padding-top');
                });
                $height_default=parseInt($height)+parseInt($$margin_top)+parseInt($$padding_top)+4; 
                $ul.select("li.item").each(function(li){
                li.setStyle('height:'+$height+"px");
                });
                $("bestseller").setStyle('height:'+$height_default+"px");
        }
    }
}

document.observe("dom:loaded", function() {
    decorateSlideshow();
    decorateSlideshow1();
    decorateSlideshow2();
    decorateSlideshow3();
    decorateSlideshow4();
    menu();
    setHeightAutoGridCatagoryDefault(".widget-products .products-grid");
    setHeightAutoGridCatagoryDefault(".topoffer .products-grid");
    setHeightAutoGridCatagoryDefault(".catalogsearch-result-index .col-main .products-grid");
    setHeightAutoGridCatagoryDefault(".crosssell #crosssell-products-list");
    setHeightAutoGridCatagoryDefault(".tag-product-list .col-main .products-grid");
    
    setHeightAutoGridCatagoryDefault("#upsell-product-table .products-grid");
    //setHeightAutoGridCatagoryDefault(".topoffer .products-grid");
    setHeightAutoGridCatagoryDefault(".catalog-category-view .col-main .category-products .products-grid");
    setHeightBestseller(".bestseller .products-grid");// scroll
});

// }}}

})();
