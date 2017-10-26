<?php
	/* Reece Benson */
	/* BSc Comp Sci */
    require_once('../sys/core.php');

    // > Variables
    $resp = array();

    if(!isset($_POST)) {
        $resp['status'] = 500;
        $resp['recv'] = array();
        $resp['no_post'] = true;
        die(json_encode($resp, JSON_PRETTY_PRINT));
    }

    // > Setup our response
    $resp['status'] = 200;
    $resp['recv'] = $_POST;

    // > Request our places
    $woeId = $_POST['woeid'];
    $resp['resp_json'] = Places::queryPlaceByWOEID($woeId)['query'];
    $resp['place'] = Places::formatPlace($resp['resp_json']['results']['place']);
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

    // > Request places weather
    $resp['weather'] = Places::queryPlaceWeather($resp['place']['location']['latitude'], $resp['place']['location']['longitude']);
    if(!isset($resp['weather']['wind']['deg'])) { $resp['weather']['wind']['deg'] = "<em>undefined&nbsp;</em>"; }

    // > Build Weather
    $weather = "";
    foreach($resp['weather']['weather'] as $w) {
        $weather .= $w['main'] . ", ";
    }
    $weather = substr($weather, 0, -2);

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

    // > Request place tweets within 1km [,#UWEBristol] for searching hashtags
    // "geocode:LAT,LONG,1km" or "geocode:LAT,LONG,1km,#UWEBristol"
    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $geocode = "geocode=" . $resp['place']['location']['latitude'] . "," . $resp['place']['location']['longitude'] . ",5km";
    $getfield = "?result_type=recent&" . $geocode . "&count=200";
    
    $twitter = new TwitterAPIExchange($tw_details);
    $resp['resp_twitter_raw'] = json_decode($twitter->setGetfield($getfield)
        ->buildOauth($url, "GET")
        ->performRequest(), true);

    // > Push our raw twitter response into something readable
    $resp['resp_tweets'] = "";
    foreach($resp['resp_twitter_raw']['statuses'] as $tweet)
    {
        // > Only print out solid tweets with no replies or retweets
        if(!$tweet['retweeted'] && count($tweet['entities']['user_mentions']) == 0)
        {
            $resp['resp_tweets'] .= '<div class="row">
                <div class="col-sm-4">
                    <img src="' . $tweet['user']['profile_image_url'] . '" style="width: 32px; height: 32px; border-radius: 8px;">
                </div>
                <div class="col-sm-5" style="padding: 5px;">
                    <strong>@' . $tweet['user']['screen_name'] . '</strong>
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
        } else
            $tweet['resp_tweets'] = "|" . $tweet['is_quote_status'] . "<br/>";
    }

    // > Echo our response as JSON
    die(json_encode($resp, JSON_PRETTY_PRINT));
?>