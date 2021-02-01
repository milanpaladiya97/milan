jQuery( document ).ready(function($) {
 // Logo Showcase Filter
	if( $('.pgafu-filtr-container').length > 0) {
		jQuery('.pgafu-filtr-container').filterizr({
			selector 	: '.pgafu-filtr-container',
			layout 		: 'sameWidth',
		});
		
		$(document).on('click', '.pgafu-filter li', function(){
			$('.pgafu-filtr-cat').removeClass('pgafu-active-filtr');
			$(this).addClass('pgafu-active-filtr');
		});
	}
});