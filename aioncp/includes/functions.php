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

function redirect($location="") {
	if(!check($location)) {
		header('Location: ' . __BASE_URL__);
		return;
	}
	if(Validator::Url($location)) {
		header('Location: ' . $location);
		return;
	}
	header('Location: ' . __BASE_URL__ . $location);
	return;
}

function config($cfg, $return=true) {
	global $config;
	if(!check($cfg)) return;
	if($return) return $config[$cfg];
	echo $config[$cfg];
}

function filterPost($varname) {
	return filter_input(INPUT_POST, $varname);
}

function message($message="",$type="") {
	if(!check($message)) return;
	switch($type) {
		case "success":
			echo '<div class="alert alert-success">'.$message.'</div>';
			break;
		case "error":
			echo '<div class="alert alert-danger">'.$message.'</div>';
			break;
		case "warning":
			echo '<div class="alert alert-warning">'.$message.'</div>';
			break;
		default:
			echo '<div class="alert alert-info">'.$message.'</div>';
	}
}

function highRankAccess() {
	if($_SESSION['aioncp']['access_level'] >= 50) return true;
	return;
}

function getAccountNameFromId($id) {
	$db = Handler::loadDB('db');
	if(!check($id)) return;
	$result = $db->queryFetchSingle("SELECT `name` FROM `account_data` WHERE `id` = ?", array($id));
	if(is_array($result)) return $result['name'];
	return;
}

function unstickPlayer($name, $race)  {
	$sdb = Handler::loadDB('sdb');
	if(!check($name)) return;
	if(!check($race)) return;
	if($race == 'ELYOS') {
		$data = array(
			'210010000',
			'1209.86',
			'1044.74',
			'140.428',
			'0',
			$name
		);
	} else {
		$data = array(
			'220010000',
			'571.039',
			'2787.34',
			'299.875',
			'0',
			$name
		);
	}
	
	$unstick = $sdb->query("UPDATE `players` SET `world_id` = ?, `x` = ?, `y` = ?, `z` = ?, `online` = ? WHERE `name` = ?", $data);
	if(!$unstick) return;
	
	return true;
}

function banAccount($name) {
	$db = Handler::loadDB('db');
	if(!check($name)) return;
	$result = $db->query("UPDATE `account_data` SET `ip_force` = 1 WHERE `name` = ?", array($name));
	if(!$result) return false;
	return true;
}

function unbanAccount($name) {
	$db = Handler::loadDB('db');
	if(!check($name)) return;
	$result = $db->query("UPDATE `account_data` SET `ip_force` = null WHERE `name` = ?", array($name));
	if(!$result) return false;
	return true;
}

function expToLevel($exp) {
	if ($exp <= '400') return '1';
	elseif ($exp <= '1433') return '2';
	elseif ($exp <= '3820') return '3';
	elseif ($exp <= '9054') return '4';
	elseif ($exp <= '17655') return '5';
	elseif ($exp <= '30978') return '6';
	elseif ($exp <= '52010') return '7';
	elseif ($exp <= '82982') return '8';
	elseif ($exp <= '126069') return '9';
	elseif ($exp <= '182252') return '10';
	elseif ($exp <= '260622') return '11';
	elseif ($exp <= '360825') return '12';
	elseif ($exp <= '490331') return '13';
	elseif ($exp <= '649169') return '14';
	elseif ($exp <= '844378') return '15';
	elseif ($exp <= '1080479') return '16';
	elseif ($exp <= '1393133') return '17';
	elseif ($exp <= '1793977') return '18';
	elseif ($exp <= '2282186') return '19';
	elseif ($exp <= '2881347') return '20';
	elseif ($exp <= '3659516') return '21';
	elseif ($exp <= '4622407') return '22';
	elseif ($exp <= '5821524') return '23';
	elseif ($exp <= '7227983') return '24';
	elseif ($exp <= '8835056') return '25';
	elseif ($exp <= '10699436') return '26';
	elseif ($exp <= '12853998') return '27';
	elseif ($exp <= '15255815') return '28';
	elseif ($exp <= '18061172') return '29';
	elseif ($exp <= '21551945') return '30';
	elseif ($exp <= '25635643') return '31';
	elseif ($exp <= '30490364') return '32';
	elseif ($exp <= '36299780') return '33';
	elseif ($exp <= '43890745') return '34';
	elseif ($exp <= '53559061') return '35';
	elseif ($exp <= '65392986') return '36';
	elseif ($exp <= '81005799') return '37';
	elseif ($exp <= '98965887') return '38';
	elseif ($exp <= '120006279') return '39';
	elseif ($exp <= '145305546') return '40';
	elseif ($exp <= '174901906') return '41';
	elseif ($exp <= '209678951') return '42';
	elseif ($exp <= '246923912') return '43';
	elseif ($exp <= '286491872') return '44';
	elseif ($exp <= '328812035') return '45';
	elseif ($exp <= '374157438') return '46';
	elseif ($exp <= '422165990') return '47';
	elseif ($exp <= '473102570') return '48';
	elseif ($exp <= '527287631') return '49';
	elseif ($exp <= '584861315') return '50';
	elseif ($exp <= '649149135') return '51';
	elseif ($exp <= '718967268') return '52';
	elseif ($exp <= '793470302') return '53';
	elseif ($exp <= '871508583') return '54';
	elseif ($exp <= '953180528') return '55';
	elseif ($exp <= '1039463797') return '56';
	elseif ($exp <= '1130615342') return '57';
	elseif ($exp <= '1226906283') return '58';
	elseif ($exp <= '1328622680') return '59';
	elseif ($exp <= '1434562107') return '60';
	elseif ($exp <= '1548141590') return '61';
	elseif ($exp <= '1667422949') return '62';
	elseif ($exp <= '1793319043') return '63';
	elseif ($exp <= '1926765410') return '64';
	elseif ($exp <= '2066885620') return '65';
	elseif ($exp <= '2631427378') return '66';
	elseif ($exp <= '4271005600') return '67';
	elseif ($exp <= '8023982311') return '68';
	elseif ($exp <= '15826312699') return '69';
	elseif ($exp <= '31430688278') return '70';
	elseif ($exp <= '62660507393') return '71';
	elseif ($exp <= '117158523579') return '72';
	elseif ($exp <= '212151338979') return '73';
	elseif ($exp <= '374747480973') return '74';
	elseif ($exp <= '608905517112') return '75';
	//elseif ($exp <= '933228334012') return '76';
	else return '75';
}

function generateLegionProfileUrl($id, $name) {
	$legionName = preg_replace('/\s+/', '-', trim($name));
	$result = __GA_BASE_URL__ . 'legion/' . $id . '/' . $legionName;
	return $result;
}

function checkVersion() {
	$url = 'http://version.aioncms.com/1.0/index.php';
	
	$fields = array(
		'version' => urlencode(__AIONCMS_VERSION__),
		'baseurl' => urlencode(dirname(__BASE_URL__)),
	);
	
	foreach($fields as $key => $value) {
		$fieldsArray[] = $key . '=' . $value;
	}
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, count($fields));
	curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $fieldsArray));
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'WebEngine');
	curl_setopt($ch, CURLOPT_HEADER, false);
	$result = curl_exec($ch);
	curl_close($ch);
	
	if(!$result) return;
	$resultArray = json_decode($result, true);
	if(!is_array($resultArray)) return;
	return $resultArray;
}