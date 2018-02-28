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
	 * Requirements
	 */
	require_once('../sys/core.php');
	
	/**
	 * Check if we have been passed a Woe ID
	 */
	if(!isset($_REQUEST['woeid']))
		die('Unable to load data. WOE ID was not received.');

	/**
	 * Get our city data from WOE ID
	 */
	$woeid = $_REQUEST['woeid'];
	$city = $site->getCityData($woeid);

    /**
     * Initialise the Twitter API we have been provided
     */
	$url = 'https://api.twitter.com/1.1/search/tweets.json';
    $geocode = "geocode=" . $city['lat'] . "," . $city['long'] . ",5km";
	$getfield = "?result_type=recent&" . $geocode . "&count=200&tweet_mode=extended";
    $twitter = new TwitterAPIExchange($tw_details);
    
    /**
     * Get the response from the Twitter API
     * 
     * @var array resp_twitter_raw This holds the JSON array data that has been received from the Twitter API
     */ 
    $twitter_resp = json_decode($twitter->setGetfield($getfield)
        ->buildOauth($url, "GET")
        ->performRequest(), true);
?>
<div class="container">
    <div id="content">
		<?php
			/* Iterate through Tweets */
			foreach($twitter_resp['statuses'] as $tweet) {
				if(isset($tweet['retweeted_status']))
					continue;

				$tweet['stamp'] = strtotime($tweet['created_at']);
				echo '<div class="row" style="min-height: 60px; border-bottom: 1px solid #ccc; margin-bottom: 15px; margin: 15px 0;">';
				echo '<div class="col-1"><img src="' . $tweet["user"]["profile_image_url"] . '"></div>';
				echo '<div class="col">';
				echo '<strong>@' . $tweet["user"]["screen_name"] . '</strong> <small>(' . $site->timeago($tweet['stamp']) . ')</small>';
				echo '<br/>' . $tweet["full_text"] . '</div>';
				echo '</div>';
			}

			echo "<script type='text/javascript'>console.log(" . json_encode($twitter_resp['statuses'], JSON_PRETTY_PRINT) . ");</script>";
		?>
    </div>
</div>