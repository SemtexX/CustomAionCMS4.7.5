<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

/**
 * 
 * For support join us on discord:
 * https://discord.gg/sGJjJHf
 * 
 */

$config['active'] = true;
$config['debug'] = true;

$config['DB_HOST'] = '127.0.0.1';
$config['DB_NAME'] = 'aion_ls';
$config['DB_NAME_2'] = 'aioncms';
$config['DB_USER'] = 'ChangeMe';
$config['DB_PASS'] = 'ChangeMe';
$config['DB_PORT'] = '3306';
$config['PDO_DSN'] = 'mysql';

// AIONCP ACCESS
// 100: admin
// 	90: co-admin
//  80: head gm
//  50: sam
//  30: am

// MODULES
$config['modules'] = array(
	'home' => array(
		'_title' => 'Dashboard',
		'_access' => 10,
		'_separator' => true,
	),
	
	'tickets' => array(
		'_groupname' => 'Ticket Support',
		'_access' => 30,
		'_icon' => 'si-support',
		
		'open' => array(
			'_title' => 'Open Tickets',
			'_access' => 30,
		),
		'closed' => array(
			'_title' => 'Closed Tickets',
			'_access' => 30,
		),
		'banned' => array(
			'_title' => 'Banned List',
			'_access' => 30,
		),
		'view' => array(
			'_title' => 'View Ticket',
			'_access' => 30,
			'_hide' => true,
		),
	),
	
	'bans' => array(
		'_groupname' => 'Ban System',
		'_access' => 30,
		'_icon' => 'si-shield',
		
		'list' => array(
			'_title' => 'My Bans',
			'_access' => 30,
		),
		'new' => array(
			'_title' => 'Ban Account',
			'_access' => 30,
		),
		'search' => array(
			'_title' => 'Search Bans',
			'_access' => 30,
		),
		'latest' => array(
			'_title' => 'Latest Bans',
			'_access' => 30,
		),
		'view' => array(
			'_title' => 'View Ban',
			'_access' => 30,
			'_hide' => true,
		),
	),
	
	'tools' => array(
		'_groupname' => 'AM Tools',
		'_access' => 30,
		'_icon' => 'si-wrench',
		
		'accountdata' => array(
			'_title' => 'Account Details',
			'_access' => 30,
		),
		'playerdetails' => array(
			'_title' => 'Player Details',
			'_access' => 30,
		),
		'legiondetails' => array(
			'_title' => 'Legion Details',
			'_access' => 30,
		),
	),
	
	'sam' => array(
		'_groupname' => 'SAM Tools',
		'_access' => 50,
		'_icon' => 'si-grid',
		
		'online' => array(
			'_title' => 'Online Players',
			'_access' => 50,
		),
		'mailitems' => array(
			'_title' => 'Mail Items',
			'_access' => 50,
		),
		'unstickplayer' => array(
			'_title' => 'Unstick (location reset)',
			'_access' => 50,
		),
		'unstickrequest' => array(
			'_title' => 'Review Unstick Requests',
			'_access' => 50,
		),
		'referrals' => array(
			'_title' => 'Referral System Logs',
			'_access' => 50,
		),
		'legionprofiles' => array(
			'_title' => 'Legion Profile Requests',
			'_access' => 50,
		),
	),
	
	'hgm' => array(
		'_groupname' => 'HGM Tools',
		'_access' => 80,
		'_icon' => 'si-layers',
		
		'staff' => array(
			'_title' => 'GM Accounts',
			'_access' => 80,
		),
		'emailsearch' => array(
			'_title' => 'Find Accounts by Email',
			'_access' => 80,
		),
		'ipsearch' => array(
			'_title' => 'Find Accounts by IP',
			'_access' => 80,
		),
		'macsearch' => array(
			'_title' => 'Find Accounts by MAC',
			'_access' => 80,
		),
		'tracker' => array(
			'_title' => 'Item Tracker',
			'_access' => 80,
		),
	),
	
	'admin' => array(
		'_groupname' => 'Admin Tools',
		'_access' => 90,
		'_icon' => 'si-settings',
		
		'ban' => array(
			'_title' => 'Ban',
			'_access' => 90,
		),
		'unban' => array(
			'_title' => 'Unban',
			'_access' => 90,
		),
		'editaccesslvl' => array(
			'_title' => 'Edit Access Level',
			'_access' => 90,
		),
		'customtitles' => array(
			'_title' => 'Title Editor',
			'_access' => 90,
		),
		'gameserverlogs' => array(
			'_title' => 'Gameserverlogs',
			'_access' => 90,
		),
		'serverstats' => array(
			'_title' => 'Serverstats',
			'_access' => 90,
		),
		'topcredits' => array(
			'_title' => 'View Top Credits',
			'_access' => 90,
		),
		'accounteditor' => array(
			'_title' => 'Account Editor',
			'_access' => 90,
		),
		'playereditor' => array(
			'_title' => 'Player Editor',
			'_access' => 90,
		),
		'abysseditor' => array(
			'_title' => 'Abyss Editor',
			'_access' => 90,
		),
		'access' => array(
			'_title' => 'AionCP Access',
			'_access' => 90,
		),
		'playerskills' => array(
			'_title' => 'Player Skills',
			'_access' => 90,
		),
		'tempaccess' => array(
			'_title' => 'Temporal Account Access',
			'_access' => 90,
		),
		'paypal' => array(
			'_title' => 'PayPal Logs',
			'_access' => 90,
		),
		'superrewards' => array(
			'_title' => 'SuperRewards Logs',
			'_access' => 90,
		),
		'paymentwall' => array(
			'_title' => 'PaymentWall Logs',
			'_access' => 90,
		),
		'addlogs' => array(
			'_title' => 'Add Command Logs',
			'_access' => 90,
		),
		'redeemcodes' => array(
			'_title' => 'Redeem Codes',
			'_access' => 90,
		),
		'redeemcodeslogs' => array(
			'_title' => 'Redeem Codes Logs',
			'_access' => 90,
		),
		'weblogs' => array(
			'_title' => 'Web Logs (live)',
			'_access' => 90,
		),
		'websessions' => array(
			'_title' => 'Web Sessions (live)',
			'_access' => 90,
		),
	),
	
	'battlepass' => array(
		'_groupname' => 'Battlepass',
		'_access' => 90,
		'_icon' => 'si-basket',
		
		'admin' => array(
			'_title' => 'Battlepass',
			'_access' => 90,
		),
		'admintiers' => array(
			'_title' => 'Tiers',
			'_access' => 90,
		),
		'adminlogs' => array(
			'_title' => 'Logs',
			'_access' => 90,
		),
	),
	
	'webshop' => array(
		'_groupname' => 'Web Shop',
		'_access' => 90,
		'_icon' => 'si-basket',
		
		'categories' => array(
			'_title' => 'Categories',
			'_access' => 90,
		),
		'items' => array(
			'_title' => 'Items',
			'_access' => 90,
			//'_hide' => true,
		),
		'specialshoplist' => array(
			'_title' => 'Weekly Special Items',
			'_access' => 90,
		),
		'add' => array(
			'_title' => 'Add New Item',
			'_access' => 90,
			'_hide' => true,
		),
		'edit' => array(
			'_title' => 'Edit Item',
			'_access' => 90,
			'_hide' => true,
		),
		'logs' => array(
			'_title' => 'Logs',
			'_access' => 90,
		),
	),
	
	'lottery' => array(
		'_groupname' => 'Lottery System',
		'_access' => 90,
		'_icon' => 'si-trophy',
		
		'status' => array(
			'_title' => 'Status',
			'_access' => 90,
		),
		'similar' => array(
			'_title' => 'Similar Accounts',
			'_access' => 90,
		),
		'stash' => array(
			'_title' => 'Stash List',
			'_access' => 90,
		),
		'list' => array(
			'_title' => 'Lotteries List',
			'_access' => 90,
		),
	),
);