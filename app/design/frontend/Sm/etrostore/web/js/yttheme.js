jQuery(document).ready(function($){ 
	$('.sm_megamenu_menu > li > .sm-megamenu-child').parent().addClass('parent-item');
	
	var full_width = $('body').innerWidth();
	$('.full-content').css({'width':full_width});

	$( window ).resize(function() {
		var full_width = $('body').innerWidth();
		$('.full-content').css({'width':full_width});
	});
	
	$('body').bind('touchstart', function() {});

    setInterval(function(){
        $('[data-toggle="tooltip"]').tooltip();
    },99)
});