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
     * Variables
     */
    $response = array('status' => 200, 'error' => null);
    $woeid = $_REQUEST['woeid'];

    /**
     * Check for errors
     */
    if(!isset($_REQUEST) || !isset($_REQUEST['woeid']) || !isset($_REQUEST['type'])) {
        $response['status'] = 500;
        $response['error'] = "No data was sent in the request parameters.";
    }

    /**
     * Get our city data from WOE ID
     */
    $city = $site->getCityData($_REQUEST['woeid']);

    /**
     * Check we have a valid city
     */
    if($city == null) {
        $response['status'] = 500;
        $respnose['error'] = "The inputted Woe ID was invalid.";
    } else {
        /**
         * Setup response specific to type request
         */
        switch($_REQUEST['type']) {
            default:
            case "list": {
                $builtList = "";
                $id = 0;
                foreach($city['poi'] as $name => $data) {
                    $builtList .= "<div class='row' style='padding-top: 2px;'>";
                    $builtList .= "<div class='col'><img src='" . $data['image'] . "' style='border-radius: 8px; width: 16px; height: 16px;'><a href='#map' onclick='selectPoint(" . $id . ");'><span style='padding-left: 8px; vertical-align: middle;'>" . $data['name'] . "</span></a></div>";
                    $builtList .= "</div>";
                    $id++;
                }
                $response['text'] = $builtList;
            }
            break;

            case "map": {
                    /**
                     * We only want the Point of Interest information sent back
                     */
                    die(json_encode($city['poi'], JSON_PRETTY_PRINT));
                }
                break;
        }
    }
        
    
    /**
     * Output
     */
    die(json_encode($response, JSON_PRETTY_PRINT));
?>