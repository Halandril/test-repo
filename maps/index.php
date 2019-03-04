<?php
	require_once('config.php');
	require_once(INC_DIR.'csAppMapAdmin.php');
	$csapp = new csAppMapAdmin();
?><!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>mapCreator</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/map.css" />

		<style type="text/css">
			<?php
				if ($iw = @$csapp -> mapsettings['menu_icon_maxwidth']) {
					echo '.menuel-icon-div {width: '.($iw+2).'px;}'."\n";
					echo 'img.menuel-icon {max-width: '.$iw.'px}'."\n";		
				}
				if ($ih = @$csapp -> mapsettings['menu_icon_maxheight']) {
					echo 'img.menuel-icon {max-height: '.(int)$ih.'px}'."\n";
				}
			?>
		</style>
	</head>
	<body>

		<div id="map-page-container">
			<div id="map-box"></div>
		</div>
		
		<?php
			$gmurl = '';
			if ($csapp -> apikey) {
				$gmurl .= '&key='.$csapp -> apikey;
			}
		?>

		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false<?php echo $gmurl; ?>"></script>
	 
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="vendor/jquery-1.11.0.min.js"><\/script>')</script>

		<script src="vendor/underscore-min.js"></script>
		<script src="vendor/can.custom.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script> <!-- CZY TAK -->
		<script src="vendor/ekko-lightbox.min.js"></script>

		<?php
			echo '<script type="text/javascript">'."\n";
			$csapp -> bootstrap_data();
			echo "\n";
			echo 'window.maptranslations = {};'."\n";
			echo 'window.maplanguage = "'.$csapp -> lang.'"'."\n";
			echo '</script>'."\n";
		?>

		<script data-main="js/main" src="vendor/require.js"></script>
	</body>
</html>
