<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>mapCreator admin login</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/map.css" />
	</head>
	<body>

	<div class="page-container">
  
		<!-- top navbar -->
		<div class="navbar navbar-inverse" role="navigation" id="admin_menu">
			
			<div class="navbar-header">
				<a class="navbar-brand" target="_blank" href="http://www.mapcreator.pl">
					<span class="brand-map">map</span><span class="brand-artisan-reverse">Creator</span>
				</a>
			</div>

				<ul class="nav navbar-nav pull-right">
				
				<li>
					<a href="index.php"><?php echo $this -> translate('Go to main map'); ?> <i class="glyphicon glyphicon-share-alt"></i></a>
				</li>
			</ul>
		 </div>

		 <!-- title and messages bar -->
		 <div class="container">
		 	<div class="row">
				<div class="col-md-offset-3 col-md-6 text-center">
					<?php
						if ($this -> warning) {
							echo '<div class="alert alert-danger">'.$this -> warning.'</div>';
						} else if ($this -> success) {
							echo '<div class="alert alert-success">'.$this -> success.'</div>';
						}
					?>
				</div>
			</div>
		 </div>
			
		<!-- main content -->	
		<div class="container">

			<form class="form-horizontal" id="login_form" action="admin.php" role="form" method="post">
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-6">
						<h1>Panel administracyjny mapy</h1>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail3" class="col-sm-offset-3 col-sm-2 control-label"><?php echo $this -> translate('Login'); ?></label>
					<div class="col-sm-3">
						<input type="text" name="new_cs_username" class="form-control" id="inputEmail3" value="">
					</div>
				</div>
				
				<div class="form-group">
					<label for="inputPassword3" class="col-sm-offset-3 col-sm-2 control-label"><?php echo $this -> translate('Password'); ?></label>
					<div class="col-sm-3">
						<input type="password" name="new_cs_password" class="form-control" id="inputPassword3" value="">
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-sm-offset-5 col-sm-3">
						<input type="submit" id="login_submit" class="btn btn-primary btn-lg" value="<?php echo $this -> translate('Sign in'); ?>">
					</div>
				</div>
			</form>

		</div><!--/.container-->

 
	</div><!--/.page-container-->

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="vendor/jquery-1.11.0.min.js"><\/script>')</script>

		<script src="bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
