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

	// Only call with ajax
	//require_once('../sys/classes/photos.php');
	
	/**
	 * Check if we have been passed a Woe ID
	 */

	if(!isset($_REQUEST['woeid']))
		die('Unable to load data. WOE ID was not received.');

	/**
	 * Get our city data from WOE ID
	 */

	$woeid = $_REQUEST['woeid'];

	/**
	 * Classify Photo Arrays
	 */

	$photos[$cities['city_one']['woeid']] = getPhotosFromDB($cities['city_one']['woeid']);
	$photos[$cities['city_two']['woeid']] = getPhotosFromDB($cities['city_two']['woeid']);

	//$photos[$cities['city_one']['woeid']] = $city_photos_one;
	//$photos[$cities['city_two']['woeid']] = $city_photos_two;


	function getPhotosFromDB($cityID){
		global $db;

		$statement = $db->prepare("SELECT * FROM `images` WHERE `city_id` = ?");
		$statement->bindParam(1, $cityID);

		$statement->execute();

		$city_photos = $statement->fetchAll(PDO::FETCH_ASSOC);

		return $city_photos;
	}
?>
<head>
	<link href="<?=$www;?>/gallery/css/index.css" type="text/css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript">
		function fetchNewPhotos() {

			$.ajax({
				method: 'POST',
				dataType: 'json',
				url: 'http://uwe.reecebenson.me/dsa-twincities/sys/classes/photos.php',
				timeout:300000000,
				cache: false,

				error: () =>{
					console.log("Not Working");
				},

				success: (result) =>{
					console.log("All is good");
				}

			});

		}
	</script>

</head>


<style type="text/css">
	.carousel-backdrop {
		background-color: rgba(0, 0, 0, 0.5);
		border-radius: 15px;
	}

	.button-update{
		height: 52px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		font-size: 16px;
	}

	#flickr-options{	
		height: 10em;
		display: flex;
		align-items: center;
		justify-content: center }
	}

</style>
<div class="container">
	<div id="content">
		<div class="row" id="placeInfo">
			<div class="col-sm">
				<div class="title" style="font-size: 20px">Flickr Options</div>
				<div class="content" id="flickr-options">
					<table>
						<tr><td><textarea rows="3" cols="30" placeholder="Enter tags followed by commas.."></textarea></td>
						<td><button id="update" type="button" onclick="fetchNewPhotos()" class="button-update">Load New Photos</button></td></tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div id="carouselIndicator" class="carousel slide" data-ride="carousel">
		<ol class="carousel-indicators">
			<li data-target="#carouselIndicator" data-slide-to="0" class="active"></li>
			<?php for($i = 1; $i < 20; $i++) { ?>
			<li data-target="#carouselIndicator" data-slide-to="<?=$i;?>"></li>
			<?php } ?>
		</ol>
		<div class="carousel-inner">
			<?php

				var_dump($photos[$woeid]);
				/**
				 * Loop through available photos
				 */
				for($i = 0; $i < 20; $i++) {
					// Get our photo
					$photo = $photos[$woeid][$i];
					
					// Display
					echo '<div class="carousel-item ' . ($i == 0?"active":"") .'">';
					echo '<img class="d-block w-100" src="' . $photo["source"] . '" alt="' . $photo["title"] . '">';
					echo '<div class="carousel-caption d-none d-md-block carousel-backdrop">';
					echo '<h5>' . $photo["title"] . ' </h5>';
					echo '<p>'. $photo["desc"] . '</p>';
					echo '<p>Date Taken: '. $photo['date_taken'] . '</p>';
					echo '<p><a target="_blank" href="' . $photo["user_url"] . '">View Photo on Flickr</a></p>';
					echo '</div>';
					echo '</div>';
				}
			?>
		</div>
		<a class="carousel-control-prev" href="#carouselIndicator" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#carouselIndicator" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	</div>
</div>

