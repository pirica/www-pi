
<div id="map_canvas" style="height:100%;"></div>
<div id="info">
	Speed: <span id="speed">-</span>
	<span id="additional"></span>
</div>

<script type="text/javascript">

var map,geocoder,zoom=13;
var marker = null;
var locationCircle = null, precisionCircle = null;
var prevTime = 0;
	
$().ready(function(){
	$('.container').height( $(window).height() - $('.topmenu').height() - $('.breadcrumb').height() - $('.mapmenu').height() - $('.bottombar').height() - 60 );
	
	initialize();
	focusTarget('winkelomheide',zoom);
	
	setTimeout('getData()', 3000);
	//getData();
});

function initialize() {
	var myOptions = {
		zoom: zoom,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map= new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	var rendererOptions = {
		map: map
	}
	geocoder = new google.maps.Geocoder();
	
}

function focusTarget(address,zoom)
{ 
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			map.setZoom(zoom);
			
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
}

function getData()
{
	setTimeout('getData()', 500);
	
	$.ajax({
		url: "?action=js_position",
		type: 'get',
		dataType: 'text',
		async: false,
		success: function(data){
			
			var pos = $.parseJSON(data);
			var newPoint = new google.maps.LatLng(pos.lat, pos.lon);
			var r = parseInt(pos.accuracy); // meter
			var h = parseInt(pos.heading); // degrees/N
			var v = parseInt(pos.speed); // meter/sec
			var t = parseInt(pos.time);
			
			if(pos.time_lbl != ''){
				$('#additional').html('<br>Date: ' . pos.time_lbl);
			}
			else {
				$('#additional').html('');
			}
			
			if(t >= prevTime){
				
				if (marker) {
					// Marker already created - Move it
					marker.setPosition(newPoint);
					precisionCircle.setCenter(newPoint);
					precisionCircle.setRadius(r);
					locationCircle.setCenter(newPoint);
					locationCircle.setRadius(50);
				}
				else {
					// Marker does not exist - Create it
					marker = new google.maps.Marker({
						position: newPoint,
						map: map
					});
					
					var precisionOptions = {
						strokeColor: '#FF0000',
						strokeOpacity: 0.7,
						strokeWeight: 1,
						fillColor: '#FF0000',
						fillOpacity: 0.2,
						map: map,
						center: newPoint,
						radius: r
					};
					precisionCircle = new google.maps.Circle(precisionOptions);
					
					
					var locationOptions = {
						strokeColor: '#00FF00',
						strokeOpacity: 0.7,
						strokeWeight: 1,
						fillColor: '#00FF00',
						fillOpacity: 0.1,
						map: map,
						center: newPoint,
						radius: r
					};
					locationCircle = new google.maps.Circle(locationOptions);
				}
				
				precisionCircle.setVisible(r >= 50);
				locationCircle.setVisible(r < 50);
				
				marker.setIcon({path: google.maps.SymbolPath.FORWARD_OPEN_ARROW, rotation: h, zoom:3});
				
				//map.setZoom(zoom);
				
				// Center the map on the new position
				
				var b = map.getBounds();
				
				if(b){
					var sw = b.getSouthWest();
					var ne = b.getNorthEast();
					
					if(
						newPoint.lat() > ne.lat() - ((ne.lat() - sw.lat()) / 3) 
						||
						newPoint.lng() > ne.lng() - ((ne.lng() - sw.lng()) / 3) 
						||
						newPoint.lat() < sw.lat() + ((ne.lat() - sw.lat()) / 3) 
						||
						newPoint.lng() < sw.lng() + ((ne.lng() - sw.lng()) / 3) 
					){
						map.panTo(newPoint);
					}
				}
				
				var currentZoom = zoom;
				
				if(currentZoom != zoom){
					map.setZoom(zoom);
				}
				
				$('#speed').html('' + (v * 3.6) + ' km/h');
				
				// https://developers.google.com/maps/documentation/javascript/reference
				
			}
		}
	});
	
}

</script>
