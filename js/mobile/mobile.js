jQuery(window).scroll(function() {
    var scrollTop     = jQuery(window).scrollTop();
    var elementOffset = jQuery('.checkout-types').offset().top;
    var distance      = (elementOffset - scrollTop);
    if(distance<(jQuery(window).height())){
        jQuery('.fixed-container').css('display','none');
    }
    else jQuery('.fixed-container').css('display','block');
});