<?php
// Am Anfang jeder Seite
include(__DIR__ . '/../countdown_timer.php');
?>
<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block votenreward"></div>
<br /><br />

<?php
$voteSites = config('vote_sites', true);
$votePromo = config('vote_promo', true);

try {
	//if(!$_SESSION['is_staff']) throw new Exception('Under maintenance, try again in a few minutes.');
	if(!is_array($voteSites)) throw new Exception('Error, please contact the administrator.');
	
	$db = Handler::loadDB();
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	$accountSecurity = $Account->getExtraSecurityData();
	$accountVotes = $Account->getVotes();
	
	$currentYear = (int) date("Y");
	$currentMonth = (int) date("m");
	$checkVoteCount = $db->queryFetchSingle("SELECT * FROM `aioncms`.`votes_count` WHERE `id` = ? AND `year` = ? AND `month` = ?", array($_SESSION['userid'], $currentYear, $currentMonth));
	if(is_array($checkVoteCount)) {
		if(check($checkVoteCount['character'])) {
			$sdb = Handler::loadDB('siel');
			$playerInfo = $sdb->queryFetch("SELECT * FROM `players` WHERE `name` = ? AND `account_id` = ?", array($checkVoteCount['character'], $_SESSION['userid']));
			if(!is_array($playerInfo)) {
				# unlink player name from vote ranking (player is no longer on this account)
				$unlinkCharacter = $db->query("UPDATE `aioncms`.`votes_count` SET `character` = NULL, `last_update` = now() WHERE `id` = ? AND `year` = ? AND `month` = ?", array($_SESSION['userid'], $currentYear, $currentMonth));
				
				# reload vote count
				$checkVoteCount = $db->queryFetchSingle("SELECT * FROM `aioncms`.`votes_count` WHERE `id` = ? AND `year` = ? AND `month` = ?", array($_SESSION['userid'], $currentYear, $currentMonth));
			}
		}
		
		if(!check($checkVoteCount['character'])) {
			message('You have opted-in to the '.date("F").' vote rankings, however the character you selected is no longer in your account. You need to choose a new main character, otherwise your votes will not count. <a href="'.module_url('usercp/voteranking/', true).'">CLICK HERE</a> to proceed.', 'error');
		}
	}
	
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	
	# how to vote
	echo '<h4>How to vote:</h4>';
	echo '<ol>';
		echo '<li>Click the <span class="btn btn-xs btn-primary">Vote!</span> button.</li>';
		echo '<li>Type the words you see in the captcha and vote.</li>';
		echo '<li>Click on our banner to return.</li>';
	echo '</ol>';
	
	# promo
	$activePromo = votePromo();
	if(is_array($activePromo)) {
		echo '<div class="text-center" style="color:green;margin: 20px auto;">';
			echo '<h2>Promo Active!</h2>';
			echo '<p><strong>'.$activePromo['reward'].'</strong> credits/vote from <strong>'.date("M jS", $activePromo['start']).'</strong> thru <strong>'.date("M jS", $activePromo['end']).'</strong></p>';
			echo '<p style="color:#ffae00;"><strong>'.floor($activePromo['reward']*1.5).'</strong> credits/vote if <strong>VIP</strong></p>';
		echo '</div>';
		
		$promoActive = true;
	}
	
	# vote OUT process
	if(check($_GET['site'])) {
		try {
			if(!array_key_exists($_GET['site'], $voteSites)) throw new Exception('Your request could not be completed, please try again later.');
			
			# check account votes
			if(is_array($accountVotes)) {
				if(array_key_exists($_GET['site'], $accountVotes)) {
					if(time() < $accountVotes[$_GET['site']]['newdate']) throw new Exception('You already voted for this site in the last 12 hours.');
				}
			}
			
			$_SESSION['check_referer'] = true;
			
			logSystem::add('vote reward OUT');
			
			if(is_array($voteSites[$_GET['site']][1])) {
				# multi link vote site
				# 50/50 chance each link
				if(mt_rand(1,100) > 50) {
					redirect($voteSites[$_GET['site']][1][0]);
				} else {
					redirect($voteSites[$_GET['site']][1][1]);
				}
			} else {
				# single link
				redirect($voteSites[$_GET['site']][1]);
			}
			
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	# vote IN process (return)
	if(check($_GET['return'], $_SESSION['httpref'])) {
		try {
			//if($_SESSION['username'] != 'daeron91') throw new Exception('This module is currently under development.'); # DEV
			
			# load siel and lumiel databases
			$sdb = Handler::loadDB('siel');
			
			# vote site id
			$voteSiteId = $_GET['return'];
			
			# check vote site id
			if(!array_key_exists($voteSiteId, $voteSites)) throw new Exception('1');
			$voteSiteReferers = $voteSites[$voteSiteId][4];
			
			# check if coming from correct vote site
			if(!in_array($_SESSION['httpref'], $voteSiteReferers)) throw new Exception('2');
			
			# EXTRA: levenstein check (similar accounts, last 20 or so (DISTINCT))
			
			# check account votes
			if(is_array($accountVotes)) {
				if(array_key_exists($voteSiteId, $accountVotes)) {
					if(time() < $accountVotes[$voteSiteId]['newdate']) throw new Exception('You already voted for this site in the last 12 hours.');
				}
			}
			
			# check ip
			$userIP = Handler::userIP();
			$checkIP = $db->queryFetchSingle("SELECT max(`newdate`) as unlockTime FROM `aioncms`.`votes` WHERE `ip` = ? AND `site` = ?", array($userIP, $voteSiteId));
			if(check($checkIP['unlockTime'])) {
				if(time() < $checkIP['unlockTime']) throw new Exception('Your IP address already voted in the last 12 hours.');
			}
			
			# check mac
			$userMAC = $accountData['last_mac'];
			if(!check($userMAC)) throw new Exception('You must play the game to unlock the voting system. [1]');
			if($userMAC == 'xx-xx-xx-xx-xx-xx') throw new Exception('You must play the game to unlock the voting system. [2]');
			$checkMAC = $db->queryFetchSingle("SELECT max(`newdate`) as unlockTime FROM `aioncms`.`votes` WHERE `mac` = ? AND `site` = ?", array($userMAC, $voteSiteId));
			if(check($checkMAC['unlockTime'])) {
				if(time() < $checkMAC['unlockTime']) throw new Exception('You already voted from this computer in the last 12 hours.');
			}
			
			# check if account has characters
			$sielCharacters = $sdb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($accountData['id']));
			if(!is_array($sielCharacters)) throw new Exception('Please create at least one character to use the voting system.');
			
			# check characters last online
			$hasLoggedIn = false;
			if(is_array($sielCharacters)) {
				foreach($sielCharacters as $sielCharacter) {
					
					$timeLimit = time()-(86400*7);
					$lastOnline = strtotime($sielCharacter['last_online']);
					
					if($lastOnline > $timeLimit) {
						$hasLoggedIn = true;
					}
				}
			}
			if(!$hasLoggedIn) throw new Exception('Looks like you have not been active in the last 7 days. Please log-in to the game to unlock the voting system.');
			
			# clear vote log
			$clearVote = $db->query("DELETE FROM `aioncms`.`votes` WHERE `name` = ? AND `site` = ?", array($accountData['name'], $voteSiteId));
			if(!$clearVote) throw new Exception('There was a problem clearing your vote, please contact support.');
			
			# update vote
			$newDate = time() + 60*60*12;
			$updateVote = $db->query("INSERT INTO `aioncms`.`votes` (`name`, `ip`, `site`, `newdate`, `mac`) VALUES (?, ?, ?, ?, ?)", array($accountData['name'], $userIP, $voteSiteId, $newDate, $userMAC));
			if(!$updateVote) throw new Exception('There was a problem registering your vote, please contact support.');
			
			# vote reward check
			$creditReward = $voteSites[$voteSiteId][0];
			if(!check($creditReward)) throw new Exception('There was a problem loading vote data, please contact support.');
			if($creditReward < 1) throw new Exception('There was a problem loading vote data, please contact support. [2]');
			
			# check promos
			if($promoActive == true) {
				$creditReward = $activePromo['reward'];
			}
			
			# check if VIP
			if($accountData['membership'] == 2 && strtotime($accountData['expire']) > time()) {
				$creditReward = floor($creditReward*1.5);
			} else {
				
				# reward as VIP is player is online in-game
				if(isOnline($accountData['id'], 'all')) {
					$creditReward = floor($creditReward*2.75);
				}
			}
			
			# reward user
			$sendCredits = $Account->addCredits($creditReward);
			if(!$sendCredits) throw new Exception('There was a problem sending the reward, please contact support.');
			
			# add vote count
			if(is_array($checkVoteCount)) {
				if(check($checkVoteCount['character'])) {
					$addVoteCount = $db->query("UPDATE `aioncms`.`votes_count` SET `votes` = `votes` + 1, `last_update` = now() WHERE `id` = ? AND `year` = ? AND `month` = ?", array($_SESSION['userid'], $currentYear, $currentMonth));
				}
			}
			
			# EXTRA: check last login of account (it doesnt work for some accounts)
			
			# success message
			message('<strong>Great!</strong> your account has been rewarded <strong>'.$creditReward.'</strong> credits.', 'success');
			
			/*
			$chance = 30;
			if(mt_rand(0, 100) <= $chance) {
				message('<strong>Yay!</strong> you got a free regular token!', 'warning');
			}
			*/
			
			logSystem::add('vote reward IN successful');
			
			# RANDOM ENCHANTMENT TOKEN
			$randomToken = mt_rand(1,100);
			$randomTokenChance = config('vote_enchant_token_chance', true); // percent
			
			
			//if(!is_array($checkTokens)) $randomTokenChance = 100; // give free 1st token
			
			if($randomToken<=$randomTokenChance) {
				
				# give free token
				$checkTokens = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_enchant_tokens` WHERE `account_id` = ?", array($accountData['id']));
				if(is_array($checkTokens)) {
					# update tokens
					$giveToken = $db->query("UPDATE `aioncms`.`website_enchant_tokens` SET `tokens` = `tokens` + 1 WHERE `account_id` = ?", array($accountData['id']));
					
				} else {
					# insert into tokens table
					$giveToken = $db->query("INSERT INTO `aioncms`.`website_enchant_tokens` (`account_id`, `tokens`) VALUES (?, ?)", array($accountData['id'],1));
					
				}
				if($giveToken) {
					message('<strong>Look who got lucky!</strong> you have won a free enchantment token!', 'warning');
					logSystem::add('free enchant token given');
				}
				
			}
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
		
		# vote process finished -> dont check for referer
		unset($_SESSION['check_referer']);
		unset($_SESSION['httpref']);
	}
	
	echo '<br />';
	
	# site list
	echo '<table class="table">';
	echo '<thead>';
		echo '<tr>';
			echo '<th></th>';
			echo '<th>Cooldown</th>';
			echo '<th>Reward</th>';
			echo '<th></th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($voteSites as $site => $row) {
		
		# check cooldown
		if(is_array($accountVotes)) {
			if(array_key_exists($site, $accountVotes)) {
				$offset = $accountVotes[$site]['newdate']-time();
				if($offset < 1) {
					$cooldown = '<i>none</i>';
				} else {
					$cldn = sec_to_hms($offset);
					if($cldn[0] == 0) {
						$cooldown = '<strong>'.$cldn[1].'</strong> min(s)';
					} else {
						$cooldown = '<strong>'.$cldn[0].'</strong> hr(s)';
					}
				}
			} else {
				$cooldown = '<i>none</i>';
			}
		} else {
			$cooldown = '<i>none</i>';
		}
		
		$reward = '<td class="vert-align">'.$row[0].' credits</td>';
		if($promoActive == true) {
			$reward = '<td class="vert-align" style="color:green;"><strong>'.$activePromo['reward'].'</strong> credits</td>';
		}
		
		if($accountData['membership'] == 2 && strtotime($accountData['expire']) > time()) {
			# account has VIP (1.5x rewards)
			$vipReward = floor($row[0]*1.5);
			if($promoActive == true) {
				# vip + promo reward
				$vipReward = floor($activePromo['reward']*2.75);
			}
			$reward = '<td class="vert-align" style="color:#ffae00;"><strong>'.$vipReward.'</strong> credits</td>';
		} else {
			if(isOnline($_SESSION['userid'], 'all')) {
				$vipReward = floor($row[0]*2.75);
				if($promoActive == true) {
					# vip + promo reward
					$vipReward = floor($activePromo['reward']*2.75);
				}
				$reward = '<td class="vert-align" style="color:#ffae00;"><strong>'.$vipReward.'</strong> credits</td>';
			}
		}
		
		echo '<tr>';
			echo '<td class="vert-align"><img class="vote-img" src="'.template_img(true).'vote/'.$row[2].'" title="'.$row[3].'" alt="'.$row[3].'"/></td>';
			echo '<td class="vert-align">'.$cooldown.'</td>';
			echo $reward;
			echo '<td class="vert-align"><a href="'.module_url('usercp/vote/site/' . $site, true).'" class="btn btn-xs btn-primary">Vote!</a></td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
	echo '<div class="text-center" style="color:red;font-size: 12px;">Abusing the vote reward system in any way will get your account permanently banned!</div>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'warning');
}
?>