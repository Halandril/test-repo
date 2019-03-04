<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
require('server/UploadHandler.php');

$upload_handler = new UploadHandler(array(
	'upload_dir' => dirname(__FILE__).'/images/',
	'upload_url' => 'images/',
	'delete_type' => 'POST',
	'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
	'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
	'image_versions' => array(
		// The empty image version key defines options for the original image:
		'' => array(
			// Automatically rotate images based on EXIF meta data:
			'auto_orient' => false,
			'max_width' => 800,
	       'max_height' => 700
		),
		// Uncomment the following to create medium sized images:
		/*
		'medium' => array(
			'max_width' => 800,
			'max_height' => 600
		),
		*/
		'thumbnail' => array(
			// Uncomment the following to use a defined directory for the thumbnails
			// instead of a subdirectory based on the version identifier.
			// Make sure that this directory doesn't allow execution of files if you
			// don't pose any restrictions on the type of uploaded files, e.g. by
			// copying the .htaccess file from the files directory for Apache:
			//'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
			//'upload_url' => $this->get_full_url().'/thumb/',
			// Uncomment the following to force the max
			// dimensions and e.g. create square thumbnails:
			//'crop' => true,
			'max_width' => 120,
			'max_height' => 120
		)
)));
?>