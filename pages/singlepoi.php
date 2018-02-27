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
	if(!isset($_REQUEST['woeid']) || !isset($_REQUEST['name']))
		die('Unable to load data. WOE ID was not received.');

	/**
	 * Get our POIs Data
	 */
	$pois = $site->getPointsOfInterest($_REQUEST['woeid']);

	/**
	 * Check we have valid POIs
	 */
	if($pois == null)
		die('Specified WOE ID (' . $_REQUEST['woeid'] . ') does not exist.');

	/**
	 * Get our specific POI
	 */
	$poi = $pois[$_REQUEST['name']];

	/**
	 * Check we have valid POI
	 */
	if($poi == null)
		die('POI data invalid, maybe it doesn\'t exist?');
?>
<div class="container">
	<div class="row">
		<div class="col" style="background-color: rgb(125, 125, 125); padding: 10px; text-align: left; border-bottom: 1px solid rgb(115, 115, 115);">
			<a href="#" onclick="goBack();" style="color: white;"><i class="fa fa-arrow-left"></i> Go Back to <strong>Points of Interest</strong></a>
		</div>
	</div>
    <div id="content">
        <h3><?=$poi['name'];?></h3>
		<p><?=$poi['desc'];?></p>
    </div>
</div>
<script type="text/javascript">
	function goBack() {
		let contentHolder = $("#ajaxContent");
		$.get("./pages/poi.php?woeid=<?=$_REQUEST['woeid'];?>", function(data) {
			// Replace HTML with the data inside of content
			contentHolder.html(data);
		});
	}
</script>