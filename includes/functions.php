<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

function check() {
	foreach(func_get_args() as $args) {
		if((@count($args)>0 and !@empty($args) and @isset($args)) || $args=='0') {
		} else {
			return;
		}
	}
	return true;
}

function debug($value) {
	echo '<pre>';
		print_r($value);
	echo '</pre>';
}

function sec_to_hms($input_seconds) {
	if($input_seconds >= 1) {
		$hours_module = $input_seconds % 3600;
		$hours = ($input_seconds-$hours_module)/3600;
		$minutes_module = $hours_module % 60;
		$minutes = ($hours_module-$minutes_module)/60;
		$seconds = $minutes_module;
		return array($hours,$minutes,$seconds);
	} else {
		return array(0,0,0);
	}
}

function sec_to_dhms($input_seconds) {
	if($input_seconds >= 1) {
		$days_module = $input_seconds % 86400;
		$days = ($input_seconds-$days_module)/86400;
		$hours_module = $days_module % 3600;
		$hours = ($days_module-$hours_module)/3600;
		$minutes_module = $hours_module % 60;
		$minutes = ($hours_module-$minutes_module)/60;
		$seconds = $minutes_module;
		return array($days,$hours,$minutes,$seconds);
	} else {
		return array(0,0,0,0);
	}
}

function redirect($location="") {
	$baseUrl = __BASE_URL__;
	$pageUrl = __PAGE_URL__;
	
	if(!check($location)) {
		header('Location: ' . $baseUrl);
		return;
	}
	if(Validator::Url($location)) {
		header('Location: ' . $location);
		return;
	}
	header('Location: ' . $pageUrl . $location);
	return;
}

function config($cfg, $return=true) {
	global $config;
	if(!check($cfg)) return;
	if($return) return $config[$cfg];
	echo $config[$cfg];
}

function isLoggedIn() {
	if(!check($_SESSION['valid'])) return;
	if($_SESSION['valid'] != true) return;
	if(!check($_SESSION['userid'])) return;
	if(!check($_SESSION['username'])) return;
	if(!check($_SESSION['email'])) return;
	return true;
}

function filterPost($varname) {
	return filter_input(INPUT_POST, $varname);
}

function filterGet($varname) {
	return filter_input(INPUT_GET, $varname);
}

function isServerValid($server) {
	if(!check($server)) return false;
	$allowed = array('siel','lumiel');
	if(!in_array(strtolower($server), $allowed)) return false;
	return true;
}

function getItemName($id) {
	if(!check($id)) return;
	$db = Handler::loadDB();
	$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($id));
	if(!is_array($itemInfo)) return;
	return $itemInfo['item_name'];
}

function getItemInfo($id) {
	if(!check($id)) return;
	$db = Handler::loadDB();
	$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($id));
	if(!is_array($itemInfo)) return;
	return $itemInfo;
}

function aionDatabaseLink($id) {
	return 'https://aioncodex.com/us/item/'.$id.'/';
}

function expToLevel($exp) {
	if ($exp <= '400') return '0';
	elseif ($exp <= '1433') return '1';
	elseif ($exp <= '3820') return '2';
	elseif ($exp <= '9054') return '3';
	elseif ($exp <= '17655') return '4';
	elseif ($exp <= '30978') return '5';
	elseif ($exp <= '52010') return '6';
	elseif ($exp <= '82982') return '7';
	elseif ($exp <= '126069') return '8';
	elseif ($exp <= '182252') return '9';
	elseif ($exp <= '260622') return '10';
	elseif ($exp <= '360825') return '11';
	elseif ($exp <= '490331') return '12';
	elseif ($exp <= '649169') return '13';
	elseif ($exp <= '844378') return '14';
	elseif ($exp <= '1080479') return '15';
	elseif ($exp <= '1393133') return '16';
	elseif ($exp <= '1793977') return '17';
	elseif ($exp <= '2282186') return '18';
	elseif ($exp <= '2881347') return '19';
	elseif ($exp <= '3659516') return '20';
	elseif ($exp <= '4622407') return '21';
	elseif ($exp <= '5821524') return '22';
	elseif ($exp <= '7227983') return '23';
	elseif ($exp <= '8835056') return '24';
	elseif ($exp <= '10699436') return '25';
	elseif ($exp <= '12853998') return '26';
	elseif ($exp <= '15255815') return '27';
	elseif ($exp <= '18061172') return '28';
	elseif ($exp <= '21551945') return '29';
	elseif ($exp <= '25635643') return '30';
	elseif ($exp <= '30490364') return '31';
	elseif ($exp <= '36299780') return '32';
	elseif ($exp <= '43890745') return '33';
	elseif ($exp <= '53559061') return '34';
	elseif ($exp <= '65392986') return '35';
	elseif ($exp <= '81005799') return '36';
	elseif ($exp <= '98965887') return '37';
	elseif ($exp <= '120006279') return '38';
	elseif ($exp <= '145305546') return '39';
	elseif ($exp <= '174901906') return '40';
	elseif ($exp <= '209678951') return '41';
	elseif ($exp <= '246923912') return '42';
	elseif ($exp <= '286491872') return '43';
	elseif ($exp <= '328812035') return '44';
	elseif ($exp <= '374157438') return '45';
	elseif ($exp <= '422165990') return '46';
	elseif ($exp <= '473102570') return '47';
	elseif ($exp <= '527287631') return '48';
	elseif ($exp <= '584861315') return '49';
	elseif ($exp <= '649149135') return '50';
	elseif ($exp <= '718967268') return '51';
	elseif ($exp <= '793470302') return '52';
	elseif ($exp <= '871508583') return '53';
	elseif ($exp <= '953180528') return '54';
	elseif ($exp <= '1039463797') return '55';
	elseif ($exp <= '1130615342') return '56';
	elseif ($exp <= '1226906283') return '57';
	elseif ($exp <= '1328622680') return '58';
	elseif ($exp <= '1434562107') return '59';
	elseif ($exp <= '1548141590') return '60';
	elseif ($exp <= '1667422949') return '61';
	elseif ($exp <= '1793319043') return '62';
	elseif ($exp <= '1926765410') return '63';
	elseif ($exp <= '2066885620') return '64';
	elseif ($exp <= '2631427378') return '65';
	/*elseif ($exp <= '4271005600') return '66';
	elseif ($exp <= '8023982311') return '67';
	elseif ($exp <= '15826312699') return '68';
	elseif ($exp <= '31430688278') return '69';
	elseif ($exp <= '62660507393') return '70';
	elseif ($exp <= '117158523579') return '71';
	elseif ($exp <= '212151338979') return '72';
	elseif ($exp <= '374747480973') return '73';
	elseif ($exp <= '608905517112') return '74';
	elseif ($exp <= '933228334012') return '75';*/
	else return '65';
}

function getRaceImg($race) {
	if($race == 'ELYOS') return '<img src="'.template_img(true).'character_icons/elyos-ico.png" data-toggle="tooltip" data-placement="top" title="Elyos" alt="Elyos"/>';
	return '<img src="'.template_img(true).'character_icons/asmodian-ico.png" data-toggle="tooltip" data-placement="top" title="Asmodian" alt="Asmodian"/>';
}

function getClassImg($class) {
	switch($class) {
		case 'SCOUT':
			return '<img src="'.template_img(true).'character_icons/scout.png" data-toggle="tooltip" data-placement="top" title="Scout" alt="Scout"/>';
			break;
		case 'CLERIC':
			return '<img src="'.template_img(true).'character_icons/cleric.png" data-toggle="tooltip" data-placement="top" title="Cleric" alt="Cleric"/>';
			break;
		case 'ENGINEER':
			return '<img src="'.template_img(true).'character_icons/cleric.png" data-toggle="tooltip" data-placement="top" title="Cleric" alt="Cleric"/>';
			break;
		case 'PRIEST':
			return '<img src="'.template_img(true).'character_icons/priest.png" data-toggle="tooltip" data-placement="top" title="Priest" alt="Priest"/>';
			break;
		case 'WARRIOR':
			return '<img src="'.template_img(true).'character_icons/warrior.png" data-toggle="tooltip" data-placement="top" title="Warrior" alt="Warrior"/>';
			break;
		case 'RANGER':
			return '<img src="'.template_img(true).'character_icons/ranger.png" data-toggle="tooltip" data-placement="top" title="Ranger" alt="Ranger"/>';
			break;
		case 'MAGE':
			return '<img src="'.template_img(true).'character_icons/mage.png" data-toggle="tooltip" data-placement="top" title="Mage" alt="Mage"/>';
			break;
		case 'TEMPLAR':
			return '<img src="'.template_img(true).'character_icons/templar.png" data-toggle="tooltip" data-placement="top" title="Templar" alt="Templar"/>';
			break;
		case 'GLADIATOR':
			return '<img src="'.template_img(true).'character_icons/gladiator.png" data-toggle="tooltip" data-placement="top" title="Gladiator" alt="Gladiator"/>';
			break;
		case 'CHANTER':
			return '<img src="'.template_img(true).'character_icons/chanter.png" data-toggle="tooltip" data-placement="top" title="Chanter" alt="Chanter"/>';
			break;
		case 'SPIRIT_MASTER':
			return '<img src="'.template_img(true).'character_icons/spiritmaster.png" data-toggle="tooltip" data-placement="top" title="Spirit Master" alt="Spirit Master"/>';
			break;
		case 'SORCERER':
			return '<img src="'.template_img(true).'character_icons/sorcerer.png" data-toggle="tooltip" data-placement="top" title="Sorcerer" alt="Sorcerer"/>';
			break;
		case 'ASSASSIN':
			return '<img src="'.template_img(true).'character_icons/assassin.png" data-toggle="tooltip" data-placement="top" title="Assassin" alt="Assassin"/>';
			break;
		case 'BARD':
			return '<img src="'.template_img(true).'character_icons/bard.png" data-toggle="tooltip" data-placement="top" title="Bard" alt="Bard"/>';
			break;
		case 'GUNNER':
			return '<img src="'.template_img(true).'character_icons/gunner.png" data-toggle="tooltip" data-placement="top" title="Gunner" alt="Gunner"/>';
			break;
		case 'RIDER':
			return '<img src="'.template_img(true).'character_icons/rider.png" data-toggle="tooltip" data-placement="top" title="Rider" alt="Rider"/>';
			break;
		default:
			return $class;
	}
}

function getGenderImg($gender) {
	if($gender == 'MALE') return '<img src="'.template_img(true).'character_icons/male.png" data-toggle="tooltip" data-placement="top" title="Male" alt="Male"/>';
	return '<img src="'.template_img(true).'character_icons/female.png" data-toggle="tooltip" data-placement="top" title="Female" alt="Female"/>';
}

function getLocationName($worldId) {
	$locationDefinition = config('location_definition', true);
	if(!is_array($locationDefinition)) return 'Unknown';
	
	if(!array_key_exists($worldId, $locationDefinition)) return 'Unknown';
	return $locationDefinition[$worldId];
}

function getOnlineStatusImg($status) {
	if($status == 1) {
		return '<img src="'.template_img(true).'character_icons/on.png" data-toggle="tooltip" data-placement="top" title="Online"/>';
	}
	
	return '<img src="'.template_img(true).'character_icons/off.png" data-toggle="tooltip" data-placement="top" title="Offline"/>';
}

function loadCacheFile($filename) {
	if(!check($filename)) return;
	if(!file_exists(__PATH_CACHE__ . $filename)) return;
	return file_get_contents(__PATH_CACHE__ . $filename);
}

function rankingCacheToArray($cacheData) {
	if(!check($cacheData)) return;
	$rows = explode("||", $cacheData);
	if(is_array($rows)) {
		foreach($rows as $row) {
			$returnData[] = explode(",", $row);
		}
	}
	return $returnData;
}

function votePromo() {
	$votePromo = config('vote_promo', true);
	if(!is_array($votePromo)) return;
	
	foreach($votePromo as $promo) {
		$promoStart = strtotime($promo[0]);
		$promoEnd = strtotime($promo[1]);
		$promoReward = $promo[2];
		
		if(Validator::Number(time(), $promoEnd, $promoStart)) {
			$return = array(
				'start' => $promoStart,
				'end' => $promoEnd,
				'reward' => $promoReward
			);
			
			return $return;
		}
	}
	
	return;
}

function voteCheckReferer() {
	$voteSites = config('vote_sites', true);
	if(!is_array($voteSites)) return;
	
	$httpReferer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	if(!check($httpReferer)) return;
	
	foreach($voteSites as $id => $site) {
		$referers = $site[4];
		if(in_array($httpReferer, $referers)) {
			
			$_SESSION['httpref'] = $httpReferer;
			echo '<script type="text/javascript">';
				echo 'window.location.href = "'.__PAGE_URL__.'usercp/vote/return/'.$id.'";';
			echo '</script>';
			die('Redirecting...');
		}
	}
}

function isOnline($accountId, $server="siel") {
	if(!check($accountId)) return;
	if(!check($server)) return;
	
	if(strtolower($server) == 'siel' || strtolower($server) == 'all') {
		$sdb = Handler::loadDB('siel');
		$sielCharacters = $sdb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($accountId));
		if(is_array($sielCharacters)) {
			foreach($sielCharacters as $character) {
				if($character['online'] == 1) return true;
			}
		}
	}
	
	if(strtolower($server) == 'lumiel' || strtolower($server) == 'all') {
		$ldb = Handler::loadDB('lumiel');
		$lumielCharacters = $ldb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($accountId));
		if(is_array($lumielCharacters)) {
			foreach($lumielCharacters as $character) {
				if($character['online'] == 1) return true;
			}
		}
	}
	
	return;
}

function legionEmblemCacheExists($legionId) {
	if(!check($legionId)) return;
	
	$fileName = $legionId . '.png';
	if(!file_exists(__PATH_EMBLEMS_ROOT__ . $fileName)) return;
	
	return true;
}

function cacheLegionEmblem($legionId) {
	if(!check($legionId)) return;
	$sdb = Handler::loadDB('siel');
	
	$fileName = $legionId . '.png';
	if(file_exists(__PATH_EMBLEMS_ROOT__ . $fileName)) return; // cache file already exists
	
	$url = __BASE_URL__ . 'legionemblem/'.$legionId.'.dds';
	$data = json_decode(file_get_contents('http://api.rest7.com/v1/image_convert.php?url=' . $url . '&format=png'));

	if (@$data->success !== 1)
	{
		// SET DEFAULT LEGION EMBLEM
		@copy(__ROOT_DIR__ . 'cache/emblem/blank.png', __PATH_EMBLEMS_ROOT__ . $legionId . '.png');
		return;
	}
	$image = file_get_contents($data->file);
	
	if(!file_put_contents(__PATH_EMBLEMS_ROOT__ . $legionId . '.png', $image)) return;
	
	return true;
}

function legionEmblem($legionId) {
	if(!check($legionId)) return;
	
	if(!legionEmblemCacheExists($legionId)) {
		cacheLegionEmblem($legionId);
	}
	
	if(!legionEmblemCacheExists($legionId)) return;
	
	return __PATH_LEGION_EMBLEM__ . $legionId . '.png';
}

function getSkillName($id) {
	if(!check($id)) return;
	$db = Handler::loadDB();
	$skillInfo = $db->queryFetchSingle("SELECT * FROM `aion_skilllist` WHERE `skill_id` = ?", array($id));
	if(!is_array($skillInfo)) return;
	return $skillInfo['skill_name'];
}

function aioncmsBuildAionItemList($itemId) {
	$apiRequest = file_get_contents(__BASE_URL__.'api/item.php?id=' . $itemId);
	if(!$apiRequest) return;
	
	$apiResult = JSON_DECODE($apiRequest, true);
	if(!is_array($apiResult)) return;
	
	if(!check($apiResult['name'])) return;
	if(!check($apiResult['icon'])) return;
	
	$db = Handler::loadDB();
	$addItem = $db->query("INSERT INTO `aion_itemlist` (item_id, item_name, item_icon) VALUES (?, ?, ?)", array($itemId, $apiResult['name'], $apiResult['icon']));
	if(!$addItem) return;
	return true;
}

// https://www.powderkegwebdesign.com/web-dev-tid-bit-grabbing-id-youtube-url-php/
function getYouTubeVideoIdFromUrl($url) {
	$params = null;

	//Video ID
	preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $video_parts);
	if(isset($video_parts[1])) {
		$params = $video_parts[1];
	} else {
		$params = $url;
	}
	
	return $params;
}

// https://stackoverflow.com/a/13353502
function getImageExtensionFromUrl($url) {
	$filename_from_url = parse_url($url);
	$ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
	if(!check($ext)) return;
	return $ext;
}

function generateLegionProfileUrl($id, $name) {
	$legionName = preg_replace('/\s+/', '-', trim($name));
	$result = __BASE_URL__ . 'legion/' . $id . '/' . $legionName;
	return $result;
}