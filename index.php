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
     * TODO:
     * - When selecting nav items on the left (Home, PoI, Twitter, Flickr), it will ajax update
     *   the content. (Twitter whilst on "Manchester" will show tweets from Manchester, etc.)
     * 
     * NOTE:
     * - This is Reece's page, so just leave it :)
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

        <!-- HEADER REQUIREMENT -->
		<link rel="icon" type="image/png" href="<?=$www;?>/gallery/img/favicon.png">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" rel="stylesheet">
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" type="text/css" rel="stylesheet">
		<link href="<?=$www;?>/gallery/css/index.css" type="text/css" rel="stylesheet">

        <style>        
            .slideshow { height: 560px; width: 530px; margin: auto }
            .slideshow img { padding: 7px; border-radius: 3px; border: 1px solid #ccc; background-color: #eee; }
        </style>
    </head>
    <body style="margin: 0 auto;">
        <div class="container">
            <div id="content-container">
                <div id="header">
                    <?=$cities['city_one']['name'];?> and <?=$cities['city_two']['name'];?>
                </div>
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto" id="navbarNav">
                            <li class="nav-item active">
                                <a class="nav-link" href="#" id="btnHome"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnPoi"><i class="fa fa-map-marker"></i> Points of Interest</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnTwitter"><i class="fa fa-twitter"></i> Twitter</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnFlickr"><i class="fa fa-flickr"></i> Flickr</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="cityOneClick"><?=$cities['city_one']['name'];?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="cityTwoClick"><?=$cities['city_two']['name'];?></a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div id="ajaxContent">
                    <div style="text-align: center; padding-top: 25px;">
                        <h3>Please select a city from the top right</h3>
                    </div>
                </div>
            </div>
            <div class="footer">
                Copyright <?=@date("Y");?> &copy; <a href="<?=$www;?>"><?=$site->getSystemInfo("authors");?></a> | <a href="https://validator.w3.org/nu/?doc=http%3A%2F%2Fuwe.reecebenson.me%2Fdsa-twincities%2F" target="_blank">Validate W3C</a>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/locale/en-gb.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqtxJ8-MzY4Dvr6HDDwasownTMIvXYHXk&libraries=places" async defer></script>
        <script src="<?=$www;?>/gallery/js/moment.timezone.js"></script>
        <script>
            /**
             * Variables
             */
            let $ = jQuery;
            let base = $(document);
            let currentCity = null;

            /**
             * City Data
             */
            <?php
                $cityOneData = json_encode($site->getCityData($cities['city_one']['woeid']));
                $cityTwoData = json_encode($site->getCityData($cities['city_two']['woeid']));

                /*$cityOneData = json_encode($cities['city_one']);
                $cityTwoData = json_encode($cities['city_two']);*/
            ?>
            let cityOne = <?=($cityOneData!=null ? $cityOneData : "{}");?>;
            let cityTwo = <?=($cityTwoData!=null ? $cityTwoData : "{}");?>;

            /**
             * Function for when waiting for a tab to load
             */
            function waitForLoad() {
                /**
                 * Elements
                 */
                 let ajaxContent = $("#ajaxContent");

                 // Set Element HTML
                 ajaxContent.html("<div style='text-align: center; margin-top: 25px;'><img src='<?=$www;?>/gallery/img/load.gif'></div>");
            }

            /**
             * Function to fetch weather via AJAX Call
             */
            function fetchWeather(woeId, lat, long) {
                /**
                 * Elements
                 */
                // Map
                let mapDiv = $("#map");
                // Weather
                let weatherContent = $("#ajaxWeather");
                let weatherName = weatherContent.find("#name");
                let weatherInfo = weatherContent.find("#data");
                let weatherIcon = weatherContent.find("#ajaxWeatherIcon");
                let weatherTime = weatherContent.parent().find(".last-pull");
                // Information
                let informationContent = $("#ajaxInformation");
                // Points of Interest
                let poiContent = $("#ajaxPointsOfInterest");

                let tempDiff = 273.15;
                $.ajax({
                    method: 'POST',
                    dataType: 'json',
                    url: './data/fetchWeather.php',
                    async: false,
                    timeout: 30000,
                    data: { woeid: woeId, latitude: lat, longitude: long },
                    error: () => {
                        weatherName.html("There was an issue trying to fetch the data.");
                        weatherTime.html("<a href='javascript:fetchWeather(" + woeId + ", " + lat + ", " + long + ");'>Refresh</a>");
                        weatherInfo.html("");
                        weatherContent.html("");
                    },
                    success: (result) => {
                        // Deconstruct Variables
                        let weatherData = result.weather;
                        let tempCurrent = Math.floor(weatherData.main.temp - tempDiff);
                        let tempMin = Math.floor(weatherData.main.temp_min - tempDiff);
                        let tempMax = Math.floor(weatherData.main.temp_max - tempDiff);

                        // Information
                        let tempString = "Currently " + tempCurrent + "&#8451;, from " + tempMin + "&#8451; to " + tempMax + "&#8451;";
                        let windString = "Wind: " + weatherData.wind.speed + "m/s, " + weatherData.wind.deg + "&deg; (" + getCardinalDirection(weatherData.wind.deg) + ")";
                        let sunRiseSet = "Sunrise: " + weatherData.sunrise + ", Sunset: " + weatherData.sunset;

                        // Set Weather Icon
                        weatherName.html("<strong>" + toTitleCase(weatherData.weather[0].description) + "</strong> <small>(" + weatherData.clouds.all + "% clouds)</small>");
                        weatherInfo.html(tempString + "<br/>" + windString + "<br/>" + sunRiseSet);
                        weatherTime.html("Last updated " + result.timeago + " | <a href='javascript:fetchWeather(" + woeId +", " + lat + ", " + long + ");'>Refresh</a>");
                        weatherIcon.attr("src", "http://openweathermap.org/img/w/" + weatherData.weather[0].icon + ".png");

                        // Debug
                        console.log(result);
                    }
                });
            }

            /**
             * Function to fetch Information via AJAX Call
             */
             function fetchInformation(woeId) {
                 
                /**
                 * Elements
                 */
                let ajaxInfo = $("#ajaxInformation");

                // Submit Ajax Request
                $.ajax({
                    method: 'POST',
                    dataType: 'json',
                    url: './data/fetchInfo.php',
                    async: false,
                    timeout: 30000,
                    data: { woeid: woeId },
                    error: () => {
                        ajaxInfo.html("There was an issue trying to fetch the data.");
                    },
                    success: (result) => {
                        let cityInfo = result.city;
                        let info = "";

                        // Build City Information
                        info += "<div class='row'>";
                        info += "<div class='col-5' style='font-weight:bold; text-align: right;'>Name:<br/>Population:<br/>Timezone:<br/>Lat/Long:<br/>Current Time:</div>";
                        info += "<div class='col'>" + cityInfo.name + "<br/>" + cityInfo.population + "<br/>" + cityInfo.timezone + "<br/>" + cityInfo.lat + "," + cityInfo.long + "<br/><div id='currentTimezone'>Calculating...</div></div>";
                        info += "</div>";

                        // Present Information
                        ajaxInfo.html(info);

                        // Calculate Time
                        updateTimezones(cityInfo.timezone, cityInfo.timezone);

                        // Debug
                        console.log(result);
                    }
                });
             }

            /**
             * Convert each first letter of each word to Upper Case
             */
            function toTitleCase(str)
            {
                return str.replace(/\w\S*/g, function(txt) { return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(); });
            }

            /**
             * Convert Degrees into Cardinal Direction
             */
            function getCardinalDirection(angle) {
                if (typeof angle === 'string') angle = parseInt(angle);
                if (angle <= 0 || angle > 360 || typeof angle === 'undefined') return '☈';
                const arrows = { north: '↑ N', north_east: '↗ NE', east: '→ E', south_east: '↘ SE', south: '↓ S', south_west: '↙ SW', west: '← W', north_west: '↖ NW' };
                const directions = Object.keys(arrows);
                const degree = 360 / directions.length;
                angle = angle + degree / 2;
                for (let i = 0; i < directions.length; i++) {
                    if (angle >= (i * degree) && angle < (i + 1) * degree) return arrows[directions[i]];
                }
                return arrows['north']; // < Fallback
            }

            /****************************
             * MAP
             ****************************/
            var map;
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

            function initialiseMap(_lat, _long) {
                let latlong = { lat: _lat, lng: _long };

                // Create our Map
                map = new google.maps.Map(document.getElementById("map"), {
                    mapTypeControlOptions: {
                        mapTypeIds: ['mapstyle']
                    },
                    center: latlong,
                    zoom: 12,
                    mapTypeId: 'mapStyle'
                });
                map.mapTypes.set('mapStyle', new google.maps.StyledMapType(mapStyle, { name: "Default Style" }));

                // Generate Markers
                let markers = [];
                for(var i = 0; i < currentCity.poi; i++) {
                    let place = currentCity.poi[i];
                    console.log(place);
                }

                // Create Markers
            }

            function executeCity(woeId, lat, long, page) {
                // Get our content holder
                let contentHolder = $("#ajaxContent");

                // Get our specified page
                waitForLoad();
                switch(page) {
                    default: case "home": {
                        $.get("./pages/main.php", function(data) {
                            // Replace HTML with the data inside of content
                            contentHolder.html(data);

                            // Initialise City Data
                            initialiseMap(lat, long);
                            fetchWeather(woeId, lat, long);
                            fetchInformation(woeId);
                        });
                    };
                    break;

                    case "poi": {
                        $.get("./pages/poi.php?woeid="+woeId, function(data) {
                            // Replace HTML with the data inside of content
                            contentHolder.html(data);

                            // Initialise Points of Interest Table
                            //initialisePOI(woeId);

                        });
                    };
                    break;

                    case "specific_poi": {
                        $.get("./pages/singlepoi.php?woeid="+woeId+"&id=null", function(data) {
                            // Replace HTML with the data inside of content
                            contentHolder.html(data);
                        });
                    };
                    break;

                    case "twitter": {
                        $.get("./pages/twitter.php?woeid="+woeId, function(data) {
                            // Replace HTML with the data inside of content
                            contentHolder.html(data);
                        });
                    };
                    break;

                    case "flickr": {
                        $.get("./pages/flickr.php?woeid=" + woeId, function(data) {
                            // Replace HTML with the data inside of content
                            contentHolder.html(data);
                        });
                    };
                    break;
                }
            }

            /**
             * Navigation Bar Button Reset
             */
            function removeActiveButtons() {
                let navbar = $("#navbarNav");
                navbar.each(function() {
                    $(this).find("li").each(function() {
                        let navbarItem = $(this);
                        navbarItem.removeClass("active");
                    });
                });
            }
            
            /**
             * Function to update timezone text
             */
            function updateTimezones(tz, currentTz) {
                // Check if we're still running the same city
                if(tz != currentTz)
                    return;

                // Declare Elements
                const timezoneArea = $("#currentTimezone");

                // Check if our element exists
                setTimeout(() => {
                    // Check if our element exists
                    if(timezoneArea != null) {
                        // Get our formatted strings for the timezones on our cities
                        const cityTimezone = moment().tz(tz).format('MMMM Do YYYY, h:mm:ss a');

                        // Update HTML
                        timezoneArea.html(cityTimezone);

                        // Recursive Function
                        updateTimezones(tz, currentCity.timezone);
                    }
                }, 1000);
            }

            /**
             * Execute AJAX Calls when browser is ready
             */
            base.ready(() => {
                /**
                 * Setup Clickers
                 */
                let currentPage = "home";
                let btnCityOne = $("#cityOneClick");
                let btnCityTwo = $("#cityTwoClick");
                let btnHome = $("#btnHome");
                let btnPoi = $("#btnPoi");
                let btnTwitter = $("#btnTwitter");
                let btnFlickr = $("#btnFlickr");

                /**
                 * Setup Listeners
                 */
                btnCityOne.click(function() {
                    btnCityOne.parent().addClass("active");
                    btnCityTwo.parent().removeClass("active");
                    btnPoi.removeClass("disabled");
                    btnTwitter.removeClass("disabled");
                    btnFlickr.removeClass("disabled");
                    currentCity = cityOne;
                    console.log(currentCity);
                    executeCity(cityOne.woeid, cityOne.lat, cityOne.long, currentPage);
                });

                btnCityTwo.click(function() {
                    btnCityTwo.parent().addClass("active");
                    btnCityOne.parent().removeClass("active");
                    btnPoi.removeClass("disabled");
                    btnTwitter.removeClass("disabled");
                    btnFlickr.removeClass("disabled");
                    currentCity = cityTwo;
                    executeCity(cityTwo.woeid, cityTwo.lat, cityTwo.long, currentPage);
                });

                btnHome.click(function() {
                    if(currentCity == null) return;
                    executeCity(currentCity.woeid, currentCity.lat, currentCity.long, "home");
                    currentPage = "home";
                    removeActiveButtons();
                    btnHome.parent().addClass("active");
                });

                btnPoi.click(function() {
                    if(currentCity == null) return;
                    executeCity(currentCity.woeid, currentCity.lat, currentCity.long, "poi");
                    currentPage = "poi";
                    removeActiveButtons();
                    btnPoi.parent().addClass("active");
                });

                btnTwitter.click(function() {
                    if(currentCity == null) return;
                    executeCity(currentCity.woeid, currentCity.lat, currentCity.long, "twitter");
                    currentPage = "twitter";
                    removeActiveButtons();
                    btnTwitter.parent().addClass("active");
                });

                btnFlickr.click(function() {
                    if(currentCity == null) return;
                    executeCity(currentCity.woeid, currentCity.lat, currentCity.long, "flickr");
                    currentPage = "flickr";
                    removeActiveButtons();
                    btnFlickr.parent().addClass("active");
                });

                /**
                 * Disable buttons whilst currentCity is null
                 */
                if(currentCity == null) {
                    btnPoi.addClass("disabled");
                    btnTwitter.addClass("disabled");
                    btnFlickr.addClass("disabled");
                }

                /**
                 * Ready!
                 */
                console.log("Ready!");
            });
        </script>
    </body>
</html>