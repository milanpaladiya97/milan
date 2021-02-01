jQuery(document).ready(function () {
	jQuery('li.es-card').first().addClass('active');
	jQuery('.control_next').on( 'click', function(){
	  console.log(999);
	  jQuery('.es-card').next().addClass('active');

	});
});