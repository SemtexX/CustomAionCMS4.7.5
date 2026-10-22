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
$config['enable_maintenance_redirect'] = true;
$config['maintenance_page'] = 'https://discord.gg/ChangeMe';
$config['maintenance_ip_access'] = array(
	'127.0.0.1',
);

$config['template'] = 'default';
$config['language'] = 'en';
$config['debug'] = true;

$config['forum_url'] = 'https://discord.gg/ChangeMe';

$config['DB_HOST'] = '127.0.0.1';
$config['DB_NAME'] = 'aion_ls';
$config['DB_NAME_2'] = 'aioncms';
$config['DB_USER'] = 'ChangeMe';
$config['DB_PASS'] = 'ChangeMe';
$config['DB_PORT'] = '3306';
$config['PDO_DSN'] = 'mysql';

$config['email_active'] = true;
$config['email_send_name'] = 'Aion';
$config['email_send_from'] = 'ChangeMe';

$config['smtp_active'] = true;
$config['smtp_host'] = 'ssl://smtp.gmail.com';
$config['smtp_port'] = 465;
$config['smtp_user'] = 'ChangeMe@gmail.com';
$config['smtp_pass'] = 'ChangeMe';

$config['sr_active'] = false;
$config['sr_app_hash'] = '';
$config['sr_secret_key'] = '';

$config['pw_active'] = false;
$config['pw_project_key'] = '';
$config['pw_private_key'] = '';
$config['pw_widget'] = 'p1_1';
$config['pw_widget_width'] = 590;
$config['pw_widget_height'] = 500;

$config['fk_active'] = false;
$config['fk_merchant_id'] = 000000;
$config['fk_secret'] = '';
$config['fk_secret_2'] = '';
$config['fk_donation_options'] = array(
	5 => 500,
	10 => 1100,
	25 => 2815,
	50 => 5750,
	100 => 12000,
);

$config['referral_active'] = true;
$config['referral_required_onlinetime_hours'] = 3;
$config['referral_credits_reward'] = 300;
$config['referral_link_base'] = 'https://www.yoursite.net/page/register/friend/ref/';

$config['security_questions'] = array(
	'What was the name of your elementary school?',
	'What is the name of your first pet?',
	'What is your favourite colour?',
	'What is your favourite animal?',
	'What was your childhood nickname?',
	'What year did you buy your first computer?',
	'What is the name of your favourite movie?',
	'What is the name of your favourite teacher?',
	'In what city did you grow up?',
	'What is the name of your favourite YouTube channel?',
	'What is the name of your favourite TV show?',
	'In what city or town was your first job?'
);

$config['premium_cost'] = 1500;
$config['vip_cost'] = 1500;

$config['vote_sites'] = array(
	1 => array(
		10,
		'https://topg.org/aion-private-servers/server-505301',
		'topg.gif',
		'TopG',
		array(
			'topg.org',
			'www.topg.org'
		)
	),
	2 => array(
		10,
		'https://gtop100.com/topsites/Aion-Online/sitedetails/GlobalXcentra-Aion-Server-89722?vote=1',
		'gtop100.jpg',
		'Gtop100',
		array(
			'gtop100.com',
			'www.gtop100.com'
		)
	),
	//3 => array(
		//10,
		//'#link',
		//'topofg.gif',
		//'TopofGames',
		//array(
			//'topofgames.com',
			//'www.topofgames.com'
		//)
	//),
	//4 => array(
		//10,
		//'#link',
		//'gs200.gif',
		//'Arena-Top100',
		//array(
			//'arena-top100.com',
			//'www.arena-top100.com'
		//)
	//),
	//5 => array(
		//10,
		//'https://gtop100.com/topsites/Aion-Online/sitedetails/GlobalXcentra-Aion-Server-89722?vote=1',
		//'gtop100.jpg',
		//'Gtop100',
		//array(
			//'gtop100.com',
			//'www.gtop100.com'
		//)
	//)
);

$config['vote_promo'] = array(
	array('2025-12-01', '2026-01-01', 50),
);

$config['vote_enchant_token_chance'] = 1; // percent

$config['usercp_sidebar_menu'] = array(
	array(
		'My Account',
		'usercp/account/',
		'acc_ico.png',
	),
	array(
		'My Characters',
		'usercp/characters/',
		'char_ico.png',
	),
	array(
		'My Support Tickets',
		'tickets/list/',
		'tokens_ico.png',
	),
	array(
		'Webshop',
		'shop/',
		'items_ico.png',
	),
	array(
		'Weekly Special Shop',
		'shop/',
		'wsitems_ico.png',
	),
	array(
		'Get Premium | VIP Membership',
		'usercp/upgrade/',
		'premium_ico.png',
	),
	array(
		'Change Password',
		'usercp/password/',
		'changepass_ico.png',
	),
	array(
		'Ban System',
		'usercp/bansystem/',
		'bansystem_ico.png',
	),
	array(
		'Forum Events',
		'forumevents/',
		'forumevents_ico.png',
	),
	array(
		'Online Time Exchange',
		'usercp/timexchange/',
		'xfer_ot_icon.png',
	),
	array(
		'Lottery',
		'lottery/',
		'lottery_ico.png',
	),
	array(
		'Redeem Code',
		'usercp/redeem/',
		'redeem_ico.png',
	),
	array(
		'Referral System',
		'usercp/referrals/',
		'referrals_ico.png',
		true //no separator at end
	),
);

$config['enchant_price'] = array(
	0 => 40,//-
	1 => 50,// |
	2 => 60,// | 300 credits
	3 => 70,// |
	4 => 80,//-
	5 => 100,//-
	6 => 120,// |
	7 => 140,// | 700 credits
	8 => 160,// |
	9 => 180,//-
	10 => 180,//-
	11 => 190,// |
	12 => 200,// | 1000 credits
	13 => 210,// |
	14 => 220,//-
	15 => 260,//-
	16 => 280,// |
	17 => 300,// | 1500 credits
	18 => 320,// |
	19 => 340,//-
);

$config['enchant_skill_change_price'] = 500;

$config['enchantable_items'] = array(
	'110601593' => array(13138,13141,13147),
	'113601539' => array(13077,13074,13076),
	'111601556' => array(13054,13052,13049),
	'114601546' => array(13117,13127,13124),
	'112601538' => array(13087,13093,13107),
	'110101809' => array(13138,13141,13147),
	'113101639' => array(13077,13074,13076),
	'111101627' => array(13053,13052,13049),
	'114101673' => array(13117,13127,13124),
	'112101577' => array(13086,13090,13107),
	'110551126' => array(13138,13141,13147),
	'113501703' => array(13077,13074,13076),
	'111501685' => array(13053,13052,13049),
	'114501713' => array(13117,13127,13124),
	'112501624' => array(13086,13090,13107),
	'110301790' => array(13138,13141,13147),
	'113301759' => array(13077,13074,13076),
	'111301728' => array(13054,13052,13049),
	'114301796' => array(13117,13127,13124),
	'112301667' => array(13087,13093,13107),
	'110301791' => array(13138,13141,13147),
	'113301760' => array(13077,13074,13076),
	'111301729' => array(13053,13052,13049),
	'114301797' => array(13117,13127,13124),
	'112301668' => array(13086,13090,13107),
	'110601768' => array(13131,13138,13143),
	'110601769' => array(13138,13143,13131),
	'110551331' => array(13138,13143,13131),
	'110551332' => array(13138,13143,13131),
	'110551333' => array(13138,13143,13131),
	'110301980' => array(13138,13143,13131),
	'110301981' => array(13138,13143,13131),
	'110102004' => array(13138,13143,13131),
	'110102005' => array(13138,13143,13131),
	'111601732' => array(13054,13041,13045),
	'111601733' => array(13054,13041,13045),
	'111501891' => array(13053,13039,13045),
	'111501892' => array(13053,13039,13045),
	'111501893' => array(13053,13039,13045),
	'111301919' => array(13054,13041,13045),
	'111301920' => array(13054,13041,13045),
	'111101798' => array(13053,13039,13045),
	'111101799' => array(13053,13039,13045),
	'112601713' => array(13087,13085,13101),
	'112601714' => array(13087,13085,13101),
	'112501827' => array(13086,13083,13101),
	'112501828' => array(13086,13083,13101),
	'112501829' => array(13086,13083,13101),
	'112301856' => array(13087,13085,13101),
	'112301857' => array(13087,13085,13101),
	'112101743' => array(13086,13083,13101),
	'112101744' => array(13086,13083,13101),
	'113601715' => array(13077,13079,13081),
	'113601716' => array(13077,13079,13081),
	'113501910' => array(13077,13079,13081),
	'113501911' => array(13077,13079,13081),
	'113501912' => array(13077,13079,13081),
	'113301950' => array(13077,13079,13081),
	'113301951' => array(13077,13079,13081),
	'113101809' => array(13077,13081,13079),
	'113101810' => array(13077,13081,13079),
	'114601721' => array(13117,13113,13111),
	'114601722' => array(13117,13113,13111),
	'114501918' => array(13117,13113,13111),
	'114501919' => array(13117,13113,13111),
	'114501920' => array(13117,13113,13111),
	'114301987' => array(13117,13113,13111),
	'114301988' => array(13117,13113,13111),
	'114101843' => array(13117,13113,13111),
	'114101844' => array(13117,13113,13111),
	'110101573' => array(13138,13141,13147),
	'110301628' => array(13138,13141,13147),
	'110301470' => array(13138,13141,13147),
	'110501431' => array(13138,13141,13147),
	'110501478' => array(13138,13141,13147),
	'110601406' => array(13138,13141,13147),
	'110601459' => array(13138,13141,13147),
	'111101418' => array(13053,13052,13049),
	'111301569' => array(13053,13052,13049),
	'111501388' => array(13053,13052,13049),
	'111301410' => array(13049,13052,13054),
	'111501435' => array(13049,13052,13054),
	'111601368' => array(13049,13052,13054),
	'111601368' => array(13049,13052,13054),
	'111601421' => array(13049,13052,13054),
	'112101372' => array(13086,13090,13107),
	'112301508' => array(13086,13090,13107),
	'112501330' => array(13086,13090,13107),
	'112301354' => array(13087,13093,13107),
	'112501377' => array(13087,13093,13107),
	'112601350' => array(13087,13093,13107),
	'112601402' => array(13087,13093,13107),
	'113101436' => array(13077,13074,13076),
	'113301435' => array(13077,13074,13076),
	'113301594' => array(13077,13074,13076),
	'113501405' => array(13077,13074,13076),
	'113501451' => array(13077,13074,13076),
	'113601358' => array(13077,13074,13076),
	'113601404' => array(13077,13074,13076),
	'114101465' => array(13117,13127,13124),
	'114301471' => array(13117,13127,13124),
	'114301633' => array(13117,13127,13124),
	'114501413' => array(13117,13127,13124),
	'114501460' => array(13117,13127,13124),
	'114601356' => array(13117,13127,13124),
	'114601409' => array(13117,13127,13124),
	'100002014' => array(13021,13017,13005),
	'100002013' => array(13021,13017,13005),
	'100002068' => array(13021,13017,13005),
	'100101496' => array(13019,13017,13005),
	'100101546' => array(13019,13017,13005),
	'100101495' => array(13019,13017,13005),
	'100201677' => array(13021,13017,13005),
	'100201725' => array(13021,13017,13005),
	'100201676' => array(13021,13017,13005),
	'100501454' => array(13019,13017,13005),
	'100501453' => array(13019,13017,13005),
	'100501509' => array(13019,13017,13005),
	'100601572' => array(13019,13017,13005),
	'100601621' => array(13019,13017,13005),
	'100601571' => array(13019,13017,13005),
	'100901531' => array(13021,13017,13005),
	'100901582' => array(13021,13017,13005),
	'100901530' => array(13021,13017,13005),
	'101301415' => array(13021,13017,13005),
	'101301414' => array(13021,13017,13005),
	'101301463' => array(13021,13017,13005),
	'101501517' => array(13021,13017,13005),
	'101501567' => array(13021,13017,13005),
	'101501516' => array(13021,13017,13005),
	'101701512' => array(13021,13017,13005),
	'101701566' => array(13021,13017,13005),
	'101701511' => array(13021,13017,13005),
	'101801347' => array(13019,13017,13005),
	'101801393' => array(13019,13017,13005),
	'101801346' => array(13019,13017,13005),
	'101901252' => array(13019,13017,13005),
	'101901297' => array(13019,13017,13005),
	'101901251' => array(13019,13017,13005),
	'102001375' => array(13019,13017,13005),
	'102001421' => array(13019,13017,13005),
	'102001374' => array(13019,13017,13005),
	'102101190' => array(13019,13017,13005),
	'102101236' => array(13019,13017,13005),
	'102101189' => array(13019,13017,13005),
	'101301416' => array(13021,13017,13005),
	'100901532' => array(13021,13017,13005),
	'100101497' => array(13019,13017,13005),
	'101501518' => array(13021,13017,13005),
	'100002015' => array(13021,13017,13005),
	'100201678' => array(13021,13017,13005),
	'100901337' => array(13037,13001,13005),
	'101301242' => array(13037,13001,13005),
	'101501333' => array(13037,13001,13005),
	'100901341' => array(13037,13001,13005),
	'101301244' => array(13036,13001,13005),
	'101901104' => array(13035,13001,13005),
	'101701343' => array(13035,13001,13005),
	'100201478' => array(13037,13001,13005),
	'100101306' => array(13035,13001,13005),
	'102001221' => array(13035,13001,13005),
	'115001731' => array(13037,13001,13005),
	'100001707' => array(13037,13001,13005),
	'101801193' => array(13035,13001,13005),
	'100601404' => array(13035,13001,13005),
	'100501292' => array(13035,13001,13005),
	'115001732' => array(13035,13001,13005),
	'101501335' => array(13037,13001,13005),
	'101900960' => array(13035,13001,13005),
	'115001563' => array(13037,13001,13005),
	'115001564' => array(13035,13001,13005),
	'101701211' => array(13037,13001,13005),
	'102100800' => array(13035,13001,13005),
	'100201325' => array(13037,13001,13005),
	'100901177' => array(13037,13001,13005),
	'102001016' => array(13035,13001,13005),
	'100101161' => array(13035,13001,13005),
	'100501167' => array(13035,13001,13005),
	'101800973' => array(13035,13001,13005),
	'101301112' => array(13037,13001,13005),
	'101501193' => array(13037,13001,13005),
	'100001514' => array(13037,13001,13005),
	'100601249' => array(13035,13001,13005),
	'187000034' => array(13230,13231,13028),
	'187000033' => array(13230,13231,13028),
	'187000197' => array(13233,13017,13032),
	'187000201' => array(13008,13028,13005),
	'187060242' => array(13233,13017,13032),
	'187060248' => array(13233,13017,13032),
	'102001376' => array(13019,13017,13005),
	'100501455' => array(13019,13017,13005),
	'100601573' => array(13019,13017,13005),
	'101801348' => array(13019,13017,13005),
	'101701513' => array(13019,13017,13005),
	'115001974' => array(13021,13017,13005),
	'115001973' => array(13021,13017,13005),
	'102101037' => array(13035,13001,13005),
	
);