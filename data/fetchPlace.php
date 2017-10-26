<?php
	/* Reece Benson */
    /* BSc Comp Sci */
    
	/**
	 * Requirements
	 */
    require_once('../sys/core.php');

	/**
	 * Response to be sent back to the webpage that requested this (ajax call)
     * 
     * @var array 
	 */
    $resp = array();

    /**
     * Ensure that we have received some data
     */
    if(!isset($_POST)) {
        $resp['status'] = 500;
        $resp['recv'] = array();
        $resp['no_post'] = true;
        die(json_encode($resp, JSON_PRETTY_PRINT));
    }

	/**
	 * Setup our response back to the webpage
     * 
     * @var int   status The status of the response
     * @var array The data that was received the webpage that requested this
	 */
    $resp['status'] = 200;
    $resp['recv'] = $_POST;

    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // REQUEST PLACE INFORMATION
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	/**
	 * @var string The WOEID sent by the webpage that requested this (POST)
	 */
    $woeId = $_POST['woeid'];

    /**
     * @var array resp_json    This will hold the information about the place requested (this is done by the Yahoo WOE ID)
     * @var array place        This will hold the formatted information from the data received from 'resp_json'
     */
    $resp['resp_json'] = Places::queryPlaceByWOEID($woeId)['query'];
    $resp['place'] = Places::formatPlace($resp['resp_json']['results']['place']);

    /**
     * @var string resp_details This data will be sent back to be displayed as HTML to the webpage that requested this
     */
    $resp['resp_details'] = '<table style="width: 100%; margin: 0 auto;">
        <tbody>
            <tr class="content"><td style="font-weight: bold;">Name</td> <td>' . $resp['place']['name'] . '</td></tr>
            <tr class="content"><td style="font-weight: bold;">Type</td> <td>' . $resp['place']['type'] . '</td></tr>
            <tr class="content"><td style="font-weight: bold;">County</td> <td>' . $resp['place']['county']['name'] . '</td></tr>
            <tr class="content"><td style="font-weight: bold;">State</td> <td>' . $resp['place']['state']['name'] . '</td></tr>
            <tr class="content"><td style="font-weight: bold;">Country</td> <td>' . $resp['place']['country']['name'] . '</td></tr>
            <tr class="content"><td style="font-weight: bold;">Post Code</td> <td>' . $resp['place']['postcode']['name'] . '</td></tr>
            <tr class="content"><td style="font-weight: bold;">WOE ID</td> <td>' . $resp['place']['woe_id'] . '</td></tr>
            <tr class="content"><td style="font-weight: bold;">Latitude / Longitude</td> <td>' . $resp['place']['location']['latitude'] . ' / ' . $resp['place']['location']['longitude'] . '</td></tr>
        </tbody>
    </table>';

    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // REQUEST PLACE WEATHER
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    
    /**
     * @var array weather   This will hold the weather data in an array from the specified latitude and longitude
     */
    $resp['weather'] = Places::queryPlaceWeather($resp['place']['location']['latitude'], $resp['place']['location']['longitude']);

    /**
     * If the degree from the weather response is unset/null, we'll set it as "undefined" so that we do not error
     */
    if(!isset($resp['weather']['wind']['deg'])) { $resp['weather']['wind']['deg'] = "<em>undefined&nbsp;</em>"; }

    /**
     * Build our weather response
     */
    $weather = "";
    foreach($resp['weather']['weather'] as $w) {
        $weather .= $w['main'] . ", ";
    }
    $weather = substr($weather, 0, -2);

    /**
     * @var string resp_weather This data will be sent back to be displayed as HTML to the webpage that requested this
     */
    $resp['resp_weather'] = '<table style="width: 100%; margin: 0 auto;">
    <tbody>
        <tr class="content"><td style="font-weight: bold;">Weather</td> <td>' . $weather . '<br/>(' . $resp['weather']['clouds']['all'] . '% clouds)</td></tr>
        <tr class="content"><td style="font-weight: bold;">Current Temperature</td> <td>' . ($resp['weather']['main']['temp']-273.15) . '&#8451;</td></tr>
        <tr class="content"><td style="font-weight: bold;">Temperature Min/Max</td> <td>' . ($resp['weather']['main']['temp_min']-273.15) . '&#8451;/' . ($resp['weather']['main']['temp_max']-273.15) . '&#8451;</td></tr>
        <tr class="content"><td style="font-weight: bold;">Humidity</td> <td>' . $resp['weather']['main']['humidity'] . '%</td></tr>
        <tr class="content"><td style="font-weight: bold;">Wind Speed</td> <td>' . $resp['weather']['wind']['speed'] . 'm/s</td></tr>
        <tr class="content"><td style="font-weight: bold;">Wind Direction</td> <td>' . $resp['weather']['wind']['deg'] . '&deg;</td></tr>
        <tr class="content"><td style="font-weight: bold;">Time of Sunrise</td> <td>' . date("H:i:sa", $resp['weather']['sys']['sunrise']) . '</td></tr>
        <tr class="content"><td style="font-weight: bold;">Time of Sunset</td> <td>' . date("H:i:sa", $resp['weather']['sys']['sunset']) . '</td></tr>
    </tbody>
</table>';

    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // REQUEST PLACE TWEETS
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    /**
     * TODO: Move this tweet data into `places.php` for better organisation
     * 
     * @var string url      The URL we will be querying
     * @var string geocode  The location we will be querying
     * @var string getfield The built query to be sent to the Twitter API
     */
    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $geocode = "geocode=" . $resp['place']['location']['latitude'] . "," . $resp['place']['location']['longitude'] . ",5km";
    $getfield = "?result_type=recent&" . $geocode . "&count=200";
    
    /**
     * Initialise the Twitter API we have been provided
     */
    $twitter = new TwitterAPIExchange($tw_details);
    
    /**
     * Get the response from the Twitter API
     * 
     * @var array resp_twitter_raw This holds the JSON array data that has been received from the Twitter API
     */
    $resp['resp_twitter_raw'] = json_decode($twitter->setGetfield($getfield)
        ->buildOauth($url, "GET")
        ->performRequest(), true);

    /**
     * Push our raw twitter response into something readable
     * 
     * @var string resp_tweets This data will be sent back to be displayed as HTML to the webpage that requested this
     */
    $resp['resp_tweets'] = "";

    /**
     * Cycle through the tweets received from the API and display them into HTML
     */
    foreach($resp['resp_twitter_raw']['statuses'] as $tweet)
    {
        /**
         *  Only print out solid tweets with no replies or retweets
         */
        $timestamp = strtotime($tweet['created_at']);
        if(!$tweet['retweeted'] && count($tweet['entities']['user_mentions']) == 0)
        {
            $resp['resp_tweets'] .= '<div class="row">
                <div class="col-sm-2">
                    <img src="' . $tweet['user']['profile_image_url'] . '" style="width: 32px; height: 32px; border-radius: 8px;">
                </div>
                <div class="col-sm-8" style="padding: 5px;">
                    <strong>@' . $tweet['user']['screen_name'] . '</strong> <small>(' . $site->timeago($timestamp) . ')</small>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10 content">
                    ' . $tweet['text'] . '
                </div>
                <div class="col-sm-1"></div>
            </div>
            <br/>';
        }
    }

    // > Echo our response as JSON
    die(json_encode($resp, JSON_PRETTY_PRINT));
?>