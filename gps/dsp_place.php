


<div class="mapmenu">
	<a class="btn btn-sm btn-default" href="index.php?action=setplaces">
		<!--span class="glyphicon glyphicon-edit"></span-->
		Cancel
	</a>
	
	<form class="placeform pull-right" method="post" action="index.php?action=do_setplace&amp;id_place=<?=$id_place?>">
		<input type="hidden" id="lat_top" 		name="lat_top"		value="<?= $place_lat_top ?>">
		<input type="hidden" id="lon_right"		name="lon_right"	value="<?= $place_lon_right ?>">
		<input type="hidden" id="lat_bottom"	name="lat_bottom"	value="<?= $place_lat_bottom ?>">
		<input type="hidden" id="lon_left"		name="lon_left"		value="<?= $place_lon_left ?>">
		
		Description:
		<input type="text" name="description" value="<?= $place_description ?>">
		
		Prefix:
		<input type="text" name="pre_description" value="<?= $place_pre_description ?>">
		
		<a class="btn btn-sm btn-success" href="javascript:$('.placeform').submit();">
			<!--span class="glyphicon glyphicon-edit"></span-->
			Save
		</a>
	</form>
</div>


<div id="map_canvas" style="height:100%;"></div>

<script type="text/javascript">

var map,geocoder,zoom=13;

// data entered
var
	// ne
	lat_top		= <?= $place_lat_top ?>,
	lon_right	= <?= $place_lon_right ?>,
	// sw
	lat_bottom	= <?= $place_lat_bottom ?>,
	lon_left	= <?= $place_lon_left ?>
;

// no data entered
/*
var
	// ne
	lat_top		= null,
	lon_right	= null,
	// sw
	lat_bottom	= null,
	lon_left	= null
;
*/
	
$().ready(function(){
	$('.container').height( $(window).height() - $('.topmenu').height() - $('.breadcrumb').height() - $('.mapmenu').height() - $('.bottombar').height() - 60 );
	
	initialize();
	
	if(lon_left == null){
		focusTarget('winkelomheide',zoom);
	}
	else {
		setData();
	}
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
	
	
	var drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.RECTANGLE,
		drawingControl: true,
		drawingControlOptions: {
			position: google.maps.ControlPosition.TOP_CENTER,
			drawingModes: [
				//google.maps.drawing.OverlayType.MARKER,
				//google.maps.drawing.OverlayType.CIRCLE,
				//google.maps.drawing.OverlayType.POLYGON,
				//google.maps.drawing.OverlayType.POLYLINE,
				google.maps.drawing.OverlayType.RECTANGLE
			]
		},
		/*markerOptions: {
		icon: 'images/beachflag.png'
		},*/
		circleOptions: {
			fillColor: '#00ff00',
			fillOpacity: 0.3,
			strokeWeight: 1,
			clickable: false,
			editable: true,
			zIndex: 1
		},
		rectangleOptions: {
			fillColor: '#ff0000',
			fillOpacity: 0.3,
			strokeWeight: 1,
			clickable: false,
			editable: true,
			zIndex: 1
		}
	});
	drawingManager.setMap(map);
	
	google.maps.event.addListener(drawingManager, 'overlaycomplete', function(marker) {
		if (marker.type == google.maps.drawing.OverlayType.CIRCLE) {
			var radius = marker.overlay.getRadius();
			var center = marker.overlay.getCenter();
			var lat = center.lat(), lon = center.lng();
			console.log('circle');
		}
		if (marker.type == google.maps.drawing.OverlayType.RECTANGLE) {
			//var radius = marker.overlay.getRadius();
			//var center = marker.overlay.getCenter();
			var bounds = marker.overlay.getBounds();
			var ne = bounds.getNorthEast(), sw = bounds.getSouthWest();
			var lat1 = ne.lat(), lon1 = ne.lng(), lat2 = sw.lat(), lon2 = sw.lng();
			console.log('rectangle');
			
			// ne
			lat_top		= lat1;
			lon_right	= lon1;
			// sw
			lat_bottom	= lat2;
			lon_left	= lon2;
			
			$('#lat_top')		.val(	lat_top		);
			$('#lon_right')		.val(	lon_right	);
			$('#lat_bottom')	.val(	lat_bottom	);
			$('#lon_left')		.val(	lon_left	);
		}
		
		/*marker.setOptions({
			draggable: true
		});*/
		google.maps.event.addListener(marker, 'dragend', function () {  
			// Put your code here when marker finish event drangend Example get LatLang 
			/*
				var objLatLng = marker.getPosition().toString().replace("(", "").replace(")", "").split(',');
				Lat = objLatLng[0];
				Lat = Lat.toString().replace(/(\.\d{1,5})\d*$/, "$1");// Set 5 Digits after comma
				Lng = objLatLng[1];
				Lng = Lng.toString().replace(/(\.\d{1,5})\d*$/, "$1");// Set 5 Digits after comma

			*/
		});
		drawingManager.setOptions({ drawingControl: false });
		drawingManager.setDrawingMode(null);
		addDeleteHandler(marker);
	});
	
	
	// https://developers.google.com/maps/documentation/javascript/reference
	
}

function addDeleteHandler(shape) {
	google.maps.event.addListener(shape, 'click', function () {
		shape.setMap(null);
		drawingManager.setOptions({
			drawingControl: true,
			drawingControlOptions: {
				position: google.maps.ControlPosition.TOP_CENTER,
				drawingModes: [
					//google.maps.drawing.OverlayType.MARKER,
					//google.maps.drawing.OverlayType.CIRCLE,
					//google.maps.drawing.OverlayType.POLYGON,
					//google.maps.drawing.OverlayType.POLYLINE,
					google.maps.drawing.OverlayType.RECTANGLE
				]
			}
		});

	});
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

function setData(){
	
	var rectangle = new google.maps.Rectangle({
		strokeColor: '#0000FF',
		strokeOpacity: 1,
		strokeWeight: 1,
		fillColor: '#990000',
		fillOpacity: 0.3,
		map: map,
		bounds: new google.maps.LatLngBounds(
			/* sw: */ new google.maps.LatLng(lat_bottom, lon_left),
			/* ne: */ new google.maps.LatLng(lat_top, lon_right)
		)
		
	});
	rectangle.setVisible(true);
	
	map.setZoom(zoom);
	map.panTo(new google.maps.LatLng(lat_bottom, lon_left));

}

//google.maps.event.addDomListener(window, 'load', initialize);

</script>

