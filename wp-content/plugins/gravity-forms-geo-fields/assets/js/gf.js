function ggf_init( ggfSettings ) {
	
	if ( ggfSettings['ggf-locator-hide-submit'] == 1 ) jQuery('#gform_submit_button_'+ggfSettings['id']).hide();
		
	ggfAutocomplete = '.ggf-autocomplete input[type="text"]';
	
	//remove lat/lng fields when address changes
	jQuery('.ggf-field input[type="text"]').on("input", function() {
		jQuery('#ggf-text-fields-wrapper input').val('');
		jQuery('#ggf-update-location').addClass('update');
	});

	if( jQuery('#ggf-map').length ) {
		
		var latlng = new google.maps.LatLng(mapArgs.latitude,mapArgs.longitude);
	
		var options = {
			zoom: parseInt(mapArgs.zoom_level),
			center: latlng,
			mapTypeId: google.maps.MapTypeId[mapArgs.map_type],
		};
	
		// creat the map
		ggfMap = new google.maps.Map(document.getElementById("ggf-map"), options);
	
		// the geocoder object allows us to do latlng lookup based on address
		geocoder = new google.maps.Geocoder();
	
		ggfMarker = new google.maps.Marker({
			position:latlng,
			map: ggfMap,
			draggable: true,
		});
		
		//when dragging the marker on the map
		google.maps.event.addListener( ggfMarker, 'dragend', function(evt){
			jQuery('#ggf-update-location').removeClass('update');
			jQuery("#ggf-field-lat").val( evt.latLng.lat() );
			jQuery("#ggf-field-lng").val( evt.latLng.lng() );
			returnAddress( evt.latLng.lat(), evt.latLng.lng(), false );  
		});
	
	}
	
    //locator button clicked 
    jQuery('.ggf-locator-button').click(function(){
    	jQuery('#ggf-update-location').removeClass('update');
        jQuery('#ggf-update-location').addClass('autolocating');
    	jQuery(".ggf-locator-spinner-wrapper").show();
  		getLocationBP();
  	}); 
  	
    //get current location
    function getLocationBP() {
    	
		if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition( showPosition, showError, {timeout:10000} );
		} else {
   	 		alert("Geolocation is not supported by this browser.");
   	 		jQuery("#ggf-locator-spinner").hide();
   		}
		
	}
    
    //show results of current location
	function showPosition(position) {	
		
		alert('Location found');
   		
  		returnAddress( position.coords.latitude, position.coords.longitude, true );
  		jQuery(".ggf-locator-spinner-wrapper").hide();
  		
  		if ( ggfSettings['ggf-locator-autosubmit'] == 1 ) jQuery('#ggf-update-location').addClass('autosubmit');
  		
	}

	//error message for locator button
	function showError(error) {
  		
		switch(error.code) {
   	 		case error.PERMISSION_DENIED:
      			alert("User denied the request for Geolocation.");
     		break;
   		 	case error.POSITION_UNAVAILABLE:
   		   		alert("Location information is unavailable.");
    	  	break;
    		case error.TIMEOUT:
      			alert("The request to get user location timed out.");
     		break;
    		case error.UNKNOWN_ERROR:
      			alert("An unknown error occurred.");
      		break;
		}
		jQuery("#ggf-locator-spinner").hide();
	}
	
	//update map
	function update_map() {
		
		if ( !jQuery('#ggf-map').length ) return;
		
		var latLng = new google.maps.LatLng( jQuery('#ggf-field-lat').val(), jQuery('#ggf-field-lng').val() );
		
		ggfMarker.setMap(null);
		
		ggfMarker = new google.maps.Marker({
		    position: latLng,
		    map: ggfMap,
                    draggable: true
		});
		ggfMap.setCenter(latLng);
		
		//when dragging the marker on the map
		google.maps.event.addListener( ggfMarker, 'dragend', function(evt){
			jQuery('#ggf-update-location').removeClass('update');
			returnAddress( evt.latLng.lat(), evt.latLng.lng(), false );  
		});
	}
	
	//autocomplete
	function ggfAutocompleteInit() {
		
                var acField;
                var umField;
                var faField;
                
		jQuery(ggfAutocomplete).autocomplete({
	
			source: function(request,response) {
                               
				geocoder = new google.maps.Geocoder();
				// the geocode method takes an address or LatLng to search for
				// and a callback function which should process the results into
				// a format accepted by jqueryUI autocomplete
				geocoder.geocode( {'address': request.term }, function(results, status) {
					response(jQuery.map(results, function(item) {
						return {
							label: item.formatted_address, // appears in dropdown box
							value: item.formatted_address, // inserted into input element when selected
							geocode: item                  // all geocode data: used in select callback event
						};
					}));
				});
			},
	
			// event triggered when drop-down option selected
			select: function(event,ui){
				
				if ( jQuery('#'+acField).length == 1 ) {
                                    
					//update_ui(  ui.item.value, ui.item.geocode.geometry.location );
					//update_map( ui.item.geocode.geometry );
   
                                        //if we updating hidden fields of location
					if ( faField == true ) {
                                            jQuery('#ggf-update-location').removeClass('update');	
                                            jQuery('#ggf-text-fields-wrapper input').val('');
                                            breakAddress(ui.item.geocode);
                                        }
                                        //when updating marker on the map
                                        if ( umField == true ) update_map();
	
				}
				
			}
		});
		
		// triggered when user presses a key in the address box
		jQuery(ggfAutocomplete).bind('keydown', function(event) {
                        
                        acField = jQuery(this).attr('id');
                        umField = ( jQuery(this).closest('li').hasClass('autocomplete-update-map') ) ? true : false;
                        faField = ( jQuery(this).closest('li').hasClass('ggf-full-address') ) ? true : false;
                        
			if(event.keyCode == 13) {
				// ensures dropdown disappears when enter is pressed
				jQuery(acField).autocomplete("disable");
			} else {
				// re-enable if previously disabled above
				jQuery(acField).autocomplete("enable");
			}
		});
		
	}
	ggfAutocompleteInit();
	
	/* main function to conver lat/long to address */
	function returnAddress( gotLat, gotLng, updateMap ) {
				
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(gotLat ,gotLng);
	
		//geocode lat/lng to address
		geocoder.geocode( {'latLng': latlng }, function(results, status) {
      		
			if (status == google.maps.GeocoderStatus.OK) {
				
                            if ( results[0] ) {

                                    breakAddress(results[0]);
                                    if ( updateMap == true ) update_map();
                            }
       	 		
                        } else {
      			
                            alert("Geocoder failed due to: " + status);
        		
                        }
   		});
	} 
	
	//address components
	function breakAddress(location) {
			
		//remove all address fields
		jQuery('#ggf-text-fields-wrapper input').val('');
		
                if ( jQuery('#ggf-update-location').hasClass('autolocating') ) {
                    jQuery('.ggf-full-address').each(function() {
                        if ( !jQuery(this).hasClass('disable-locator-fill') ) {
                            jQuery(this).find('input[type="text"]').val(location.formatted_address);
                        }
                    });
                    jQuery('#ggf-update-location').removeClass('autolocating');
                } else {
                    jQuery('#ggf-field-formatted_address, .ggf-full-address input[type="text"]').val(location.formatted_address);
                }
                
		jQuery("#ggf-field-lat").val( location.geometry.location.lat() );
		jQuery("#ggf-field-lng").val( location.geometry.location.lng() );
		
		address = location.address_components;
		
		var street_number = false;
		
		for ( x in address ) {

			if ( address[x].types == 'street_number' ) {
				street_number = address[x].long_name;
			}
			
			if ( address[x].types == 'route' ) {
				street = address[x].long_name;  
				if ( street_number != false ) {
					street = street_number + ' ' + street;
					jQuery("#ggf-field-street").val(street);
					
					if ( !jQuery('#ggf-update-location').hasClass('update') ) 
						jQuery('.ggf-field-street input[type="text"]').val(street);
					
				} else {
					jQuery("#ggf-field-street").val(street);
					
					if ( !jQuery('#ggf-update-location').hasClass('update') ) 
						jQuery('.ggf-field-street input[type="text"]').val(street);
					
				}
			}
	
			if ( address[x].types == 'administrative_area_level_1,political' ) {
                                state = address[x].short_name;
                                state_long = address[x].long_name;
                                jQuery("#ggf-field-state").val(state);
                                jQuery("#ggf-field-state_long").val(state_long);

                                jQuery('.ggf-field-state input[type="text"]').val(state);

                        } 

                        if (address[x].types == 'locality,political') {
                                city = address[x].long_name;
                                jQuery("#ggf-field-city").val(city);

                                if ( !jQuery('#ggf-update-location').hasClass('update') ) 
                                        jQuery('.ggf-field-city input[type="text"]').val(city);

                        } 

                        if (address[x].types == 'postal_code') {
                                zipcode = address[x].long_name;
                                jQuery("#ggf-field-zipcode").val(zipcode);

                                if ( !jQuery('#ggf-update-location').hasClass('update') ) 
                                        jQuery('.ggf-field-zipcode input[type="text"]').val(zipcode);

                        } 

                        if (address[x].types == 'country,political') {
                                country = address[x].short_name;
                                country_long = address[x].long_name;
                                jQuery("#ggf-field-country").val(country);
                                jQuery("#ggf-field-country_long").val(country_long);

                                jQuery('.ggf-field-country input[type="text"]').val(country);

                         } 
                }
		
		if ( jQuery('#ggf-update-location').hasClass('update') || jQuery('#ggf-update-location').hasClass('autosubmit') ) {
			
			jQuery('#ggf-update-location').removeClass(function() {
				setTimeout(function() {
					jQuery('#gform_'+ggfSettings['id'] ).submit();	
				}, 800);
			},'update');
			
		}
	}

	//convert address to lat/lng
	jQuery('#gform_submit_button_'+ggfSettings['id'] ).click(function(e) {
		
		if ( !jQuery('#ggf-update-location').hasClass('update') ) return;
		
		e.preventDefault();		
		getLatLong();
	});
	
	/* convert address to lat/long */
	function getLatLong() {
		
		var geoAddress;
		
		if ( ggfSettings.address_fields.use == 1 ) {
			geoAddress = jQuery('.ggf-full-address input[type="text"]').val();
		} else if ( ggfSettings.address_fields.use == 2 ) {
			
			var street  = jQuery('.ggf-field-street input[type="text"]').val();
	 	  	var city    = jQuery('.ggf-field-city input[type="text"]').val();
	 	  	var state   = jQuery('.ggf-field-state input[type="text"]').val();
	 	  	var zipcode = jQuery('.ggf-field-zipcode input[type="text"]').val();
	  	  	var country = jQuery('.ggf-field-country input[type="text"]').val();
	  	  	
	  	  	geoAddress  = street + " " + city + " " + state + " " + zipcode + " " + country;
		}
		
    	geocoder = new google.maps.Geocoder();
   	 	geocoder.geocode( { 'address': geoAddress}, function(results, status) {
      		
   	 		if (status == google.maps.GeocoderStatus.OK) {
        		
        		breakAddress(results[0]);
          		       						
    		} else {
    			
        		alert( 'Geocode was not successful for the following reason: ' + status + '. Please check the address you entered.' );     
       			
    		}
    	});
	}
	  	
};