jQuery(document).ready(function($) {

	$('#gmw-gl-autocomplete').autocomplete({
		
		source: function(request,response) {
		
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode( {
				'address': request.term }, function(results, status) {
				response(jQuery.map(results, function(item) {
					return {
						label: item.formatted_address, 
						value: item.formatted_address, 
						geocode: item                 
					};
				}));
			});
		},
	});

	$('#gmw-gl-autocomplete').bind('keydown', function(event) {
		if(event.keyCode == 13) {
			$('#gmw-gl-autocomplete').autocomplete("disable");
		} else {
			$('#gmw-gl-autocomplete').autocomplete("enable");
		}
	});

});
