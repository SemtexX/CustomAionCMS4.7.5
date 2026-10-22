<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

# Access
define('access', 'cron');

# Path
$file_name = basename(__FILE__);
$sys_path = str_replace('\\','/',dirname(dirname(__FILE__))).'/';

# Load system
try {

	if(!@include_once($sys_path . 'includes/system.php')) {
		throw new Exception('Could not load engine.');
	}
	
	if(!check($_GET['id'])) throw new Exception('invalid request.');
	
	$aiondatabase = file_get_contents('https://aioncodex.com/tip.php?nf=on&l=us&id=item--' . $_GET['id']);
	$itemDataArray = array();
	
	$itemNamePattern = "/<b>(.*?)<\/b>/";
    $itemNameSearch = preg_match($itemNamePattern, $aiondatabase, $itemNameMatches);
	if($itemNameSearch == 1) {
		$itemDataArray['name'] = $itemNameMatches[1];
	}
	
	$itemQualityPattern = "/quality-(.*?)\"/";
    $itemQualitySearch = preg_match($itemQualityPattern, $aiondatabase, $itemQualityMatches);
	if($itemQualitySearch == 1) {
		$itemDataArray['quality'] = $itemQualityMatches[1];
	}
	
	$itemIconPattern = "/items\/(.*?).png/";
    $itemIconSearch = preg_match($itemIconPattern, $aiondatabase, $itemIconMatches);
	if($itemIconSearch == 1) {
		$itemDataArray['icon'] = $itemIconMatches[1];
	}
	
	$itemSkillIdPattern = "/skill--(.*?)\"/";
    $itemSkillIdSearch = preg_match_all($itemSkillIdPattern, $aiondatabase, $itemSkillIdMatches);

	$itemSkillIconPattern = "/skills\/(.*?).png/";
    $itemSkillIconSearch = preg_match_all($itemSkillIconPattern, $aiondatabase, $itemSkillIconMatches);
	
	if(check($itemSkillIdMatches[1][0], $itemSkillIconMatches[1][0])) {
		$itemDataArray['skills'][$itemSkillIdMatches[1][0]] = $itemSkillIconMatches[1][0];
	}
	
	if(check($itemSkillIdMatches[1][2], $itemSkillIconMatches[1][1])) {
		$itemDataArray['skills'][$itemSkillIdMatches[1][2]] = $itemSkillIconMatches[1][1];
	}
	
	if(check($itemSkillIdMatches[1][4], $itemSkillIconMatches[1][2])) {
		$itemDataArray['skills'][$itemSkillIdMatches[1][4]] = $itemSkillIconMatches[1][2];
	}
	
	echo JSON_ENCODE($itemDataArray);
	
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}