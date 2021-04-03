require(['jquery'], function($)
{ 
	var width = $(window).width();
       if (width < 991){
        $('.header_phone_number_section').wrapInner('<div class="caption">');
           $('.header_phone_number_section').click(function() {
	       $(this).toggleClass('open');
	});
    }
    
});