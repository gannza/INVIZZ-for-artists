
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3lS_z2mHkm20q8KtKGk9Bm6uutMQFJ2A&callback=initMap&libraries=&v=weekly" defer></script>
    <style type="text/css">
        html,
        body,
        #map {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        
    </style>
    <script>
        const mapStyle = [{
            stylers: [{
                visibility: "off"
            }],
        }, {
            featureType: "landscape",
            elementType: "geometry",
            stylers: [{
                visibility: "on"
            }, {
                color: "#fcfcfc"
            }],
        }, {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
                visibility: "on"
            }, {
                color: "#bfd4ff"
            }],
        }, ];
        let map;
        let censusMin = Number.MAX_VALUE,
            censusMax = -Number.MAX_VALUE;

        function initMap() {
            // load the map
            const myLatlng =  {
                    lat: 40,
                    lng: -100
                };
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 40,
                    lng: -100
                },
                zoom: 7,
                options: {
                    gestureHandling: 'greedy'
                }
                //styles: mapStyle,
            });
            // const marker = new google.maps.Marker({
            //     position: myLatlng,
            //     map,
            //     title: "Click to zoom",
            // });

            map.addListener("zoom_changed", () => {
                // 3 seconds after the center of the map has changed, pan back to the
                // marker.
                // window.setTimeout(() => {
                // map.panTo(marker.getPosition());
                // }, 3000);
                console.log(map.getZoom())
                if(map.getZoom()<4){
                    map.setZoom(6)
                    map.panTo(myLatlng);
                } 
            });
            // set up the style rules and events for google.maps.Data
            map.data.setStyle(styleFeature);
            map.data.addListener("mouseover", mouseInToRegion);
            map.data.addListener("mouseout", mouseOutOfRegion);
            map.data.addListener("click", mouseClickOfRegion);
            // wire up the button

            clearCensusData();
            loadCensusData("https://storage.googleapis.com/mapsdevsite/json/DP05_0017E");

            // const selectBox = document.getElementById("census-variable");
            // google.maps.event.addDomListener(selectBox, "change", () => {
            //     clearCensusData();
            //     loadCensusData(selectBox.options[selectBox.selectedIndex].value);
            //     // console.log(selectBox.options[selectBox.selectedIndex].value)
            // });


            // state polygons only need to be loaded once, do them now
            loadMapShapes();
        }

        /** Loads the state boundary polygons from a GeoJSON source. */
        function loadMapShapes() {
            // load US state outline polygons from a GeoJson file
            map.data.loadGeoJson(
                "https://storage.googleapis.com/mapsdevsite/json/states.js", {
                    idPropertyName: "STATE"
                }
            );
            // wait for the request to complete by listening for the first feature to be
            // added
            // google.maps.event.addListenerOnce(map.data, "addfeature", () => {
            //     google.maps.event.trigger(
            //         document.getElementById("census-variable"),"change"
            //     );
            // });
        }

        /**
         * Loads the census data from a simulated API call to the US Census API.
         *
         * @param {string} variable
         */
        function loadCensusData(variable) {
            // load the requested variable from the census API (using local copies)
            const xhr = new XMLHttpRequest();
            xhr.open("GET", variable + ".json");

            xhr.onload = function() {
                const censusData = JSON.parse(xhr.responseText);
                censusData.shift(); // the first row contains column names
                censusData.forEach((row) => {
                    const censusVariable = parseFloat(row[0]);
                    const stateId = row[1];

                    // keep track of min and max values
                    if (censusVariable < censusMin) {
                        censusMin = censusVariable;
                    }

                    if (censusVariable > censusMax) {
                        censusMax = censusVariable;
                    }
                    // update the existing row with the new data

                    //console.log("census:", stateId)

                    // map.data
                    //     .getFeatureById(stateId)
                    //     .setProperty("census_variable", censusVariable);
                });
                // update and display the legend
                // document.getElementById(
                //     "census-min"
                // ).textContent = censusMin.toLocaleString();
                // document.getElementById(
                //     "census-max"
                // ).textContent = censusMax.toLocaleString();
            };
            xhr.send();
        }

        /** Removes census data from each shape on the map and resets the UI. */
        function clearCensusData() {
            censusMin = Number.MAX_VALUE;
            censusMax = -Number.MAX_VALUE;
            // map.data.forEach((row) => {
            //     row.setProperty("census_variable", undefined);
            // });
            //document.getElementById("data-box").style.display = "none";
            //document.getElementById("data-caret").style.display = "none";
        }

        /**
         * Applies a gradient style based on the 'census_variable' column.
         * This is the callback passed to data.setStyle() and is called for each row in
         * the data set.  Check out the docs for Data.StylingFunction.
         *
         * @param {google.maps.Data.Feature} feature
         */
        function styleFeature(feature) {
            const low = [5, 69, 54]; // color of smallest datum
            const high = [151, 83, 34]; // color of largest datum
            // delta represents where the value sits between the min and max
            const delta =
                (feature.getProperty("census_variable") - censusMin) /
                (censusMax - censusMin);
            const color = [];

            for (let i = 0; i < 3; i++) {
                // calculate an integer color based on the delta
                color.push((high[i] - low[i]) * delta + low[i]);
            }
            // determine whether to show this shape or not
            let showRow = true;

            // if (
            //     feature.getProperty("census_variable") == null ||
            //     isNaN(feature.getProperty("census_variable"))
            // ) {
            //     showRow = false;
            // }
            let outlineWeight = 0.1,
                filedColor = "#9BCA82",
                filledOpacity = 0,
                zIndex = 1;

            if (feature.getProperty("state") === "hover") {
                outlineWeight = zIndex = 2;
                filledOpacity = 0.2
            }

            if (feature.getProperty("state") === "click") {
                outlineWeight = zIndex = 4;
                filedColor = "#FBCA82"
                filledOpacity = 0.1
            }

            // console.log(feature.getProperty("NAME"))
            return {
                strokeWeight: outlineWeight,
                strokeColor: "#588FE9",
                zIndex: zIndex,
                fillColor: filedColor,
                fillOpacity: filledOpacity,
                visible: showRow,
            };
        }

        /**
         * Responds to the mouse-in event on a map shape (state).
         *
         * @param {?google.maps.MouseEvent} e
         */
        function mouseInToRegion(e) {
            // set the hover state so the setStyle function can change the border
            e.feature.setProperty("state", "hover");
            // const percent =
            //     ((e.feature.getProperty("census_variable") - censusMin) /
            //         (censusMax - censusMin)) * 100;
            // update the label
            document.getElementById(
                "data-label"
            ).textContent = e.feature.getProperty("NAME");

            //console.log(e.feature.getProperty("NAME"))
            // document.getElementById(
            //         "data-value"
            //     ).textContent = e.feature
            //     .getProperty("census_variable")
            //     .toLocaleString();
            // document.getElementById("data-box").style.display = "block";
            // document.getElementById("data-caret").style.display = "block";
            // document.getElementById("data-caret").style.paddingLeft = percent + "%";
        }

        function mouseClickOfRegion(e) {
            e.feature.setProperty("state", "click");
        }

        /**
         * Responds to the mouse-out event on a map shape (state).
         *
         */
        function mouseOutOfRegion(e) {
            // reset the hover state, returning the border to normal
            e.feature.setProperty("state", "normal");
        }
    </script>
</head>

<body>
@extends('layouts.app')

@section('content')
    <div id="data-box" class="nicebox" style="display:none">
        <label id="data-label" for="data-value"></label>
        <span id="data-value"></span>
    </div>
    <div id="map" style="margin-top:-20px;"></div>
@endsection

<body>
