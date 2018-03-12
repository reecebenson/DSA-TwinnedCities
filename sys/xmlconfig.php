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
     * Set Headers
     */
    header("Content-Type:text/xml");

	/**
	 * Requirements
	 */
    require_once('./core.php');
    
    /**
     * Array to XML
     */
    function doesValueContainArrays($value) {
        foreach($value as $k => $v) {
            if(is_array($v))
                return true;
        }
    }

    function doesValueContainArraysWithIndexes($value) {
        foreach($value as $k => $v) {
            if(is_array($v) && is_numeric($k))
                return true;
        }
    }

    /**
     * Only way I could figure out how to do array stuff
     */
    function convertArrayToXML($arr, $prevKey = "", $ignore = false) {
        foreach($arr as $key => $value) {
            if(is_array($value) && !is_numeric($key)) {
                if(!$ignore && doesValueContainArrays($value) && doesValueContainArraysWithIndexes($value)) { 
                    echo "<" . $key . ">";
                    convertArrayToXML($value, $key);
                    echo "</" . $key . ">";
                } else if(doesValueContainArraysWithIndexes($value)) {
                    convertArrayToXML($value, $key);
                } else {
                    echo "<" . $key . ">";
                    convertArrayToXML($value, $key, true);
                    echo "</" . $key . ">";
                }
            } else if(is_array($value) && is_numeric($key)) {
                echo "<" . $prevKey . ">";
                convertArrayToXML($value);
                echo "</" . $prevKey . ">";
            } else {
                echo "<" . $key . ">" . $value . "</" . $key . ">";
            }
        }
    }

    $cityConcatenate = array(
        "city" => array($cities['city_one'], $cities['city_two'])
    );
    $cityDetails = $cityConcatenate;
    
    // Remove POI
    unset($cityDetails['city'][0]['poi']);
    unset($cityDetails['city'][1]['poi']);

    // Delete old elements
    unset($cityDetails['city']['city_one']);
    unset($cityDetails['city']['city_two']);

    /**
     * Merge our arrays
     */
    $config = array(
        "cities" => $cityDetails,
        "twitter" => $tw_details,
        "flickr" => $fl_details
    );

    /**
     * Print out
     */
    print("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
    print("<config>");
    convertArrayToXML($config);
    print("</config>");
?>