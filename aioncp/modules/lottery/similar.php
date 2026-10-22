<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	$Lottery = new LotterySystem();
	
	$config['proximity'] = 3;
	$results = $db->queryFetch("SELECT `account_data`.`name` FROM `lottery_tickets` INNER JOIN `account_data` ON `lottery_tickets`.`userid` = `account_data`.`id` WHERE `lottery_tickets`.`lottery_id` = ? GROUP BY `account_data`.`name`", array($Lottery->getLotteryData('name')));
	if(!is_array($results)) throw new Exception('No similar accounts found.');
	
	foreach($results as $log) {
		$soundex = $db->queryFetch("SELECT `account_data`.`name` FROM `lottery_tickets` INNER JOIN `account_data` ON `lottery_tickets`.`userid` = `account_data`.`id` WHERE SOUNDEX(`account_data`.`name`) = SOUNDEX(?) AND `account_data`.`name` != ? AND `lottery_tickets`.`lottery_id` = ? GROUP BY `account_data`.`name`", array($log['name'],$log['name'],$Lottery->getLotteryData('name')));
		if(count($soundex) >= 1) {
			$smlr = array();
			
			foreach($soundex as $similar) {
				$proximity = levenshtein($log['name'], $similar['name']);
				if($proximity <= $config['proximity']) {
					$smlr[] = $similar['name'];
				}
			}
			
			if(is_array($smlr) && count($smlr) >= 1) {
				$final[$log['name']] = $smlr;
			}
		}
	}
	
	if(!is_array($final)) throw new Exception('No similar accounts found.');
	
	foreach($final as $key => $finalres) {
		$cRecord = $key;
		foreach($finalres as $inner) {
			if(array_key_exists($inner, $final)) {
				unset($final[$cRecord]);
			}
		}
	}
	
	if(check($_GET['lock'], $_GET['userid'])) {
		$message = "You have purchased tickets with multiple accounts, thus breaking one of the lottery conditions. Please contact us at the forum to get your stash unlocked.";
		$check = $db->queryFetchSingle("SELECT * FROM `lottery_stash` WHERE `userid` = ?", array($_GET['userid']));
		if(!is_array($check)) {
			$do = $db->query("INSERT INTO `lottery_stash` (userid, locked, note) VALUES (?, ?, ?)", array($_GET['userid'], 1, $message));
			if($do) {
				echo '<div style="color:green;">STASH LOCKED</div>';
			} else {
				echo '<div style="color:red;">ERROR</div>';
			}
		} else {
			if($check['locked'] != 1) {
				$do = $db->query("UPDATE `lottery_stash` SET locked = ?, note = ? WHERE userid = ?", array(1, $message, $_GET['userid']));
				if($do) {
					echo '<div style="color:green;">STASH LOCKED</div>';
				} else {
					echo '<div style="color:red;">ERROR</div>';
				}
			} else {
				echo '<div style="color:red;">THIS STASH IS ALREADY LOCKED</div>';
			}
		}
	}
	
	echo '<ul style="list-style-type:square;">';
	foreach($final as $key => $row) {
		
		$tickets_1 = $db->queryFetch("SELECT * FROM `lottery_tickets` WHERE `lottery_tickets`.`userid` = (SELECT `account_data`.`id` FROM `account_data` WHERE `account_data`.`name` = ?) AND `lottery_tickets`.`lottery_id` = ?", array($key, $Lottery->getLotteryData('name')));
		foreach($tickets_1 as $t1) {
			$tt1[$key][] = $t1['id'];
		}
		$ttx = implode(" ", $tt1[$key]);
		
		echo '<li>' . $key . ' ['.$t1['userid'].']['.$ttx.'] (<a href="similar.php?lock=1&userid='.$t1['userid'].'">lock</a>)';
			echo '<ul style="list-style-type:none;margin-bottom: 20px;">';
				foreach($row as $roww) {
					
					$tickets_2 = $db->queryFetch("SELECT * FROM `lottery_tickets` WHERE `lottery_tickets`.`userid` = (SELECT `account_data`.`id` FROM `account_data` WHERE `account_data`.`name` = ? AND `lottery_tickets`.`lottery_id` = ?)", array($roww, $Lottery->getLotteryData('name')));
					foreach($tickets_2 as $t2) {
						$tt2[$roww][] = $t2['id'];
					}
					$tty = implode(" ", $tt2[$roww]);
		
					echo '<li>' . $roww . ' ['.$t2['userid'].']['.$tty.'] (<a href="similar.php?lock=1&userid='.$t2['userid'].'">lock</a>)</li>';
				}
			echo '</ul>';
		echo '</li>';
	}
	echo '</ul>';
	
	
} catch (Exception $ex) {
	message($ex->getMessage(), 'error');
}