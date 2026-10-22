<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?><div class="page-header-block usercp"></div>
<br /><br />

<h3>Player Unstuck Request</h3>
<p>Your request will be reviewed as soon as possible!</p>

<?php
try {
	
	if(!isServerValid($_GET['server'])) throw new Exception('Your request could not be completed, please try again later.');
	
	# load server database
	if($_GET['server'] == 'siel') {
		$sdb = Handler::loadDB('siel');
	} else {
		$sdb = Handler::loadDB('lumiel');
	}
	
	# player data
	$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `account_id` = ? AND `name` = ?", array($_SESSION['userid'], $_GET['player']));
	if(!is_array($playerData)) throw new Exception('Your request could not be completed, please try again later. [2]');
	
	# check for existing requests
	$existingRequest = $sdb->queryFetchSingle("SELECT * FROM `aioncms`.`unstick` WHERE `player` = ?", array($playerData['name']));
	if(is_array($existingRequest)) throw new Exception('You already have an unstuck request for this character.');
	
	# add new request
	$addRequest = $sdb->query("INSERT INTO `aioncms`.`unstick` (`player`,`race`,`account`) VALUES (?, ?, ?)", array($playerData['name'], $playerData['race'], $_SESSION['username']));
	if(!$addRequest) throw new Exception('Your request could not be completed, please try again later. [3]');
	
	message('Player unstuck request has been sent!', 'success');
	logSystem::add('submitted unstuck request ('.$playerData['name'].')');
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}

?>