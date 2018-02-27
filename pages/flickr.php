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
	require_once('../sys/classes/photos.php');
	
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
	$photos[$cities['city_one']['woeid']] = $city_photos_one;
	$photos[$cities['city_two']['woeid']] = $city_photos_two;
?>
<style type="text/css">
	.carousel-backdrop {
		background-color: rgba(0, 0, 0, 0.5);
		border-radius: 15px;
	}
</style>
<div class="container">
	<div id="carouselIndicator" class="carousel slide" data-ride="carousel">
		<ol class="carousel-indicators">
			<li data-target="#carouselIndicator" data-slide-to="0" class="active"></li>
			<?php for($i = 1; $i < 50; $i++) { ?>
			<li data-target="#carouselIndicator" data-slide-to="<?=$i;?>"></li>
			<?php } ?>
		</ol>
		<div class="carousel-inner">
			<?php
				/**
				 * Loop through available photos
				 */
				for($i = 0; $i < 50; $i++) {
					// Get our photo
					$photo = $photos[$woeid][$i];

					// Display
					echo '<div class="carousel-item ' . ($i == 0?"active":"") .'">';
					echo '<img class="d-block w-100" src="' . $photo["url"] . '" alt="' . $photo["title"] . '">';
					echo '<div class="carousel-caption d-none d-md-block carousel-backdrop">';
					echo '<h5>' . $photo["title"] . '</h5>';
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