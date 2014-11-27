jQuery(document).ready( function() {
	jQuery('.grid-view .zoomin,.grid-view .grid-date').hide();
	jQuery('.grid-view').hover(function () {
		jQuery(this).children().children('.zoomin').show();
		jQuery(this).children().children('.grid-date').slideDown('fast');
	}, 
	function () {
		jQuery(this).children().children('.zoomin').hide();
		jQuery(this).children().children('.grid-date').slideUp('fast');
	});
});