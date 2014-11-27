var map = null;
var polyLine;
var tmpPolyLine;
var order = [];
var markers = [];
var vmarkers = [];
var idarray = [];
var g = google.maps;

var initMap = function(mapHolder) {
	markers = [];
	vmarkers = [];
	var mapOptions = {
		zoom: 2,
		center: new g.LatLng(5, 30), 
		mapTypeId: g.MapTypeId.HYBRID,
		draggableCursor: 'auto',
		draggingCursor: 'move',
		disableDoubleClickZoom: true
	};
	map = new g.Map(document.getElementById(mapHolder), mapOptions);
	g.event.addListener(map, "click", mapLeftClick);
	g.event.addListener(map, 'zoom_changed', function() {
		var zoom = map.getZoom(); 
		jQuery('input#zoom').empty().val(zoom);
	});
	g.event.addListener(map, 'maptypeid_changed', function() {
		var type = map.getMapTypeId(); ;
		jQuery('input#type').empty().val(type);
	});
	g.event.addListener(map, 'center_changed', function() {
		var center = map.getCenter();
		jQuery('input#default_coords').empty().val(center);
	});
	mapHolder = null;
	mapOptions = null;
};

var initPolyline = function() {
	var polyOptions = {
		strokeColor: "#3355FF",
		strokeOpacity: 0.8,
		strokeWeight: 4
	};
	var tmpPolyOptions = {
		strokeColor: "#3355FF",
		strokeOpacity: 0.4,
		strokeWeight: 4
	};
	polyLine = new g.Polyline(polyOptions);
	polyLine.setMap(map);
	tmpPolyLine = new g.Polyline(tmpPolyOptions);
	tmpPolyLine.setMap(map);
};

var mapAddWaypoint = function(id, pos) {
	var wp = '<div id="waypoint-'+ id +'" class="waypoint">';
	wp += '<input type="hidden" id="wplatlng-'+ id +'" name="waypoint['+ id +'][latlng]" value="'+ pos +'" />';
	wp += '<label for="wptitle-'+ id +'">'+ mapoWpTitle +' #'+ id +'</label>';
	wp += '<input type="text" id="wptitle-'+ id +'" name="waypoint['+ id +'][title]" id="" value="" />';
	wp += '<label for="wpdesc-'+ id +'">'+ mapoWpDesc +' #'+ id +'</label>';
	wp += '<textarea id="wpdesc-'+ id +'" name="waypoint['+ id +'][description]"></textarea>';
	wp += '</div>';
	return wp;
};

var setWaypontsHidden = function() {
	for (var w = 0; w < idarray.length; w++) {
		jQuery('#waypoint-'+ idarray[w] ).addClass('waypoint-hide');
	};
	w = null;
};

var mapLeftClick = function(event) {
	if (event.latLng) {
		var marker = createMarker(event.latLng);
		markers.push(marker);
		var mid = marker.__gm_id;
		var p = marker.getPosition();
		var wayp = mapAddWaypoint(mid,p);
	  	setWaypontsHidden();
		idarray.push(mid);
		var orderCoords = jQuery('input#order_coords').val();
		orderCoords = String(orderCoords);
		orderCoords += String(mid +',');
		jQuery('input#order_coords').val(orderCoords);
		marker.setTitle(mapoWaypoint +' #'+ mid);
		jQuery('#mapology-waypoints').prepend(wayp)
		if (markers.length != 1) {
			var vmarker = createVMarker(event.latLng);
			vmarkers.push(vmarker);
			vmarker = null;
		}
		var path = polyLine.getPath();
		path.push(event.latLng);
		marker = null; mid = null; l = null; p = null; wayp = null; orderCoords = null;
	}
	event = null;
};

var createMarker = function(point) {
	var imageNormal = new g.MarkerImage(
		mapoLink +"circle.png",
		new g.Size(16, 16),
		new g.Point(0, 0),
		new g.Point(8, 8)
	);
	var imageHover = new g.MarkerImage(
		mapoLink +"circle_over.png",
		new g.Size(16, 16),
		new g.Point(0, 0),
		new g.Point(8, 8)
	);
	var marker = new g.Marker({
		position: point,
		map: map,
		icon: imageNormal,
		draggable: true
	});
	g.event.addListener(marker, "mouseover", function() {
		marker.setIcon(imageHover);
	});
	g.event.addListener(marker, "mouseout", function() {
		marker.setIcon(imageNormal);
	});
	g.event.addListener(marker, "drag", function() {
		for (var m = 0; m < markers.length; m++) {
			if (markers[m] == marker) {
				var pos = marker.getPosition();
				var mid = marker.__gm_id;
				polyLine.getPath().setAt(m, pos);
				jQuery('input#wplatlng-'+ mid ).empty().val(pos);
				moveVMarker(m);
				break;
			}
		}
		m = null;
	});
	g.event.addListener(marker, "click", function() {
		for (var m = 0; m < markers.length; m++) {
			if (markers[m] == marker) {
				var mid = marker.__gm_id;
				jQuery('#waypoint-'+ mid ).remove();
				var c = jQuery('input#order_coords').val();
				c = c.replace(mid +',','');
				jQuery('input#order_coords').val(c);
				marker.setMap(null);
				markers.splice(m, 1);
				polyLine.getPath().removeAt(m);
				removeVMarkers(m);
				break;
			}
		}
		m = null;
	});
	return marker;
};

var createVMarker = function(point) {
	var prevpoint = markers[markers.length-2].getPosition();
	var imageNormal = new g.MarkerImage(
		mapoLink +"circle_transparent.png",
		new g.Size(16, 16),
		new g.Point(0, 0),
		new g.Point(8, 8)
	);
	var imageHover = new g.MarkerImage(
		mapoLink +"circle_transparent_over.png",
		new g.Size(16, 16),
		new g.Point(0, 0),
		new g.Point(8, 8)
	);
	var marker = new g.Marker({
		position: new g.LatLng(
			point.lat() - (0.5 * (point.lat() - prevpoint.lat())),
			point.lng() - (0.5 * (point.lng() - prevpoint.lng()))
		),
		map: map,
		icon: imageNormal,
		draggable: true
	});
	g.event.addListener(marker, "mouseover", function() {
		marker.setIcon(imageHover);
	});
	g.event.addListener(marker, "mouseout", function() {
		marker.setIcon(imageNormal);
	});
	g.event.addListener(marker, "dragstart", function() {
		for (var m = 0; m < vmarkers.length; m++) {
			if (vmarkers[m] == marker) {
				var tmpPath = tmpPolyLine.getPath();
				tmpPath.push(markers[m].getPosition());
				tmpPath.push(vmarkers[m].getPosition());
				tmpPath.push(markers[m+1].getPosition());
				break;
			}
		}
		m = null;
	});
	g.event.addListener(marker, "drag", function() {
		for (var m = 0; m < vmarkers.length; m++) {
			if (vmarkers[m] == marker) {
				tmpPolyLine.getPath().setAt(1, marker.getPosition());
				break;
			}
		}
		m = null;
	});
	g.event.addListener(marker, "dragend", function() {
		for (var m = 0; m < vmarkers.length; m++) {
			if (vmarkers[m] == marker) {
				var newpos = marker.getPosition();
				var startMarkerPos = markers[m].getPosition();
				var firstVPos = new g.LatLng(
					newpos.lat() - (0.5 * (newpos.lat() - startMarkerPos.lat())),
					newpos.lng() - (0.5 * (newpos.lng() - startMarkerPos.lng()))
				);
				var endMarkerPos = markers[m+1].getPosition();
				var secondVPos = new g.LatLng(
					newpos.lat() - (0.5 * (newpos.lat() - endMarkerPos.lat())),
					newpos.lng() - (0.5 * (newpos.lng() - endMarkerPos.lng()))
				);
				var newVMarker = createVMarker(secondVPos);
				newVMarker.setPosition(secondVPos);
				var newMarker = createMarker(newpos);
				markers.splice(m+1, 0, newMarker);
				
				var mid = newMarker.__gm_id;
				var wayp = mapAddWaypoint(mid,newpos);
				setWaypontsHidden();
				idarray.push(mid);
		
				var wp = String();
				for (var s = 0; s < markers.length; s++) {
					wp += String(markers[s].__gm_id +',');
				}
				jQuery('input#order_coords').empty().val(wp);
				newMarker.setTitle(mapoWaypoint +' #'+ mid);
				jQuery('#mapology-waypoints').prepend(wayp)
				polyLine.getPath().insertAt(m+1, newpos);
				marker.setPosition(firstVPos);
				vmarkers.splice(m+1, 0, newVMarker);
				tmpPolyLine.getPath().removeAt(2);
				tmpPolyLine.getPath().removeAt(1);
				tmpPolyLine.getPath().removeAt(0);
				newpos = null; startMarkerPos = null; firstVPos = null;
				endMarkerPos = null; secondVPos = null;	newVMarker = null;
				newMarker = null; mid = null; l = null; wayp = null;
				break;
			}
		}
	});
	return marker;
};

var moveVMarker = function(index) {
	var newpos = markers[index].getPosition();
	if (index != 0) {
		var prevpos = markers[index-1].getPosition();
		vmarkers[index-1].setPosition(new g.LatLng(
			newpos.lat() - (0.5 * (newpos.lat() - prevpos.lat())),
			newpos.lng() - (0.5 * (newpos.lng() - prevpos.lng()))
		));
		prevpos = null;
	}
	if (index != markers.length - 1) {
		var nextpos = markers[index+1].getPosition();
		vmarkers[index].setPosition(new g.LatLng(
			newpos.lat() - (0.5 * (newpos.lat() - nextpos.lat())), 
			newpos.lng() - (0.5 * (newpos.lng() - nextpos.lng()))
		));
		nextpos = null;
	}
	newpos = null;
	index = null;
};

var removeVMarkers = function(index) {
	if (markers.length > 0) {
		if (index != markers.length) {
			vmarkers[index].setMap(null);
			vmarkers.splice(index, 1);
		} else {
			vmarkers[index-1].setMap(null);
			vmarkers.splice(index-1, 1);
		}
	}
	if (index != 0 && index != markers.length) {
		var prevpos = markers[index-1].getPosition();
		var newpos = markers[index].getPosition();
		vmarkers[index-1].setPosition(new g.LatLng(
			newpos.lat() - (0.5 * (newpos.lat() - prevpos.lat())),
			newpos.lng() - (0.5 * (newpos.lng() - prevpos.lng()))
		));
		prevpos = null;
		newpos = null;
	}
	index = null;
};

jQuery(document).ready(function() {
	initMap('mapology-create-map');
	initPolyline();
	jQuery('#wpshow').click(function() {
		jQuery('.waypoint-hide').addClass('wp-show');
		return false;
	});
	jQuery('#wphide').click(function() {
		jQuery('.waypoint-hide').removeClass('wp-show');
		return false;
	});

    jQuery(function() {
        var dates = jQuery( "#start_date,#end_date" ).datepicker({
            firstDay: 1,
            changeMonth: false,
            changeYear: false,
            dateFormat: "yy-mm-dd",
            onSelect: function( selectedDate ) {
                var option = this.id == "start_date" ? "minDate" : "maxDate", instance = jQuery(this).data("datepicker");
                date = jQuery.datepicker.parseDate( instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings );
                dates.not(this).datepicker( "option", option, date );
            }
        });
    });
});