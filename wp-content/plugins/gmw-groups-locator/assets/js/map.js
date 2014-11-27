jQuery(document).ready(function($){ 
	
	function glMapInit( glGroups ) {
	
		var zoomLevel = ( gmwForm.results_map['zoom_level'] == 'auto' ) ? 13 : gmwForm.results_map['zoom_level'];
		
		var groupsMap = new google.maps.Map(document.getElementById('gmw-map-'+ glGroups.ID), {
			zoom: parseInt(zoomLevel),
			panControl: true,
	  		zoomControl: true,
	  		mapTypeControl: true,
			center: new google.maps.LatLng(glGroups.your_lat, glGroups.your_lng),
			mapTypeId: google.maps.MapTypeId[glGroups.results_map['map_type']],
		});	
		
		var latlngbounds = new google.maps.LatLngBounds();
		
		if ( glGroups.your_lat && glGroups.your_lng ) {
                    
			var yourLocation  = new google.maps.LatLng( glGroups.your_lat, glGroups.your_lng );
			latlngbounds.extend(yourLocation);
			
			marker = new google.maps.Marker({
				position: new google.maps.LatLng( glGroups.your_lat, glGroups.your_lng ),
				map: groupsMap,
				icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
			});
		}
	
		var i;
		var gliw = false;
		mMarkers = [];

		for ( i = 0; i < glGroups.results.length; i++ ) { 
			var mapIcon;
		
			var groupLocation = new google.maps.LatLng( glGroups.results[i]['lat'], glGroups.results[i]['lng'] );
			latlngbounds.extend(groupLocation);
			
			mapIcon = glGroups.results[i]['mapIcon'];
				
			mMarkers[i] = new google.maps.Marker({
				position: groupLocation,
				icon:mapIcon,
				map:groupsMap,
				id:i   
			});
		
			with ({ mMarker: mMarkers[i] }) {
				google.maps.event.addListener( mMarker, 'click', function() {
					if (gliw) {
						gliw.close();
						gliw = null;
					}
					gliw = new google.maps.InfoWindow({
						content: getFLIWContent( mMarker.id ),
					});
					gliw.open( groupsMap, mMarker ); 		
				});
			}
		}
		if ( gmwForm.results_map['zoom_level'] == 'auto' || ( gmwForm.your_lat == false && gmwForm.your_lng == false ) ) groupsMap.fitBounds(latlngbounds);
		
		if ( glGroups.results.length == 1 && !glGroups.your_lat ) {
			blistener = google.maps.event.addListener( ( groupsMap ), 'bounds_changed', function(event) {  
				this.setZoom(11);
				google.maps.event.removeListener(blistener);
			});
		}
					
		function getFLIWContent(i) {
			
			var content = "";
			content +=	'<div class="wppl-gl-info-window">';
			content +=  	'<div class="wppl-info-window-thumb">' + glGroups.results[i]['avatar'] + '</div>';
			content +=		'<div class="wppl-info-window-info">';
			content +=			'<table>';
			content +=				'<tr><td><div class="wppl-info-window-permalink"><a href="' + glGroups.results[i]['permalink'] + '">' + glGroups.results[i]['name'] + '</a></div></td></tr>';
			content +=				'<tr><td><span>'+glGroups['iw_labels']['address']+'</span>' + glGroups.results[i]['address'] + '</td></tr>';
			if ( gmwForm.org_address != false ) 
				content +=				'<tr><td><span>'+glGroups['iw_labels']['distance']+'</span>' + glGroups.results[i]['distance'] + ' ' + glGroups.units_array['name'] + '</td></tr>';
			content +=			'</table>';
			content +=		'</div>';
			content +=  '</div>';
			return content;
		} 
	}
	
	$( '#gmw-map-wrapper-'+gmwForm.ID ).slideToggle(function() {
		glMapInit( gmwForm );
	});
});	