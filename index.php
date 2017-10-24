<?php
	/* Reece Benson */
	/* BSc Comp Sci */
	require_once('sys/core.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Homepage | <?=$site->getSystemInfo("site_name_long");?></title>

		<?php require_once('pages/header.php'); ?>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-9">
					<h1>Reece Benson</h1>

					<div class="alert alert-warning first" id="warning" style="display: none;">
						<b><i class="fa fa-warning"></i> Warning:</b> <span id="warningMsg"><em>error occurred</em></span>
					</div>

					<div class="box first">
						<div class="title">
							<h3><?=$site->getSystemInfo("site_name_long");?></h3>
						</div>
						<div class="content last">
							<form name="fForm" id="fForm">
								<div class="input-group">
									<input type="text" minlength="1" id="fPlace" class="form-control" placeholder="Enter a town or city...">
									<span class="input-group-btn">
										<button class="btn btn-success" type="button" id="fSearch">Search <i class="fa fa-arrow-circle-right"></i></button>
									</span>
								</div>
							</form>
						</div>
					</div>

					<div class="box" style="display: none;" id="contentPlaces">
						<div class="title">
							<h3 id="contentPlacesTitle">Places</h3>
						</div>
						<table id="contentPlacesData" style="width: 100%;">

						</table>
					</div>

					<?php require_once('pages/footer.php'); ?>
				</div>
				<div class="col-lg-3">
					<h1>Place Information</h1>

					<div class="box first">
						<div class="title">
							<h3>Place Information</h3>
						</div>
						<div class="content last" id="placeData">
							Click on a location to load some data here!
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php require_once('pages/scripts.php'); ?>
	</body>
</html>