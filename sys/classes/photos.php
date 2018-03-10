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
	require_once(__DIR__.'/../../sys/core.php');


	$flickr = new phpFlickr($fl_details['key']);
	$tags  = "colorful,city,buildings,night,day,beach,lights,photography";
	$radius = "20";
	$perpage = "400";
	$sort = "interestingness-desc";
	 
 	clearPhotoCache();
 
	// -> Photos for city one
	$results = getPhotoResults($cities['city_one'],$flickr,$tags,$radius,$perpage,$sort);

	$city_photos_one = constructPhotos($results, $flickr, $cities['city_one']['woeid']);
	cachePhotos($city_photos_one);

	// -> photos for city two
	$results = getPhotoResults($cities['city_two'],$flickr,$tags,$radius,$perpage,$sort);

	$city_photos_two = constructPhotos($results, $flickr, $cities['city_two']['woeid']);
	cachePhotos($city_photos_two);

	echo("Photos all good");

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


	function getPhotoResults($city,$flickr,$tags,$radius,$perpage,$sort){

		return $flickr->photos_search(array("tags" => $tags, "tag_mode" => "any", "lat" => $city['lat'], "lon" => $city['long'], "radius" => $radius, "per_page" => $perpage, "sort" => $sort));
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

			// Constructing the url for image display.
			$photo_url = "https://farm".$farm.".staticflickr.com/".$server."/".$id."_".$secret."_c.jpg";
			
			$info = $flickr->photos_getInfo($id);
			$photo_results[$i]['id'] = $id;
			$photo_results[$i]['source'] = $photo_url;
			$photo_results[$i]['title'] = $title;	
			$photo_results[$i]['user_url'] = "https://www.flickr.com/photos/".$owner."/".$id."";
			$photo_results[$i]['desc'] = $info['photo']['description']['_content'];
			$photo_results[$i]['date_taken'] = $info['photo']['dates']['taken'];
			$photo_results[$i]['lat'] = $info['photo']['location']['latitude'];
			$photo_results[$i]['lon'] = $info['photo']['location']['longitude'];
			
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