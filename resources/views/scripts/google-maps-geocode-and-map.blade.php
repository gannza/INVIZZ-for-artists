@if ($user->profile && $user->profile->location)
	<style>
		pre {
			border:1px solid #D6E0F5;
			padding:5px;
			margin:5px;
			background:#EBF0FA;
		}	
		
		/* fix for unwanted scroll bar in InfoWindow */
		.scrollFix {
			line-height: 1.35;
			overflow: hidden;
			white-space: nowrap;
		}
	
	</style>
	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
	<!-- <script src="http://maps.googleapis.com/maps/api/js" type="text/javascript"></script>	 -->
	<script type="text/javascript">

		"use strict";

		// variable to hold a map
		var map;

		// variable to hold current active InfoWindow
		var activeInfoWindow ;		

		// ------------------------------------------------------------------------------- //
		// initialize function		
		// ------------------------------------------------------------------------------- //

		function loadCensusData(variable) {
            // load the requested variable from the census API (using local copies)
            const xhr = new XMLHttpRequest();
            xhr.open("GET", variable);
			var row
            xhr.onload = function() {
                const censusData = JSON.parse(xhr.responseText);
				console.log(censusData.features)
				
				for(row in censusData.features){
					console.log(censusData.features[row].geometry)
					// console.log(censusData.features[row].properties.name)
				}
            };
            xhr.send();
		}
		

		function initialize() {
			
			// map options - lots of options available here 
			var mapOptions = {
				scrollwheel: true,
				disableDefaultUI: false,
				draggable: true,
				zoom: 8,
				center: new google.maps.LatLng(44.9600, -93.1000),
				mapTypeId: google.maps.MapTypeId.ROADMAP // HYBRID, ROADMAP, SATELLITE, or TERRAIN
			};
			
			var json_url1 = "https://storage.googleapis.com/mapsdevsite/json/states.js"
			var json_url = "./state.json"
			var data_url = "https://storage.googleapis.com/mapsdevsite/json/DP05_0017E.json"
			// create map in div called map-canvas using map options defined above
			map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

			// define three Google Map LatLng objects representing geographic points
			var stPaul 			= new google.maps.LatLng(44.95273,-93.08915);
			var minneapolis 	= new google.maps.LatLng(44.97994,-93.26630);
			var falconHeights 	= new google.maps.LatLng(44.9917,-93.1664);

			// place markers
			fnPlaceMarkers(stPaul,"St Paul");
			fnPlaceMarkers(minneapolis,"Minneapolis");			
			fnPlaceMarkers(falconHeights,"Falcon Heights");
			
			map.data.loadGeoJson(
				json_url,
				{ idPropertyName: "STATE" },
			);
			loadCensusData(json_url)
			// console.log(map.data.getFeatureById(3))

			const flightPlanCoordinates = [
				{ lat: 37.772, lng: -122.214 },
				{ lat: 21.291, lng: -157.821 },
				{ lat: -18.142, lng: 178.431 },
				{ lat: -27.467, lng: 153.027 },
			];	

			const flightPath = new google.maps.Polyline({
				path: flightPlanCoordinates,
				geodesic: true,
				strokeColor: "#FF6600",
				strokeOpacity: 1.0,
				strokeWeight: 2,
			});

			flightPath.setMap(map);


			
		}

		// ------------------------------------------------------------------------------- //
		// create markers on the map
		// ------------------------------------------------------------------------------- //
		function fnPlaceMarkers(myLocation,myCityName){
				
			var marker = new google.maps.Marker({
				position : myLocation,
				icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: ("#fc384a"), fillOpacity: 1, scale: 8, strokeColor: "white", strokeWeight: 3 },
			});
			// var marker = new google.maps.Marker({
			// 	position: myLocation,
			// 	icon: {
			// 		path: google.maps.SymbolPath.CIRCLE,
			// 		scale: 10,
			// 	},
			// 	draggable: true,
			// 	map: map,
			// });

			// Renders the marker on the specified map
			marker.setMap(map);	

			// create an InfoWindow - for mouseover
			var infoWnd = new google.maps.InfoWindow();						

			// create an InfoWindow -  for mouseclick
			var infoWnd2 = new google.maps.InfoWindow();

			// variable to hold number of seconds before showing infoWindow on Mouseover event
			var mouseoverTimeoutId = null;
			
			
			// -----------------------
			// ON MOUSEOVER
			// -----------------------
			
			// add content to your InfoWindow
			infoWnd.setContent('<div class="scrollFix">' + '<img src="' + '{{$user->profile->avatar}}' + '" style="width:40px; heigth:40px;">' + '</div>');
			
			// add listener on InfoWindow for mouseover event
			google.maps.event.addListener(marker, 'mouseover', function() {
			
				// Close active window if exists - [one might expect this to be default behaviour no?]				
				if(activeInfoWindow != null) activeInfoWindow.close();

				// Close info Window on mouseclick if already opened
				infoWnd2.close();

				// Open new InfoWindow for mouseover event
				infoWnd.open(map, marker);

				// Store new open InfoWindow in global variable
				activeInfoWindow = infoWnd;	

			}); 							
			
			// on mouseout (moved mouse off marker) make infoWindow disappear
			google.maps.event.addListener(marker, 'mouseout', function() {
				infoWnd.close();	
			});
			
			// --------------------------------
			// ON MARKER CLICK - (Mouse click)
			// --------------------------------
			
			// add content to InfoWindow for click event 
			infoWnd2.setContent('<div class="scrollFix">' + 'Welcome to ' +  myCityName + '. <br/>This Infowindow appears when you click on marker</div>');
			
			// add listener on InfoWindow for click event
			google.maps.event.addListener(marker, 'click', function() {
				
				//Close active window if exists - [one might expect this to be default behaviour no?]				
				if(activeInfoWindow != null) activeInfoWindow.close();

				// Open InfoWindow - on click 
				infoWnd2.open(map, marker);
				
				// Close "mouseover" infoWindow
				infoWnd.close();
				
				// Store new open InfoWindow in global variable
				activeInfoWindow = infoWnd2;
			}); 							
			
		}

		// ------------------------------------------------------------------------------- //
		// initial load
		// ------------------------------------------------------------------------------- //		
		google.maps.event.addDomListener(window, 'load', initialize);

	</script>

@endif