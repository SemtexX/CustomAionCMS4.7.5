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
<!-- Snow Effect [Delete Line Below to remove the Effect! -->
<!--<script async type="text/javascript" src="templates/default/js/snowstorm.js"></script>-->
<!-- Snow Effect [Delete Line Above to remove the Effect! -->

	<title>GlobalXcentra - Aion Private Server</title>
	<meta name="description" content="Top Aion Custom Server, increased rates, custom arenas, custom farmzones and mutch more"/>
	<meta name="keywords" content="AION, Aion 4.7.5, Aion, GlobalXcentra, GlobalXcentra Custom Server, Privat Server, AION Server, Aion Private Server, International Aion Private Server, Aion 4.7.5, Aion 4.7.5 Privat-Server (deutsch/englisch), Truly Free To Play, Events, abyss, best, top"/>
	<meta content="index,follow" name="robots">
	<meta property="og:site_name" content="GlobalXcentra - Best Aion Custom-Server"/>
	<meta property="og:description" content="Aion 4.7.5 Privat-Server (German/English) for the MMORPG &quot;Aion - Steel Cavalry&quot;. many custom things await you on our server"/>
	<meta property="og:title" content="GlobalXcentra 4.7.5 Privat-Server - GlobalXcentra - Best Aion Custom-Server"/>
	<meta property="og:url" content="https://www.globalxcentra.net"/>
	<meta name="language" content="English/German">
	<meta name="type" content="website">
	<meta name="copyright" content="Copyright GlobalXcentra">
	<meta name="resource-type" content="games - Aion 4.7.5">
	<meta name="Distribution" content="Global">
	<meta name="email" content="info@globalxcentra.net">
	<meta name="Charset" content="UTF-8">
	<meta name="Rating" content="General">
	<meta name="robots" content="INDEX,FOLLOW">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">
	<link href='https://fonts.googleapis.com/css?family=Marcellus' rel='stylesheet' type='text/css'>
	<link href="https://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet">
	<link rel="stylesheet" href="<?php template_css(); ?>main.css">
	<link rel="stylesheet" href="<?php template_css(); ?>webshop.css">
	<link rel="stylesheet" href="<?php template_css(); ?>shop.css">
	<link rel="stylesheet" href="<?php template_css(); ?>profile.css">
	<link rel="stylesheet" href="<?php template_css(); ?>lottery.css">
	<link rel="stylesheet" href="<?php template_css(); ?>rankings.css">
	<link rel="stylesheet" href="<?php template_css(); ?>rpc.css">
	<link rel="stylesheet" href="<?php template_css(); ?>override.css">
	
	<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/qtip2/2.2.0/basic/jquery.qtip.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php template_css(); ?>aiontooltip.css" />
	
	<link rel="Shortcut Icon" href="<?php template_img(); ?>favicon.ico">
	<script async type='text/javascript'
    src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'>
</script>

</head>
<style>
        /* Styling für das Video-Hintergrund */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .video-background video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        /* Styling für den Seiteninhalt z-index muss auf 9999 sein */
        .main-container {
            position: relative;
            z-index: 9999;
        }
</style>
<body>
    <div class="video-background">
        <video autoplay muted loop poster="<?php template_img(); ?>backround.jpg">
            <source src="<?php template_img(); ?>GlobalXcentra_AION_4.7_5.webm" type="video/webm">
            Your browser does not support this video format.
        </video>
    </div>
	<div class="main-container">
		<div class="main-header">
			<div class="main-logo">
				<img src="<?php template_img(); ?>logo.png" width="400px" height="auto"/>
			</div>
		</div>
		<div class="main-navbar">
			<div class="main-navbar-container">
			<ul class="left">
				<li><a href="<?php base_url(); ?>">Home</a></li>
				<li><a href="<?php module_url(); ?>connect/">Connect</a></li>
				<li><a href="<?php module_url(); ?>info/">Server Info</a></li>
				<li><a href="<?php module_url(); ?>support/">Support</a></li>
			</ul>
			<ul class="right">
				<li><a href="<?php module_url(); ?>timetable/">Timetable</a></li>
				<li><a href="<?php module_url(); ?>rankings/">Rankings</a></li>
				<li><a href="<?php module_url(); ?>usercp/vote/">Vote Reward</a></li>
				<li><a href="<?php module_url(); ?>unique/">Uniques</a></li>
				<!--<li><a href="https://discord.gg/4nwpdqJ4MM" target="_blank">Forum</a></li>-->
			</ul>
			</div>
		</div>
		<?php Handler::loadModule($_GET['request']); ?>
		<div class="main-footer">
			<div class="row">
				<div class="col-xs-4 text-right">
					<a href="https://esrb.org/" rel="noreferrer" target="_blank"><img src="<?php template_img(); ?>img_esrb.jpeg" height="40px" width="auto" title="ESRB Ratings" style="margin-right: 15px;"/></a>
					<a href="https://pegi.info/" rel="noreferrer" target="_blank"><img src="<?php template_img(); ?>img_pegi.png" height="40px" width="auto" title="Pan European Game Information"/></a>
				</div>
				<div class="col-xs-4 text-center">
					Copyright &copy; 2023 Aion, All Rights Reserved.<br />
					This site is in no way associated with or endorsed by &copy; NCSOFT Corp.<br />
					<a href="https://www.globalxcentra.net/" style="color:#ffe4a9;" target="_blank">Powered by GlobalXcentra</a><br /><br>
					<a href="https://www.elitepvpers.com/forum/aion-private-server/4347596-aionxcentra-v-4-7-5-a.html" target="_blank"><img src="<?php template_img(); ?>epvp.png" height="30px" width="auto" title="ElitePvP" style="margin-right: 15px;"/></a>
				</div>
				<div class="col-xs-4 text-left">
					<a href="https://www.globalxcentra.net/" target="_blank"><img src="<?php template_img(); ?>aioncms_footer_logo_xs.png" height="40px" width="auto" title="AionCMS" style="margin-right: 15px;"/></a>
					<img src="<?php template_img(); ?>aion_logo_footer.png" height="40px" width="auto" title="Aion Online"/>
				</div>
			</div>
			<!--<a href="//www.free-kassa.ru/"><img src="//www.free-kassa.ru/img/fk_btn/17.png" style="display:none;"></a>-->
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