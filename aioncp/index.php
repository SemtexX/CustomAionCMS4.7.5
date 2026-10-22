<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

# Define Access
define('access', 'index');

# Load WebEngine
if(!@include_once('includes/system.php')) die('[ERROR] Could not load system.');

$pageTitleHTML = (check($config['modules'][$_GET['page']][$_GET['subpage']]['_title']) ? $config['modules'][$_GET['page']][$_GET['subpage']]['_title'] . ' | ' : '');
?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-focus"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">

        <title><?php echo $pageTitleHTML; ?>GlobalXcentra Admin Panel</title>

        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

        <link rel="shortcut icon" href="<?php echo __BASE_URL__; ?>img/favicons/favicon.png">
        <link rel="icon" type="image/png" href="<?php echo __BASE_URL__; ?>img/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="icon" type="image/png" href="<?php echo __BASE_URL__; ?>img/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="<?php echo __BASE_URL__; ?>img/favicons/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="<?php echo __BASE_URL__; ?>img/favicons/favicon-160x160.png" sizes="160x160">
        <link rel="icon" type="image/png" href="<?php echo __BASE_URL__; ?>img/favicons/favicon-192x192.png" sizes="192x192">
        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo __BASE_URL__; ?>img/favicons/apple-touch-icon-180x180.png">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
        <link rel="stylesheet" href="<?php echo __BASE_URL__; ?>css/bootstrap.min.css">
        <link rel="stylesheet" id="css-main" href="<?php echo __BASE_URL__; ?>css/oneui.min.css">
		<link rel="stylesheet" href="<?php echo __BASE_URL__; ?>css/main.css">
		<link rel="stylesheet" href="<?php echo __BASE_URL__; ?>css/extra.css">
		<link rel="stylesheet" href="<?php echo __BASE_URL__; ?>js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css">
		<link rel="stylesheet" href="<?php echo __BASE_URL__; ?>js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
    </head>
    <body>
        <div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed">
            <nav id="sidebar">
                <div id="sidebar-scroll">
                    <div class="sidebar-content">
                        <div class="side-header side-content bg-white-op">
							<button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close">
                                <i class="fa fa-times"></i>
                            </button>
                            <a class="h5 text-white" href="<?php echo __BASE_URL__; ?>">
                                <span class="h4 font-w600 sidebar-mini-hide">XcentraCMS | AION CP</span>
                            </a>
                        </div>
                        <div class="side-content">
							<?php
								Handler::buildNavbar_new();
							?>
                        </div>
                    </div>
                </div>
            </nav>
			
            <header id="header-navbar" class="content-mini content-mini-full">
                <ul class="nav-header pull-right">
                    <li><a style="color:#cccccc;font-weight:bold;font-size: 20px;" href="#"><?php echo date("d M Y H:i"); ?></a></li>
                </ul>
				<ul class="nav-header pull-left">
                    <li class="hidden-md hidden-lg">
                        <button class="btn btn-default" data-toggle="layout" data-action="sidebar_toggle" type="button">
                            <i class="fa fa-navicon"></i>
                        </button>
                    </li>
                    <li class="hidden-xs hidden-sm">
                        <button class="btn btn-default" data-toggle="layout" data-action="sidebar_mini_toggle" type="button">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                    </li>
                </ul>
            </header>
			
            <main id="main-container">
					<?php
					try {
						Handler::loadModule($db, $siel, $lumiel, $gabs, $aioncp);
					} catch(Exception $ex) {
						message($ex->getMessage(), 'error');
					}
					?>
            </main>
			
            <footer id="page-footer" class="content-mini content-mini-full font-s12 bg-gray-lighter clearfix">
                <div class="pull-right">
                    <a href="https://www.globalxcentra.net" target="_blank">Developed by GlobalXcentra</a>
                </div>
                <div class="pull-left">
                    <a href="https://www.globalxcentra.net" target="_blank">GlobalXcentra &copy;</a>
                </div>
            </footer>
        </div>
		
        <script src="<?php echo __BASE_URL__; ?>js/oneui.min.js"></script>
        <script src="<?php echo __BASE_URL__; ?>js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo __BASE_URL__; ?>js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
		
		<script>
            $(function () {
                // Init page helpers (BS Datepicker + BS Datetimepicker + BS Colorpicker + BS Maxlength + Select2 + Masked Input + Range Sliders + Tags Inputs plugins)
                App.initHelpers(['datepicker', 'datetimepicker', 'colorpicker', 'maxlength', 'select2', 'masked-inputs', 'rangeslider', 'tags-inputs']);
            });
        </script>
    </body>
</html>