<?php
	/**
	 * Notes
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies, Daisy
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
     * 
     * Due: 2pm, 8th of March
     * 
     * To Do
     * -------------------------------------------
     * Website
     * done - Restructure the website to make it look less like a bodge-job
     * done - Make it a full screen site with a little bit of padding like the current one
     * done - Each Point of Interest should have a page showing more in-depth information
     * todo     • Link each PoI to another PoI in the surrounding city (link to images about it, show tweets of PoI if possible, etc.)
     * 
     * Weather
     * done - Cache the weather every 60 minutes, process of doing so:
     *          • Check against the table to look for a weather log
     *          • If the weather log is present, check the timestamp
     *          • If the timestamp is within 60 minutes, display data stored from previous weather log
     *          • If the timestamp is over 60 minutes, call the API for an update and store the updated data into the database
     * 
     * Twitter (Devon)
     * todo - Cache Tweets in both cities in intervals of 5 minutes (300 seconds), process of doing so:
     *          • Check against the table to look for a twitter log
     *          • If the twitter log is present, check the timestamp
     *              > Timestamp is above 5 minutes ago (300 seconds exceeded from last API pull)
     *                :: Update Tweets, store updated Twitter log into the database
     *              > Timestamp is below 5 minutes ago (300 seconds has not been exceeded since last API pull)
     *                :: Pull tweets from Database
     * 
     * RSS Feed
     * todo - Generate a RSS feed from the dataset in the local database relating to our cities (Manchester & Los Angeles)
     *          • The RSS feed should show the weather, tweets and flickr images
     * 
     * XML Configuration File
     * tofi - Grab the configuration data from the XML file in `xmlconfig.php` and parse it into `configuration.php` to match specification.
     *          • Check with Prakash if the configuration file should read from XML or PHP
     * 
     * Flickr
     * todo - Cache the Flickr photos every 60 minutes, process of doing so:
     *          • Check against the table to look for a flickr log
     *          • If the flickr log is present, check the timestamp
     *          • If the timestamp is within 60 minutes, display data stored from previous flickr log
     *              > The previous data should be in a table called `photos` which should has URLs to the photo, the photo source URL and the title
     *          • If the timestamp is over 60 minutes, call the API for an update and store the updated data into the database
     * 
     * Points of Interest
     * todo - Find 15 PoI's in both cities and populate the database with the information
     * 
     * Mapping
     * todo - Plot the PoI's on the map that are in the database
	 */
?>