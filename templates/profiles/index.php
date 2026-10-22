<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>GlobalXcentra - Aion Private Server</title>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="<?php template_css(); ?>main.css">
		<link rel="stylesheet" href="<?php template_css(); ?>profile.css">
		<link rel="stylesheet" href="<?php template_css(); ?>override.css">
		<link rel="Shortcut Icon" href="<?php template_img(); ?>favicon.ico">
	</head>
	<body>
		<div class="main-container">
			<div class="main-header">
				
			</div>
			<div class="main-navbar">
				<div class="main-navbar-container">
					<a href="<?php base_url(); ?>">Home</a>
				</div>
			</div>
			<?php Handler::loadModule($_GET['request']); ?>
			<div class="main-footer">
				<br /><br />
			</div>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/qtip2/2.2.0/basic/jquery.qtip.min.js"></script>
		<script>
		$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		})
		</script>
	</body>
</html>