<?php
	/**
	 * Homepage
	 *
	 * PHP version 5.6.30
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies, Daisy
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
	 */

    /**
     * Map Styler
     * 
     * @link https://mapstyle.withgoogle.com/
     */

	/**
	 * Requirements
	 */
    require_once('sys/core.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Homepage | <?=$site->getSystemInfo("site_name_long");?></title>

		<?php require_once('pages/header.php'); ?>

        <style type="text/css">
            @import url(http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,300,400,700);
            #map_left, #map_right {
                height: 100%;
                color: black;
            }

            html,body {
                color: black !important;
                font-family: "Open Sans", sans-serif;
            }
            .photo_title{
                color:white;
                font-size: 20px;
            }
        </style>
        <style type="text/css">        
            .slideshow { height: 560px; width: 530px; margin: auto }
            .slideshow img { padding: 7px; border-radius: 3px;;border: 1px solid #ccc; background-color: #eee; }
        </style>
	</head>

	<body>
		<div class="container-fluid">
            <div class="row">
                <div class="col-md" style="min-height: 200px; background-color: white; border-radius: 3px; margin: 5px; padding: 5px; text-align: center; font-weight: bold;">
                    <span style="font-size: 32px;"><?=$cities['city_one']['name'];?></span><br/>
                    <span id="city_one_timezone">Loading...</span><br/><br/>
                    <span id="city_one_population">Population: <?=number_format($cities['city_one']['population']);?></span><br/>
                    <span id="city_one_squareft">City Square ft.: <?=number_format($cities['city_one']['square_ft']);?>sqft</span>
                </div>
                <div class="col-md" style="min-height: 200px; background-color: white; border-radius: 3px; margin: 5px; padding: 5px; text-align: center; font-weight: bold;">
                    <span style="font-size: 32px;"><?=$cities['city_two']['name'];?></span><br/>
                    <span id="city_two_timezone">Loading...</span><br/><br/>
                    <span id="city_two_population">Population: <?=number_format($cities['city_two']['population']);?></span><br/>
                    <span id="city_two_squareft">City Square ft.: <?=number_format($cities['city_two']['square_ft']);?>sqft</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md" style="min-height: 600px; background-color: white; border-radius: 3px; margin: 5px; padding: 0px;">
                    <div id="map_left" style="border-radius: 10px;"></div>
                </div>  
                <div class="col-md" style="min-height: 600px; background-color: white; border-radius: 3px; margin: 5px; padding: 0px;">
                    <div id="map_right" style="border-radius: 10px;"></div>
                </div>
            </div>

            <?php require_once('sys/classes/photos.php');?>        

            <div class="row">
                <div class="col-md" style="min-height: 200px; background-color: eee; border-radius: 3px; margin: 5px; padding: 0px;">
                    <div id="photos_left" style="border-radius: 10px; overflow:auto">
                        <div class="slideshow" style="position: relative;">
                            <?php for ($i=0; $i < 50; $i++) { 
                                echo("<span class='photo_title'>".$city_photos_one[$i]['title']."</span>");
                                echo("<a href=".$city_photos_one[$i]['user_url']." target='_blank'>");
                                echo("<img src=".$city_photos_one[$i]['url']." width='530' height='530' style=position: absolute; top: 0px; left: 0px; display: none; z-index: 5; opacity: 0; width: 530; height: 530;></a>");
                            } ?>    
                        </div>
                    </div>
                </div>
                <div class="col-md" style="min-height: 200px; background-color: eee; border-radius: 3px; margin: 5px; padding: 0px;">
                    <div id="photos_right" style="border-radius: 10px;overflow:auto">
                        <div class="slideshow" style="position: relative;">
                            <?php for ($i=0; $i < 50; $i++) { 
                                echo("<span class='photo_title'>".$city_photos_two[$i]['title']."</span>"); 
                                echo("<a href=".$city_photos_two[$i]['user_url']." target='_blank'>");
                                echo("<img src=".$city_photos_two[$i]['url']." width='530' height='530' style=position: absolute; top: 0px; left: 0px; display: none; z-index: 5; opacity: 0; width: 530; height: 530;></a>");
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

		<?php require_once('pages/scripts.php'); ?>

        <script type="text/javascript">
            
            const $ = jQuery;

            function updateTimezones() {
                // Declare Elements
                const cityLeft = $("#city_one_timezone");
                const cityRight = $("#city_two_timezone");

                // Get our formatted strings for the timezones on our cities
                const cityLeftTimezone = moment().tz('<?=$cities['city_one']['timezone'];?>').format('MMMM Do YYYY, h:mm:ss a');
                const cityRightTimezone = moment().tz('<?=$cities['city_two']['timezone'];?>').format('MMMM Do YYYY, h:mm:ss a');

                // Set elements data
                cityLeft.html(cityLeftTimezone);
                cityRight.html(cityRightTimezone);

                // Recursive (every 1000ms)
                setTimeout(updateTimezones, 1000);
            }

            $(document).ready(function() {
                // Update our timezones
                updateTimezones();
            });
        </script>

        <script type="text/javascript">
            // This example requires the Places library. Include the libraries=places
            // parameter when you first load the API. For example:
            // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

            var mapLeft;
            var mapRight;
            var infowindow;

            var mapStyle = [
                {
                    "featureType": "administrative",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "administrative.land_parcel",
                    "elementType": "labels",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "road.local",
                    "elementType": "labels",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "transit",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                }
            ];

            function initMap() {
                initMapLeft();
                initMapRight();
            }

            function initMapLeft() {
                var pyrmont = { lat: <?=$cities['city_one']['lat'];?>, lng: <?=$cities['city_one']['long'];?> };

                mapLeft = new google.maps.Map(document.getElementById('map_left'), {
                    mapTypeControlOptions: {
                        mapTypeIds: ['mapstyle']
                    },
                    center: pyrmont,
                    zoom: 15,
                    mapTypeId: 'mapStyle'
                });
                mapLeft.mapTypes.set('mapStyle', new google.maps.StyledMapType(mapStyle, { name: "Default Style" }));

                infowindow = new google.maps.InfoWindow();
                var service = new google.maps.places.PlacesService(mapLeft);

                service.nearbySearch({
                    location: pyrmont,
                    radius: 500,
                    //type: 
                    type: ['food']
                }, callbackLeft);
            }

            function initMapRight() {
                var pyrmont = { lat: <?=$cities['city_two']['lat'];?>, lng: <?=$cities['city_two']['long'];?> };

                mapRight = new google.maps.Map(document.getElementById('map_right'), {
                    mapTypeControlOptions: {
                        mapTypeIds: ['mapstyle']
                    },
                    center: pyrmont,
                    zoom: 15,
                    mapTypeId: 'mapStyle'
                });
                mapRight.mapTypes.set('mapStyle', new google.maps.StyledMapType(mapStyle, { name: "Default Style" }));

                infowindow = new google.maps.InfoWindow();
                var service = new google.maps.places.PlacesService(mapRight);
                service.nearbySearch({
                    location: pyrmont,
                    radius: 500,
                    type: ['food']
                }, callbackRight);
                console.log("calllbakck done");
            }

            function callbackLeft(results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    for (var i = 0; i < results.length; i++) {
                        createMarker(results[i], mapLeft);
                        console.log(results[i]);
                    }
                }
            }

            function callbackRight(results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    for (var i = 0; i < results.length; i++) {
                        createMarker(results[i], mapRight);
                        console.log(results[i]);
                    }
                }
            }

            function createMarker(place, m) {
                var placeLoc = place.geometry.location;
                var marker = new google.maps.Marker({
                    map: m,
                    position: place.geometry.location
                });

                google.maps.event.addListener(marker, 'mouseover', function() {
                    infowindow.setContent(place.name);
                    infowindow.open(m, this);
                });
            }

        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqtxJ8-MzY4Dvr6HDDwasownTMIvXYHXk&libraries=places&callback=initMap" async defer></script>
       
    </body>
</html>