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
	if(!isset($_REQUEST['woeid']))
		die('Unable to load data. WOE ID was not received.');

	/**
	 * Get our city data from WOE ID
	 */
	$city = $site->getCityData($_REQUEST['woeid']);

	/**
	 * Check we have a valid city
	 */
	if($city == null)
		die('Specified WOE ID (' . $_REQUEST['woeid'] . ') does not exist.');
?>
<div class="container">
    <div id="content">
		<?php $id = 0; foreach($city['poi'] as $name => $data) { ?>
        <div class="row" style="padding-bottom: 15px; border-bottom: 1px solid #CCC;">
			<div class="col-3" style="padding: 5px;">
				<img src="<?=$data['image'];?>" style="float: left; width: 100%;" />
			</div>
			<div class="col" style="padding: 5px;">
				<h4><?=$name;?></h4>
				<p><?=$data['desc'];?></p>
				<p><a href="#" onclick="goToSpecificPOI('<?=$name;?>');">See more...</a></p>
			</div>
		</div>
		<?php $id++; } ?>
    </div>
</div>
<script type="text/javascript">
	var POI = <?=json_encode($city['poi']);?>;

	function goToSpecificPOI(name) {
		let contentHolder = $("#ajaxContent"); // ref to parent page (index)
		$.get("./pages/singlepoi.php?woeid=<?=$_REQUEST['woeid'];?>&name=" + name, function(data) {
			// Replace HTML with the data inside of content
			contentHolder.html(data);
		});
	}
</script>