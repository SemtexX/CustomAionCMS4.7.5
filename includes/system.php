<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

session_name('aionCMS');
session_start();
ob_start();

# AionCMS Version
define('__AIONCMS_VERSION__', '3.1.0');

// Server Time
// http://php.net/manual/en/timezones.php
date_default_timezone_set('Europe/Berlin');

# CloudFlare IP Workaround
if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

# Load Configurations
if(!@include_once(dirname(__FILE__) . '/config.php')) {
	throw new Exception('Could not load configurations.');
}

# custom template
if(access == "legionprofile") {
	$config['template'] = 'profiles';
}

# Load Definitions (extra configs)
if(!@include_once(dirname(__FILE__) . '/definitions.php')) {
	throw new Exception('Could not load definitions.');
}

# System Status
if(!$config['active']) {
	if($config['enable_maintenance_redirect']) {
		
		if(is_array($config['maintenance_ip_access'])) {
			if(!in_array($_SERVER['REMOTE_ADDR'], $config['maintenance_ip_access'])) {
				header('Location: ' . $config['maintenance_page']);
				die();
			}
		} else {
			header('Location: ' . $config['maintenance_page']);
			die();
		}
				
	} else {
		throw new Exception('');
	}
}

# Debugging Mode
if($config['debug'] == false) {
	ini_set('display_errors', false);
	error_reporting(0);
	$config['debug_lang'] = false;
} else {
	ini_set('display_errors', true);
	error_reporting(E_ALL & ~E_NOTICE);
	$config['debug_lang'] = true;
}

# Encoding
@ini_set('default_charset', 'utf-8');

# GLOBAL URL PATHS
define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://');
define('__ROOT_DIR__', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/'); // /home/user/public_html/
define('__RELATIVE_ROOT__', str_ireplace(rtrim(str_replace('\\', '/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__)); // /
define('__BASE_URL__', SERVER_PROTOCOL . HTTP_HOST . __RELATIVE_ROOT__); // http(s)://www.mysite.com/
define('__PAGE_URL__', SERVER_PROTOCOL . HTTP_HOST . __RELATIVE_ROOT__ . 'page/'); // http(s)://www.mysite.com/page/

# PATHS
define('__PATH_INCLUDES__', __ROOT_DIR__ . 'includes/');
define('__PATH_LIBRARIES__', __PATH_INCLUDES__ . 'libraries/');
define('__PATH_MODULES__', __ROOT_DIR__ . 'modules/');
define('__PATH_EMAILS__', __PATH_INCLUDES__ . 'email/');
define('__PATH_CACHE__', __PATH_INCLUDES__ . 'cache/');
define('__PATH_CRON__', __PATH_INCLUDES__ . 'cron/');
define('__PATH_TEMPLATES__', __ROOT_DIR__ . 'templates/');
define('__PATH_TEMPLATE__', __BASE_URL__ . 'templates/' . $config['template'] . '/');
define('__PATH_TEMPLATE_ROOT__', __ROOT_DIR__ . 'templates/' . $config['template'] . '/');
define('__PATH_TEMPLATE_IMG__', __PATH_TEMPLATE__ . 'img/');
define('__PATH_TEMPLATE_CSS__', __PATH_TEMPLATE__ . 'css/');
define('__PATH_TEMPLATE_JS__', __PATH_TEMPLATE__ . 'js/');
define('__PATH_TEMPLATE_FONTS__', __PATH_TEMPLATE__ . 'fonts/');
define('__PATH_PUBLIC_CACHE__', __ROOT_DIR__ . 'cache/');
define('__PATH_EMBLEMS_ROOT__', __PATH_PUBLIC_CACHE__ . 'emblem/');
define('__PATH_LEGION_EMBLEM__', __BASE_URL__ . 'cache/emblem/');
define('__PATH_LEGION_PROFILES_CACHE__', __PATH_CACHE__ . 'profiles/legion/');

# Load Functions
if(!@include_once(dirname(__FILE__) . '/functions.php')) throw new Exception('Could not load system functions.');

# Load Libraries
if(!@include_once(__PATH_LIBRARIES__ . 'lib.database.php')) throw new Exception('Could not load library [database].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.validator.php')) throw new Exception('Could not load library [validator].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.handler.php')) throw new Exception('Could not load library [handler].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.account.php')) throw new Exception('Could not load library [account].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.login.php')) throw new Exception('Could not load library [login].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.session.php')) throw new Exception('Could not load library [session].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.email.php')) throw new Exception('Could not load library [email].');
if(!@include_once(__PATH_LIBRARIES__ . 'class.phpmailer.php')) throw new Exception('Could not load library [phpmailer].');
if(!@include_once(__PATH_LIBRARIES__ . 'class.smtp.php')) throw new Exception('Could not load library [smtp].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.lottery.php')) throw new Exception('Could not load library [lottery].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.log.php')) throw new Exception('Could not load library [log].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.enchantment.php')) throw new Exception('Could not load library [enchant].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.register.php')) throw new Exception('Could not load library [register].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.shop.php')) throw new Exception('Could not load library [shop].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.battlepass.php')) throw new Exception('Could not load library [battlepass].');
if(!@include_once(__PATH_LIBRARIES__ . 'paypal/PaypalIPN.php')) throw new Exception('Could not load library [paypal].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.superrewards.php')) throw new Exception('Could not load library [superrewards].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.tickets.php')) throw new Exception('Could not load library [tickets].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.paymentwall.php')) throw new Exception('Could not load library [paymentwall].');
if(!@include_once(__PATH_LIBRARIES__ . 'paymentwall/autoload.php')) throw new Exception('Could not load library [paymentwall].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.redeemcode.php')) throw new Exception('Could not load library [redeemcode].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.referrals.php')) throw new Exception('Could not load library [referrals].');
if(!@include_once(__PATH_LIBRARIES__ . 'lib.profiles.legion.php')) throw new Exception('Could not load library [profiles.legion].');

# check http referer (VOTE SYSTEM)
if($_SESSION['check_referer'] == true) {
	try {
		if(!isLoggedIn()) throw new Exception('Not logged in.');
		voteCheckReferer();
	} catch(Exception $ex) {
		
	}
}

# Load Template
Handler::loadTemplate();