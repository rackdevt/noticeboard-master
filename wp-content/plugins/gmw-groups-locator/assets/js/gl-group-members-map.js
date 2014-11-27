jQuery(window).ready(function($){ 
	
	$('#gmw-gl-group-members-map-wrapper').slideToggle('fast', function() {
		var glgmMap = new google.maps.Map(document.getElementById('gmw-gl-group-members-map'), {
			zoom: 8,
			panControl: true,
	  		zoomControl: true,
	  		mapTypeControl: true,
			center: new google.maps.LatLng(glgmMapArgs.your_lat, glgmMapArgs.your_lng),
			mapTypeId: google.maps.MapTypeId['ROADMAP']
		});	
		
		var latlngbounds = new google.maps.LatLngBounds();
		
		if ( glgmMapArgs.your_lat && glgmMapArgs.your_lng ) {
			var yourLocation  = new google.maps.LatLng(glgmMapArgs.your_lat, glgmMapArgs.your_lng);
			latlngbounds.extend(yourLocation);
			
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(glgmMapArgs.your_lat, glgmMapArgs.your_lng),
				map: glgmMap,
				icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
			});
		}

		var i;
		var gmiw = false;
		gmMarkers = [];
		
		for (i = 0; i < glgmGroups.length; i++) { 
			console.log(glgmGroups[i]);
			if ( glgmGroups[i]['latLng'] != undefined ) {
				var mapIcon, shadow;
			
				var groupLocation = new google.maps.LatLng(glgmGroups[i]['latLng'][0]['lat'], glgmGroups[i]['latLng'][0]['long']);
				latlngbounds.extend(groupLocation);
				
				mapIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ glgmGroups[i]['mc'] +'|FF776B|000000';
				shadow = 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow';
					
				gmMarkers[i] = new google.maps.Marker({
					position: groupLocation,
					icon:mapIcon,
					map:glgmMap,
					shadow: shadow,
					id:i   
				});
			
				with ({ gmMarker: gmMarkers[i] }) {
					google.maps.event.addListener(gmMarker, 'click', function() {
						if (gmiw) {
							gmiw.close();
							gmiw = null;
						}
						gmiw = new google.maps.InfoWindow({
							content: getGMWIContent(gmMarker.id),
						});
						gmiw.open(glgmMap, gmMarker); 		
					});
				}
			}
		}
		
		glgmMap.fitBounds(latlngbounds);
		
		if ( glgmGroups.length == 1 && !glgmMapArgs.your_lat ) {
			blistener = google.maps.event.addListener( ( glgmMap ), 'bounds_changed', function(event) {  
				this.setZoom(11);
				google.maps.event.removeListener(blistener);
			});
		}
			
		function getGMWIContent(i) {
			
			var content = "";
			content +=	'<div class="gmw-gl-gm-iw-wrapper">';
			content +=  	'<div class="gmw-gl-gm-iw-thumb">' + glgmGroups[i]['avatar'] + '</div>';
			content +=		'<div class="gmw-gl-gm-iw-info">';
			content +=			'<table>';
			content +=				'<tr><td><div class="gmw-gl-gm-iw-permalink"><a href="' + glgmGroups[i]['permalink'] + '">' + glgmGroups[i]['display_name'] + '</a></div></td></tr>';
			content +=				'<tr><td><span>Address: </span>' + glgmGroups[i]['latLng'][0]['address'] + '</td></tr>';
			//if ( glgmMapArgs.units_array != false ) 
				//content +=				'<tr><td><span>Distance: </span>' + glgmGroups[i]['distance'] + ' ' + glgmMapArgs.units_array['name'] + '</td></tr>';
			content +=			'</table>';
			content +=		'</div>';
			content +=  '</div>';
			return content; 
		} 
	});
});


				  
		