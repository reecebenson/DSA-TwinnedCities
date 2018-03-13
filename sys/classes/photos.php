 <?php 
	/**
	 * Photos.php
	 *
	 * PHP version 5.6.30
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
	 *
	 * Grabbing and caching photos from flickr base on lat and long of chosen cities.
	 *
	 */

	require_once(__DIR__.'/phpFlickr/phpFlickr.php');
	require_once(__DIR__.'/../../sys/configuration.php');

	/**
	 * Generates random tags for the flickr search from a small pool of tags
	 *  
	 * @return string of random tags
	 */
	function generateRandomTags(){
		$random_tags = array("night", "town", "city", "tree", "nature", "europe", "street", "leaves", "beautiful", "outdoor", "photo", "evening", "background", "autumn", "rain", "wooden", "weather", "forest", "lantern", "grass", "cityscape", "scene", "stone", "branch", "life", "alley", "yellow", "fall", "blue", "morning", "mystery", "path", "bench", "winter", "streetlamp", "autumnal", "seasons", "misty", "lights", "architecture", "sunset", "famous", "photography");

		$randTags = "";

		for ($i=0; $i < 5; $i++) { 
			$randTags = $randTags .$random_tags[rand(0,count($random_tags))] . ",";
		}
		 
		return $randTags;
		
	}

	
	// Check if we have recieved some tags
	if(!isset($_REQUEST['tags'])){
		 // random tags to search for photos with if no tags are produced
		$tags = generateRandomTags();
		
	} else{
		$tags = $_REQUEST['tags'];
	}
	
	// If the user hasn not submitted any tags, generate some for them.
	if($tags == ""){
		$tags = generateRandomTags();
	}
		
	$flickr = new phpFlickr($fl_details['key']);

	$radius = "20";
	$extras = "description,date_taken,geo";
	$perpage = "400";
	$sort = "interestingness-desc";
	 
 	clearPhotoCache();
 
	// -> Photos for city one
	$results = getPhotoResults($cities['city_one'],$flickr,$tags,$radius,$perpage,$sort,$extras);

	$city_photos_one = constructPhotos($results, $flickr, $cities['city_one']['woeid']);
	cachePhotos($city_photos_one);

	// -> photos for city two
	$results = getPhotoResults($cities['city_two'],$flickr,$tags,$radius,$perpage,$sort,$extras);

	$city_photos_two = constructPhotos($results, $flickr, $cities['city_two']['woeid']);
	cachePhotos($city_photos_two);

	/**
	 * Searches for photos based on city and defined parameters
	 *
	 * @param array Array of city informationcity array of city information
	 * @param object phpFlickr object
	 * @param string Tags to filter photo search
	 * @param int Radius (in KM) from lat/long
	 * @param int Amount of results returned per page
	 * @param string Arrange photo results 
	 *
	 * @return array Returns array of photo information
	 */   


	function getPhotoResults($city,$flickr,$tags,$radius,$perpage,$sort,$extras){

		return $flickr->photos_search(array("tags" => $tags, "tag_mode" => "any", "lat" => $city['lat'], "lon" => $city['long'], "radius" => $radius, "per_page" => $perpage, "sort" => $sort, "extras" => $extras));
	}

	/**
	 * Constructs url of photos to be displayed on webpage
	 * 
	 * @param array $results information about various photos that needs constructing
	 *
	 * @return array Returns array of info about flickr photos.
	 */   

	function constructPhotos($results, $flickr, $woeid){

		$photo_results = array();
		$photo_results['woeid'] = $woeid;

		$user_ids = array();

		for ($i=0; $i < 20; $i++) { 

			// -> retrieving info to construct url
			$id = $results['photo'][$i]['id'];
			$secret = $results['photo'][$i]['secret'];
			$title = $results['photo'][$i]['title'];
			$server = $results['photo'][$i]['server'];
			$farm = $results['photo'][$i]['farm'];
			$owner = $results['photo'][$i]['owner'];
			$lat = $results['photo'][$i]['latitude'];
			$lon = $results['photo'][$i]['longitude'];
			$desc = $results['photo'][$i]['description']['_content'];
			$date = $results['photo'][$i]['datetaken'];

			// Constructing the url for image display.
			$photo_url = "https://farm".$farm.".staticflickr.com/".$server."/".$id."_".$secret."_c.jpg";
			
			$photo_results[$i]['id'] = $id;
			$photo_results[$i]['source'] = $photo_url;
			$photo_results[$i]['title'] = $title;	
			$photo_results[$i]['user_url'] = "https://www.flickr.com/photos/".$owner."/".$id."";
			$photo_results[$i]['desc'] = $desc;
			$photo_results[$i]['date_taken'] = $date;
			$photo_results[$i]['lat'] = $lat;
			$photo_results[$i]['lon'] =  $lon;
			
		}
	
		return $photo_results;
	}

	/**
	 * Cleares current cache of images
	 * 
	 */
	function clearPhotoCache(){
		global $db;
		$statement = $db->prepare("TRUNCATE TABLE `images`");
		$statement->execute();
	}

	/**
	 * Caches all photos in the array passes through as a parameter, useful because pulling various
	 * information using the Flickr API is quite time intensive.
	 * 
	 * @param array $photosToCache array of information about the photos to be stored in the database.
	 */
	function cachePhotos($photosToCache){
		global $db;

		for($i=0; $i < 20; $i++) {

			try{

				$statement = $db->prepare("INSERT INTO `images` (`img_id`, `title`, `desc`, `source`, `user_url`, `date_taken`, `lat`, `long`, `city_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$statement->bindParam(1, $photosToCache[$i]['id']);
				$statement->bindParam(2, $photosToCache[$i]['title']);
				$statement->bindParam(3, $photosToCache[$i]['desc']);
				$statement->bindParam(4, $photosToCache[$i]['source']);
				$statement->bindParam(5, $photosToCache[$i]['user_url']);
				$statement->bindParam(6, $photosToCache[$i]['date_taken']);
				$statement->bindParam(7, $photosToCache[$i]['lat']);
				$statement->bindParam(8, $photosToCache[$i]['lon']);
				$statement->bindParam(9, $photosToCache['woeid']);

				$statement->execute();


			} catch(Exception $e){
				echo 'Error inserting images into database: ' + $e; 
			}

		}


	}


 ?>
