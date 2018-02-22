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
 * Grabbing photos from flickr base on lat and long of chose cities.
 *
 */

require_once('phpFlickr/phpflickr.php');
require_once('sys/configuration.php');


$flickr = new phpFlickr($fl_details['key']);
$tags  = "colorful,city,buildings,night,day,beach,lights,photography";
$radius = "20";
$perpage = "400";
$sort = "interestingness-desc";

// -> Photos for city one
$results = getPhotoResults($cities['city_one'],$flickr,$tags,$radius,$perpage,$sort);

$city_photos_one = constructPhotos($results);

// -> photos for city two
$results = getPhotoResults($cities['city_two'],$flickr,$tags,$radius,$perpage,$sort);

$city_photos_two = constructPhotos($results);



/**
 * Searches for photos based on city and defined parameters
 *
 * @param city Array of city informationcity array of city information
 * @param flickr phpFlickr object
 * @param tags Tags to filter photo search
 * @param radius Radius (in KM) from lat/long
 * @param perpage Amount of results returned per page
 * @param sort Arrange photo results 
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
 * @return array Returns a list of url's to flickr photos.
 */   

function constructPhotos($results){

	$photo_results = array();
	$user_ids = array();
	$offset = rand(0,200);

	for ($i=0; $i < 50; $i++) { 

		// -> retrieving info to construct url
		$photo_num = $i + $offset;
		$id = $results['photo'][$photo_num]['id'];
		$secret = $results['photo'][$photo_num]['secret'];
		$title = $results['photo'][$photo_num]['title'];
		$server = $results['photo'][$photo_num]['server'];
		$farm = $results['photo'][$photo_num]['farm'];
		$owner = $results['photo'][$photo_num]['owner'];

		$photo_url = "https://farm".$farm.".staticflickr.com/".$server."/".$id."_".$secret."_z.jpg";

		$photo_results[$i]['url'] = $photo_url;
		$photo_results[$i]['title'] = $title;	
		$photo_results[$i]['user_url'] = "https://www.flickr.com/photos/".$owner."/".$id."";
		
	}

	return $photo_results;
}

 ?>