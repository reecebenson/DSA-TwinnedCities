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
		die('POI data invalid, maybe it doesn\'t exist? [' . $_REQUEST["name"] . ']');
?>
<div class="container">
	<div class="row">
		<div class="col" style="background-color: rgb(125, 125, 125); padding: 10px; text-align: left; border-bottom: 1px solid rgb(115, 115, 115);">
			<a href="#" onclick="goBack();" style="color: white;"><i class="fa fa-arrow-left"></i> Go Back to <strong>Points of Interest</strong></a>
		</div>
	</div>
    <div id="content" style="margin-top: 25px;">
		<div class="row">
			<div class="col-3"><img src="<?=$poi['image'];?>" style="float: left; width: 100%; margin-right: 25px;"></div>
			<div class="col">
				<h3><?=$poi['name'];?></h3>
				<p><?=$poi['desc'];?></p>
			</div>
		</div>
		<br/><br/>
		<div class="row">
			<div class="col">
				<h4>Information</h4>
				<p>
					<div style="float: left; width: 25px;"><i class="fa fa-male"></i></div> Capacity: <?=(is_null($poi['capacity']) ? "<em>unknown</em>" : $poi['capacity']);?><br/>
					<div style="float: left; width: 25px;"><i class="fa fa-map-marker"></i></div> Location: <?=$poi['lat'];?>,<?=$poi['long'];?><br/>
					<div style="float: left; width: 25px;"><i class="fa fa-phone"></i></div> Telephone: <?=$poi['phone'];?><br/>
					<div style="float: left; width: 25px;"><i class="fa fa-globe"></i></div> Website: <a href='<?=$poi['www'];?>' target='_blank'>click here for website</a><br/>
					<div style="float: left; width: 25px;"><i class="fa fa-address-card"></i></div> Address: <?=str_replace(",", ", ", $poi['address']);?>
				</p>
			</div>
			<div class="col">
				<h4>Other Similar Places</h4>
				<ol>
					<?php
						$count = 0;
						foreach($pois as $key => $place) {
							if($place['place_type'] == $poi['place_type'] && $key != $poi['name']) {
								echo "<li><a href='#' onclick='goToSpecificPOI(\"" . $key . "\");'>" . $key . "</a></li>";
								$count++;
							}
						}

						if($count == 0) {
							echo "<li>Unable to retrieve any similar places to this one.</li>";
						}
					?>
				</ol>
			</div>
		</div>
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

	function goToSpecificPOI(name) {
		let contentHolder = $("#ajaxContent"); // ref to parent page (index)
		$.get("./pages/singlepoi.php?woeid=<?=$_REQUEST['woeid'];?>&name=" + name, function(data) {
			// Replace HTML with the data inside of content
			contentHolder.html(data);
		});
	}
</script>