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

// Server Time
// http://php.net/manual/en/timezones.php
date_default_timezone_set('UTC');

# AIONCP Version
define('__AIONCP_VERSION__', '1.0.0');
define('__AIONCMS_VERSION__', '3.1.0');

# Load Configurations
if(!@include_once(dirname(__FILE__).'/config.php')) die("[ERROR] Could not load configurations.");

# System Status
if(!$config['active']) die('Offline.');

# Debugging Mode
if($config['debug']==false) {
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
define('__GA_ROOT_DIR__', str_replace('\\','/',dirname(dirname(dirname(__FILE__)))).'/'); // /home/user/public_html/
define('__ROOT_DIR__', str_replace('\\','/',dirname(dirname(__FILE__))).'/'); // /home/user/public_html/
define('__RELATIVE_ROOT__', str_ireplace(rtrim(str_replace('\\','/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__));// /
define('__BASE_URL__', SERVER_PROTOCOL.HTTP_HOST.__RELATIVE_ROOT__); // http(s)://www.mysite.com/
define('__GA_BASE_URL__', dirname(__BASE_URL__) . '/'); // http(s)://www.mysite.com/

# GA PATHS
define('__PATH_GA_LIBRARIES__', __GA_ROOT_DIR__.'libraries/');
define('__PATH_GAW_LIBRARIES__', __GA_ROOT_DIR__.'includes/libraries/');

# WebEngine PATHS
define('__PATH_INCLUDES__', __ROOT_DIR__.'includes/');
define('__PATH_LIBRARIES__', __PATH_INCLUDES__.'libs/');
define('__PATH_MODULES__', __ROOT_DIR__.'modules/');

# Load Functions
if(!@include_once(dirname(__FILE__).'/functions.php')) die("[ERROR] Could not load WebEngine functions.");

# Load Libraries
if(!@include_once(__PATH_LIBRARIES__.'database.php')) die("[ERROR] Could not load library [database].");
if(!@include_once(__PATH_LIBRARIES__.'validation.php')) die("[ERROR] Could not load library [validation].");
if(!@include_once(__PATH_LIBRARIES__.'handler.php')) die("[ERROR] Could not load library [handler].");
if(!@include_once(__PATH_GAW_LIBRARIES__.'lib.lottery.php')) die("[ERROR] Could not load library [lottery].");
if(!@include_once(__PATH_GAW_LIBRARIES__.'lib.tickets.php')) die("[ERROR] Could not load library [tickets].");
if(!@include_once(__PATH_GAW_LIBRARIES__.'lib.redeemcode.php')) die("[ERROR] Could not load library [redeemcode].");
if(!@include_once(__PATH_GAW_LIBRARIES__.'lib.account.php')) die("[ERROR] Could not load library [account].");
if(!@include_once(__PATH_GAW_LIBRARIES__.'lib.referrals.php')) die("[ERROR] Could not load library [referrals].");
if(!@include_once(__PATH_GAW_LIBRARIES__.'lib.profiles.legion.php')) die("[ERROR] Could not load library [profiles.legion].");

# Database
$db = new database($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME'], $config['DB_PORT']);
if($db->offline) die("[ERROR] Could not connect to the system database.");

$siel = new database($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME_2'], $config['DB_PORT']);
if($siel->offline) die("[ERROR] Could not connect to SIEL database.");

$lumiel = $siel;

$aioncp = new database($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], 'aioncms', $config['DB_PORT']);
if($gabs->offline) die("[ERROR] Could not connect to AIONCP database.");

$gabs = $aioncp;

# Check User Access
if(!check($_SESSION['username'])) redirect(__GA_BASE_URL__);

$userData_AIONCP = $aioncp->queryFetchSingle("SELECT * FROM `account_access` WHERE `username` = ?", array($_SESSION['username']));
if(!is_array($userData_AIONCP)) redirect(__GA_BASE_URL__);

if($userData_AIONCP['status'] != 1) redirect(__GA_BASE_URL__);

if(!array_key_exists('aioncp', $_SESSION)
	|| !check($_SESSION['aioncp']['name'], $_SESSION['aioncp']['email'], $_SESSION['aioncp']['access_level'])
	|| $_SESSION['aioncp']['access_level'] != $userData_AIONCP['access_level']) {
	
	$_SESSION['aioncp']['name'] = $userData_AIONCP['name'];
	$_SESSION['aioncp']['email'] = $userData_AIONCP['email'];
	$_SESSION['aioncp']['access_level'] = $userData_AIONCP['access_level'];
}

if(!check($_SESSION['aioncp']['server'])) {
	# set server
	$_SESSION['aioncp']['server'] = 'siel';
}
