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
		<div class="container">
			<div class="box-container">
				<h1>Reece Benson</h1>
				<div class="box first">
					<div class="title">
						<h3><?=$site->getSystemInfo("site_name_long");?></h3>
					</div>
					<div class="content last">
						Here is some content!
					</div>
				</div>
				<?php require_once('pages/footer.php'); ?>
			</div>
		</div>
		<?php require_once('pages/scripts.php'); ?>
	</body>
</html>