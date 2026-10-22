<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class LotterySystem {
	
	private $_active = true;
	
	private $_activeTicketLimit = 5;
	
	private $_defaultInitialJackpot = 1000;
	private $_initialJackpot = 1000;
	private $_minNumber = 0;
	private $_maxNumber = 20;
	public $_ticketCost = 20;
	public $_ticketProfit = 20; // percent

	// NEW
	private $_matches4 = 55;
	private $_matches3 = 15;
	private $_matches2 = 15;
	private $_matches1 = 15;
	
	private $_startingDay = 7;
	private $_firstNumber = 5;
	private $_secondNumber = 5;
	private $_thirdNumber = 5;
	private $_fourthNumber = 5;
	private $_endingDay = 6;
	
	private $_startingTime = '06:00:00';
	private $_endingTime = '06:00:00';
	
	private $_virtualCurrencyPlural = 'credits';
	
	private $_userid;
	private $_username;
	private $_credits;
	private $_luckyNumbers = array();
	
	private $_creditsDatabase = 'aion_ls';
	private $_creditsTable = 'account_data';
    private $_creditsColumn = 'toll';
    private $_useridColumn = 'memb_guid';
    private $_userIdentifier = 'userid';
	private $_checkOnline = true;
	
	private $_lotteryData = array();
	private $_totalTickets;
	private $_accumulatedJackpot;
	
	private $_weekDays = array(
		1 => "Monday",
		2 => "Tuesday",
		3 => "Wednesday",
		4 => "Thursday",
		5 => "Friday",
		6 => "Saturday",
		7 => "Sunday",
	);
	
	function __construct($lotteryId="") {
		if(!$this->_active) throw new Exception('The lottery is not currently active.');
		
		# Init Database
		$this->db = Handler::loadDB();
		
		# Load Lottery Data
		if(check($lotteryId)) {
			$this->_lotteryData = $this->db->queryFetchSingle("SELECT * FROM `lottery` WHERE `name` = ?", array($lotteryId));
		} else {
			$this->_lotteryData = $this->db->queryFetchSingle("SELECT * FROM `lottery` WHERE finished = 0", array());
		}
		
		# load initial jackpot from database
		$this->_initialJackpot = $this->_lotteryData['current_jackpot'];
		
		$this->calculateAccumulatedJackpot();
	}
	
	public function setUserid($input) {
		
		$this->_userid = $input;
		$this->loadUserData();
	}
	
	private function loadUserData() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		
		# Retrieve Account Information
		$accountInfo = $this->db->queryFetchSingle("SELECT * FROM `account_data` WHERE `id` = ?", array($this->_userid));
		if(!is_array($accountInfo)) throw new Exception('Could not load account information.');
		
		# Set Account Information
		$this->_username = $accountInfo['name'];
		$this->_credits = $accountInfo['toll'];
	}
	
	private function checkOnlineStatus() {
		
	}
	
	public function setLuckyNumber($input) {
		if(!Validator::UnsignedNumber($input)) throw new Exception('You entered an invalid number, please try again.');
		if(!Validator::Number($input, $this->_maxNumber+1, $this->_minNumber-1)) throw new Exception('You entered an invalid number, please try again.');
		if(in_array($input, $this->_luckyNumbers)) throw new Exception('lottery_error_7');
		
		$this->_luckyNumbers[] = $input;
	}
	
	public function ticketData() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		$check = $this->db->queryFetchSingle("SELECT * FROM `lottery_tickets` WHERE userid = ? AND `lottery_id` = ?", array($this->_userid, $this->_lotteryData['name']));
		if(is_array($check)) return $check;
		return false;
	}
	
	public function getTicketHistory() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		
		$query = "SELECT `lottery_tickets`.*, `lottery`.`finished` FROM `lottery_tickets` INNER JOIN `lottery` ON `lottery_tickets`.`lottery_id` = `lottery`.`name` WHERE `lottery_tickets`.`userid` = ? ORDER BY `lottery_tickets`.`id` DESC LIMIT 20";
		$result = $this->db->queryFetch($query, array($this->_userid));
		if(!is_array($result)) return;
		return $result;
	}
	
	private function getActiveTicketCount() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		
		$ticketHistory = $this->getTicketHistory();
		if(!is_array($ticketHistory)) return 0;
		
		$count = 0;
		foreach($ticketHistory as $ticket) {
			if($ticket['finished'] == 0) $count++;
		}
		
		return $count;
	}
	
	public function checkActiveTicketLimit() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		if($this->getActiveTicketCount() >= $this->_activeTicketLimit) throw new Exception("You have reached the limit of active tickets per lottery.");
	}
	
	public function buyTicket() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		if(count($this->_luckyNumbers) != 4) throw new Exception('You have not set your 4 lucky numbers.');
		
		# check if has enough credits
		$this->checkCredits();
		
		# deduct credits
		$this->deductCredits();
		
		# ticket data
		$ticketData = array(
			'lotteryid' => $this->_lotteryData['name'],
			'userid' => $this->_userid,
			'n1' => $this->_luckyNumbers[0],
			'n2' => $this->_luckyNumbers[1],
			'n3' => $this->_luckyNumbers[2],
			'n4' => $this->_luckyNumbers[3]
		);
		
		# add to ticket list
		$query = "INSERT INTO `lottery_tickets` (lottery_id, userid, buydate, number1, number2, number3, number4) VALUES (:lotteryid, :userid, now(), :n1, :n2, :n3, :n4)";
		$addTicket = $this->db->query($query, $ticketData);
		if(!$addTicket) throw new Exception('Could not add ticket, contact the administrator.');
	}
	
	private function checkCredits() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		if(!$this->_username) throw new Exception('Username not set.');
		
		if($this->_credits < $this->_ticketCost) throw new Exception('You don\'t have enough credits.');
	}
	
	private function deductCredits() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		if(!$this->_username) throw new Exception('Username not set.');
		
		$result = $this->db->query("UPDATE `account_data` SET `toll` = `toll` - ? WHERE `id` = ?", array($this->_ticketCost, $this->_userid));
		if(!$result) throw new Exception('Could not deduct credits.');
	}
	
	private function scheduleLottery() {
		$numbers = $this->generateRandomNumbers();
		$name = 'GNL' . rand(999,9999);
		$start = $this->generateTimestamp($this->_startingDay, $this->_startingTime);
		//$end = $this->generateTimestamp($this->_endingDay, $this->_endingTime, $start);
		$end = $start+518400;
		$insertData = array(
			$name,
			$this->_defaultInitialJackpot,
			1,
			$start,
			$end,
			$numbers[0],
			$numbers[1],
			$numbers[2],
			$numbers[3],
		);
		
		$query = "INSERT INTO `lottery` (name, current_jackpot, current_stage, start_timestamp, end_timestamp, number1, number2, number3, number4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
		$schedule = $this->db->query($query, $insertData);
		if(!$schedule) throw new Exception('Could not schedule lottery.');
		
	}
	
	private function generateTimestamp($day, $time, $timestamp="") {
		$dowText = date('D', strtotime("Sunday +$day days"));
		if($day == date("N")) {
			$scheduleDay = "today";
		} else {
			$scheduleDay = "next " . $dowText;
		}
		if($timestamp) {
			return strtotime("$scheduleDay $time", $timestamp);
		} else {
			return strtotime("$scheduleDay $time");
		}
	}
	
	/*
	private function generateTimestamp($day, $time, $timestamp="") {
		if($day == date("N")) {
			$scheduleDay = "today";
		} else {
			$scheduleDay = "next " . $this->_weekDays[$day];
		}
		if($timestamp) {
			return strtotime("$scheduleDay $time", $timestamp);
		} else {
			return strtotime("$scheduleDay $time");
		}
	}*/
	
	private function generateRandomNumbers() {
		$numbers = array();
		
		while(count($numbers) < 4) {
			$randomNumber = mt_rand($this->_minNumber, $this->_maxNumber);
			if(!in_array($randomNumber, $numbers)) {
				$numbers[] = $randomNumber;
			}
		}
		
		return $numbers;
	}
	
	private function updateCurrentStatus($status) {
		
		$update = $this->db->query("UPDATE `lottery` SET `current_stage` = ? WHERE `name` = ?", array($status, $this->_lotteryData['name']));
		if(!$update) throw new Exception('Could not update lottery status.');
	}
	
	/**
	 * Stages:
	 * 0	inactive / waiting to be scheduled
	 * 1	scheduled, waiting to start
	 * 2	started, in progress
	 * 3	first number revealed
	 * 4	second number revealed
	 * 5	third number revealed
	 * 6	fourth number revealed
	 * 7	finished, standby
	 */
	public function cronJob() {
		if(!$this->_active) return;
		switch($this->_lotteryData['current_stage']) {
			case 0:
				// Schedule Lottery
				$this->clearTickets();
				$this->scheduleLottery();
				break;
			case 7:
				// Schedule Lottery
				$this->clearTickets();
				$this->scheduleLottery();
				break;
			case 1:
				// Check Start Date and update current status
				if(time() >= $this->_lotteryData['start_timestamp']) {
					$this->updateCurrentStatus(2);
				}
				break;
			case 2:
				// Check if its time to reveal the first number
				if(time() >= $this->generateTimestamp($this->_firstNumber, "15:00:00", $this->_lotteryData['start_timestamp'])) {
					$this->updateCurrentStatus(3);
				}
				break;
			case 3:
				// Check if its time to reveal the second number
				if(time() >= $this->generateTimestamp($this->_secondNumber, "15:00:00", $this->_lotteryData['start_timestamp'])) {
					$this->updateCurrentStatus(4);
				}
				break;
			case 4:
				// Check if its time to reveal the third number
				if(time() >= $this->generateTimestamp($this->_thirdNumber, "15:00:00", $this->_lotteryData['start_timestamp'])) {
					$this->updateCurrentStatus(5);
				}
				break;
			case 5:
				// Check if its time to reveal the fourth number
				if(time() >= $this->generateTimestamp($this->_fourthNumber, "15:00:00", $this->_lotteryData['start_timestamp'])) {
					$this->updateCurrentStatus(6);
				}
				break;
			case 6:
				// get winners
				debug("stage 6");
				$winners = $this->getWinners();
				if(is_array($winners['matches'])) {
					# get results
					$match[1] = (array_key_exists(1, $winners['matches']) ? count($winners['matches'][1]) : 0);
					$match[2] = (array_key_exists(2, $winners['matches']) ? count($winners['matches'][2]) : 0);
					$match[3] = (array_key_exists(3, $winners['matches']) ? count($winners['matches'][3]) : 0);
					$match[4] = (array_key_exists(4, $winners['matches']) ? count($winners['matches'][4]) : 0);
					
					# update lottery results
					if($this->_lotteryData['winners_1'] == 0 && $this->_lotteryData['winners_2'] == 0 && $this->_lotteryData['winners_3'] == 0 && $this->_lotteryData['winners_4'] == 0) {
						$this->db->query("UPDATE `lottery` SET `winners_1` = ?, `winners_2` = ?, `winners_3` = ?, `winners_4` = ? WHERE `name` = ?", array($match[1], $match[2], $match[3], $match[4], $this->_lotteryData['name']));
					}
				}
				
				// Check if lottery ended
				if(time() >= $this->_lotteryData['end_timestamp']) {
					
					# reward winners
					if(is_array($winners['matches'])) {
						foreach($winners['matches'] as $matches => $userindex) {
							
							$reward = $this->_accumulatedJackpot*($this->getMatchWinPercent($matches))/100;
							$reward = floor($reward/$match[$matches]);
							
							foreach($userindex as $index) {
								
								$this->rewardUser($winners['users'][$index], $reward);
								debug($winners['users'][$index] . ' - ' . $reward);
							}
						}
					}
					
					$this->updateCurrentStatus(7);
				}
				break;
			default:
				return;
		}
	}
	
	private function calculateAccumulatedJackpot() {
		$result = $this->db->queryFetchSingle("SELECT COUNT(*) as totalTickets FROM `lottery_tickets` WHERE `lottery_id` = ?", array($this->_lotteryData['name']));
		$this->_totalTickets = $result['totalTickets'];
		
		# Calculate accumulated jackpot
		if($this->_ticketProfit < 100) {
			$ticketMultiplier = (100-$this->_ticketProfit)/100;
			$this->_accumulatedJackpot = floor((($this->_totalTickets*$this->_ticketCost)*$ticketMultiplier)+$this->_initialJackpot);
		}
	}
	
	public function getJackpot() {
		if($this->_lotteryData['current_stage'] < 2) {
			return $this->_initialJackpot;
		} else {
			return $this->_accumulatedJackpot;
		}
	}
	
	public function showLuckyNumber($number) {
		switch($number) {
			case 1:
				if($this->_lotteryData['current_stage'] >= 3) return $this->_lotteryData['number1'];
				return "?";
			case 2:
				if($this->_lotteryData['current_stage'] >= 4) return $this->_lotteryData['number2'];
				return "?";
			case 3:
				if($this->_lotteryData['current_stage'] >= 5) return $this->_lotteryData['number3'];
				return "?";
			case 4:
				if($this->_lotteryData['current_stage'] >= 6) return $this->_lotteryData['number4'];
				return "?";
			default:
				return "?";
		}
	}
	
	public function getStartDate() {
		return date("F j, Y H:i", $this->_lotteryData['start_timestamp']);
	}
	
	public function getEndDate() {
		return date("F j, Y H:i", $this->_lotteryData['end_timestamp']);
	}
	
	public function getTicketCost() {
		return $this->_ticketCost;
	}
	
	public function getMinNumber() {
		return $this->_minNumber;
	}
	
	public function getMaxNumber() {
		return $this->_maxNumber;
	}
	
	public function getCurrencyName() {
		return $this->_virtualCurrencyPlural;
	}
	
	public function getMatchWinPercent($matches) {
		switch($matches) {
			case 1:
				return $this->_matches1;
			case 2:
				return $this->_matches2;
			case 3:
				return $this->_matches3;
			case 4:
				return $this->_matches4;
			default:
				return 0;
		}
	}
	
	public function getCurrentStage() {
		return $this->_lotteryData['current_stage'];
	}
	
	public function canBuyTicket() {
		if($this->_lotteryData['current_stage'] == 2) return true;
		return false;
	}
	
	private function rewardUser($userid, $credits) {
		$check = $this->db->queryFetchSingle("SELECT * FROM `lottery_stash` WHERE `userid` = ?", array($userid));
		if(is_array($check)) {
			# update
			$this->db->query("UPDATE `lottery_stash` SET `credits` = `credits` + ? WHERE `userid` = ?", array($credits, $userid));
		} else {
			# insert
			$this->db->query("INSERT INTO `lottery_stash` (userid, credits) VALUES (?, ?)", array($userid, $credits));
		}
	}
	
	private function loadTickets() {
		return $this->db->queryFetch("SELECT * FROM `lottery_tickets` WHERE `lottery_id` = ?", array($this->_lotteryData['name']));
	}
	
	public function getWinners() {
		$tickets = $this->loadTickets();
		$luckyNumbers = array(
			$this->_lotteryData['number1'],
			$this->_lotteryData['number2'],
			$this->_lotteryData['number3'],
			$this->_lotteryData['number4']
		);
		if(is_array($tickets)) {
			$ticketsArray = array();
			$usersArray = array();
			foreach($tickets as $ticket) {
				$usersArray[] = $ticket['userid'];
				$ticketsArray[] = array(
					$ticket['number1'],
					$ticket['number2'],
					$ticket['number3'],
					$ticket['number4']
				);
			}
			
			foreach($ticketsArray as $key => $ticket) {
				$matches = array_intersect($luckyNumbers, $ticket);
				$count = count($matches);
				if($count >= 1) {
					$results[$count][] = $key;
				}
			}
			
			$returnResults = array();
			$returnResults['users'] = $usersArray;
			$returnResults['matches'] = $results;
			return $returnResults;
		}
	}
	
	private function clearTickets() {
		//$this->muonline->query("DELETE FROM WEBENGINE_LOTTERY_TICKETS");
		$this->db->query("UPDATE `lottery` SET `finished` = 1 WHERE `name` = ?", array($this->_lotteryData['name']));
	}
	
	public function getResults($matches) {
		switch($matches) {
			case 1:
				return $this->_lotteryData['winners_1'];
				break;
			case 2:
				return $this->_lotteryData['winners_2'];
				break;
			case 3:
				return $this->_lotteryData['winners_3'];
				break;
			case 4:
				return $this->_lotteryData['winners_4'];
				break;
		}
	}
	
	public function getWeekDays() {
		return $this->_weekDays;
	}
	
	public function getStashInfo() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		$result = $this->db->queryFetchSingle("SELECT * FROM `lottery_stash` WHERE `userid` = ?", array($this->_userid));
		if(is_array($result)) return $result;
		return 0;
	}
	
	public function transferCredits() {
		if(!$this->_userid) throw new Exception('User ID not set.');
		$userStash = $this->getStashInfo();
		if(!is_array($userStash)) throw new Exception('You don\'t have enough credits in your stash.');
		
		if($userStash['locked'] == 1) throw new Exception('Your stash is locked');
		if($userStash['credits'] < 1) throw new Exception('You don\'t have enough credits in your stash.');
		
		// transfer credits
		$transfer = $this->db->query("UPDATE `account_data` SET `toll` = `toll` + ? WHERE `id` = ?", array($userStash['credits'], $this->_userid));
		if(!$transfer) throw new Exception('We could not transfer your credits, please contact support.');
		
		// update stash
		$updateStash = $this->db->query("UPDATE `lottery_stash` SET `credits` = 0 WHERE `userid` = ?", array($this->_userid));
		//if(!$updateStash) throw new Exception('ERROR L30');
		
		// add log
		$this->addLog($userStash['credits']);
	}
	
	private function addLog($credits) {
		if(!$this->_userid) throw new Exception('User ID not set.');
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$addLog = $this->db->query("INSERT INTO `lottery_stash_log` (userid, credits, timestamp, ip_address) VALUES (?, ?, now(), ?)", array($this->_userid, $credits, $ip));
	}
	
	private function getLastLotteryData() {
		$result = $this->db->queryFetchSingle("SELECT * FROM `lottery` WHERE `finished` = 1 ORDER BY `id` DESC", array());
		if(!is_array($result)) return;
		return $result;
	}
	
	public function displayResults() {
		$lastLottery = $this->getLastLotteryData();
		if(!is_array($lastLottery)) throw new Exception('No data is available.');
		
		echo '<h3>'.date("jS M", $lastLottery['start_timestamp']).' Lottery Results<br /><br />( <span style="color:#4d91dc;">'.$lastLottery['number1'].'</span> <span style="color:#abdd47;">'.$lastLottery['number2'].'</span> <span style="color:#dba649;">'.$lastLottery['number3'].'</span> <span style="color:#eaeaea;">'.$lastLottery['number4'].'</span> )</h3>';
		echo '<table class="lottery-tickethistory">';
			echo '<tr>';
				echo '<th>4 Matches</th>';
				echo '<th>3 Matches</th>';
				echo '<th>2 Matches</th>';
				echo '<th>1 Match</th>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>'.$lastLottery['winners_4'].'</td>';
				echo '<td>'.$lastLottery['winners_3'].'</td>';
				echo '<td>'.$lastLottery['winners_2'].'</td>';
				echo '<td>'.$lastLottery['winners_1'].'</td>';
			echo '</tr>';
		echo '</table>';
		echo '<div style="font-family:Arial, Helvetica, sans-serif;font-size:11px;color:#aaa;text-align:left;padding:20px;">* The results displayed are the amount of winning tickets.</div>';
	}
	
	public function getLotteryData($column="") {
		if(!check($column)) return $this->_lotteryData;
		return $this->_lotteryData[$column];
	}
	
	/*
	public function tracker($results = array()) {
		$winners = $this->getWinners();
		if(!is_array($winners)) return;
		
		$accumulatedJackpot = $this->_initialJackpot + ($results['tickets'] * 40);
		
		echo '<div style="height: 10px;margin: 20px;border-bottom: 1px dashed #aaa;"></div>';
		
		echo '<ul>';
			echo '<li>[A] Jackpot: <strong>'.$accumulatedJackpot.'</strong></li>';
			echo '<li>[T] Jackpot: <strong>'.($accumulatedJackpot-$this->getLotteryData('current_jackpot')).'</strong></li>';
			echo '<li>Tickets Profit: <strong>'.($results['tickets']*10).'</strong></li>';
		echo '</ul>';
		
		echo '<div style="height: 10px;margin: 20px;border-bottom: 1px dashed #aaa;"></div>';
		
		$m4 = count($winners['matches'][4]);
		$m3 = count($winners['matches'][3]);
		$m2 = count($winners['matches'][2]);
		$m1 = count($winners['matches'][1]);
		
		$em4 = ($m4 >= 1 ? floor(($accumulatedJackpot*($this->getMatchWinPercent(4))/100)/$m4) : 0);
		$em3 = ($m3 >= 1 ? floor(($accumulatedJackpot*($this->getMatchWinPercent(3))/100)/$m3) : 0);
		$em2 = ($m2 >= 1 ? floor(($accumulatedJackpot*($this->getMatchWinPercent(2))/100)/$m2) : 0);
		$em1 = ($m1 >= 1 ? floor(($accumulatedJackpot*($this->getMatchWinPercent(1))/100)/$m1) : 0);
		
		$t4 = $m4 * $em4;
		$t3 = $m3 * $em3;
		$t2 = $m2 * $em2;
		$t1 = $m1 * $em1;
		
		echo '<h4>Winners so far:</h4>';
		echo '<ul>';
			echo '<li>4 matches: ' . $m4 . ' ('.$em4.' each) ['.$t4.']</li>';
			echo '<li>3 matches: ' . $m3 . ' ('.$em3.' each) ['.$t3.']</li>';
			echo '<li>2 matches: ' . $m2 . ' ('.$em2.' each) ['.$t2.']</li>';
			echo '<li>1 match: ' . $m1 . ' ('.$em1.' each) ['.$t1.']</li>';
			echo '<li>TOTAL: <strong>'.($t4 + $t3 + $t2 + $t1).'</strong></li>';
		echo '</ul>';
		
		echo '<div style="height: 10px;margin: 20px;border-bottom: 1px dashed #aaa;"></div>';
		
		$query = "SELECT `account_data`.`name`, COUNT(`lottery_tickets`.`userid`) AS `tickets` FROM `lottery_tickets` INNER JOIN `account_data` ON `lottery_tickets`.`userid` = `account_data`.`id` WHERE `lottery_tickets`.`lottery_id` = ? GROUP BY `account_data`.`name`";
		$result = $this->db->queryFetch($query, array($this->getLotteryData('name')));
		if(is_array($result)) {
			
			echo '<table style="width: 200px;">';
				echo '<tr>';
					echo '<td><strong>name</strong></td>';
					echo '<td><strong>tickets</strong></td>';
				echo '</tr>';
				foreach($result as $row) {
					echo '<tr>';
						echo '<td>'.$row['name'].'</td>';
						echo '<td>'.$row['tickets'].'</td>';
					echo '</tr>';
				}
			echo '</table>';
		}
	}
	*/

}