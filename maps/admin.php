<?php
	require_once('config.php');
	require_once(INC_DIR.'csAppMapAdmin.php');
	$csapp = new csAppMapAdmin();

	$csapp -> make_login();
?><!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>mapCreator - panel administracyjny</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/map.css" />
		<link rel="stylesheet" href="css/admin.css" />
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

	<div class="page-container">
  
	<!-- top navbar -->
	<div class="navbar navbar-inverse" role="navigation" id="admin_menu">
		
	 </div>

	 <!-- title and messages bar -->
	 <div class="container-fluid" id="admin_message">
	 	
	 </div>
		
	<!-- mapeditor content -->	
	 <div class="container-fluid adminpage_switch" id="mapeditor">
		
		<div class="row">
	
		  <!-- main area -->
		  <div class="col-xs-12 col-sm-8" id="map_container">
			  <div id="map_canvas"></div>
		  </div><!-- /.col-xs-12 main -->

		  <!-- sidebar -->
		  <div class="col-xs-6 col-sm-4" id="sidebar" role="navigation">
				
				<div class="sidebar_panel" id="sidebar_panel_main">
					<div id="map_oper"></div>
					<div id="menu-tree"></div>
				</div>

				<div class="sidebar_panel" id="sidebar_panel_custom">
					<div id="map_ctrl"></div>
				</div>
		  </div>


	 </div><!--/.row-->
  </div><!--/.container-->

  <div class="container adminpage_switch" id="iconsets">
  		<div id="iconsets_ctrl"></div>
  </div>

  <div class="container adminpage_switch" id="othersettings">
  		<div id="othersettings_ctrl"></div>
  </div>

  <div class="container adminpage_switch" id="publication">
  		<div id="publication_ctrl"></div>
  </div>

  <div class="container adminpage_switch" id="passchange">
  		<div id="passchange_ctrl"></div>
  </div>

	<div class="container adminpage_switch" id="clusterer">
		<div id="clusterer_ctrl"></div>
	</div>

	<div class="container adminpage_switch" id="searchconfig">
		<div id="searchconfig_ctrl"></div>
	</div>

</div><!--/.page-container-->

		<?php
			$gmurl = '';
			if ($csapp -> apikey) {
				$gmurl .= '&key='.$csapp -> apikey;
			}
		?>
		
		<script src="ckeditor/ckeditor.js"></script>

		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry<?php echo $gmurl; ?>"></script>
	 
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="vendor/jquery-1.11.0.min.js"><\/script>')</script>

		<script src="vendor/underscore-min.js"></script>
		<script src="vendor/can.custom.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script> <!-- CZY TAK -->
		<script src="vendor/ekko-lightbox.min.js"></script>

		<script src="vendor/jquery.ui.widget.js"></script>
		<script src="vendor/jquery.iframe-transport.js"></script>
		<script src="vendor/jquery.fileupload.js"></script>

		<script src="vendor/bootstrap-colorpalette.js"></script>
		<script src="vendor/bootstrap-slider.js"></script>

		
		<?php
			echo '<script type="text/javascript">'."\n";
			$csapp -> bootstrap_data();
			echo "\n";
			echo 'window.maptranslations = {};'."\n";
			echo 'window.maplanguage = "'.$csapp -> lang.'"'."\n";
			echo '</script>'."\n";
		
			if ($csapp -> lang != 'en_US') {
				//echo '<script src="locale/map-'.$csapp -> lang.'.js"></script>'."\n";
				echo '<script src="locale/admin-'.$csapp -> lang.'.js"></script>'."\n";
			}
		?>

		<script data-main="js/admin" src="vendor/require.js"></script>
	</body>
</html>
