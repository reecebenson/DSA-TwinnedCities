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
?>
<div class="container">
    <div id="content">
        <div class="row" id="placeInfo">
            <div class="col-sm">
                <div class="title">Weather</div>
                <div class="content" id="ajaxWeather">
                    <div id="icon"><img src="<?=$www;?>/gallery/img/load.gif" id="ajaxWeatherIcon" /></div>
                    <div id="name">Loading...</div>
                    <div id="data"></div>
                </div>
                <div class="last-pull">0 minutes ago</div>
            </div>
            <div class="col-sm">
                <div class="title">Information</div>
                <div class="content" id="ajaxInformation">
                    Loading...
                </div>
            </div>
            <div class="col-sm">
                <div class="title">Points of Interest</div>
                <div class="content" id="ajaxPointsOfInterest">
                    Loading...
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin: 0 auto;">
    <div class="col" style="padding: 0;">
        <div id="map" style="height: 400px; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;"></div>
    </div>
</div>