<!-- @if ($user->profile && $user->profile->location) -->

	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
	<script type="text/javascript">

		function google_maps_geocode_and_map() {

			var geocoder = new google.maps.Geocoder();
			var address = '{{$user->profile->location}}';

			geocoder.geocode( { 'address': address}, function(results, status) {

				if (status == google.maps.GeocoderStatus.OK) {

					var latitude = results[0].geometry.location.lat();
					var longitude = results[0].geometry.location.lng();

					// SHOW LATITUDE AND LONGITUDE
					document.getElementById('latitude').innerHTML += latitude;
					document.getElementById('longitude').innerHTML += longitude;

					// CHECK IF HTML DOM CONTAINER IS FOUND
					if (document.getElementById('map-canvas')){

						function getMap() {

		
						    var LatitudeAndLongitude = new google.maps.LatLng(latitude,longitude);

							var mapOptions = {
								scrollwheel: true,
								disableDefaultUI: false,
								draggable: true,
								zoom: 14,
								center: LatitudeAndLongitude,
								mapTypeId: google.maps.MapTypeId.ROADMAP // HYBRID, ROADMAP, SATELLITE, or TERRAIN
							};

							var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

						  	// MARKER
						    var marker = new google.maps.Marker({
						        map: map,
						        //icon: "",
						        title: '<strong>{{$user->first_name}}</strong> <br />  {{$user->email}}',
						        position: map.getCenter()
						    });

						    // INFO WINDOW
							var infowindow = new google.maps.InfoWindow();
							infowindow.setContent('<strong>{{$user->first_name}}</strong> <br />  {{$user->email}}');

						    infowindow.open(map, marker);
							google.maps.event.addListener(marker, 'click', function() {
								infowindow.open(map, marker);
							});

							var locations = [
								['Bondi Beach', -33.890542, 151.274856, 4],
								['Coogee Beach', -33.923036, 151.259052, 5],
								['Cronulla Beach', -34.028249, 151.157507, 3],
								['Manly Beach', -33.80010128657071, 151.28747820854187, 2],
								['Maroubra Beach', -33.950198, 151.259302, 1]
								];

								var map = new google.maps.Map(document.getElementById('map-canvas'), {
									zoom: 10,
									center: new google.maps.LatLng(-33.92, 151.25),
									mapTypeId: google.maps.MapTypeId.ROADMAP
								});

								var infowindow = new google.maps.InfoWindow();

								var marker, i;

								for (i = 0; i < locations.length; i++) { 
								marker = new google.maps.Marker({
									position: new google.maps.LatLng(locations[i][1], locations[i][2]),
									icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: ("#fc384a"), fillOpacity: 1, scale: 7, strokeColor: "white", strokeWeight: 3 },
									map: map

								});

								

								google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
									return function() {
											infowindow.setContent(locations[i][0]);
											infowindow.open(map, marker);

											marker = new google.maps.Marker({
												position: new google.maps.LatLng(locations[i][1], locations[i][2]),
												icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: ("#05BC4A"), fillOpacity: 1, scale: 7, strokeColor: "white", strokeWeight: 3 },
												map: map
											});
										}
									})(marker, i));
								}

								// google.load('visualization', '1', {'packages': ['geomap']});
								// google.setOnLoadCallback(drawMap);

								// 	function drawMap() {
								// 	var data = google.visualization.arrayToDataTable([
								// 		['Province'],
								// 		['Lampung'],
								// 		['Banten']
								// 	]);

								// 	var options = {};
								// 	options['region'] = 'ID';
								// 	options['colors'] = [0xFF8747, 0xFFB581, 0xc06000]; //orange colors
								// 	options['dataMode'] = 'regions';

								// 	var container = document.getElementById('map_canvas');
								// 	var geomap = new google.visualization.GeoMap(container);
								// 	geomap.draw(data, options);
								// 	};

						}

						// ATTACH MAP TO DOM HTML ELEMENT
						google.maps.event.addDomListener(window, 'load', getMap);

					}

				}

				

			});

		}

		google_maps_geocode_and_map();

	</script>

<!-- @endif -->