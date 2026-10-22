<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


try {
	$lotteryName = (check($_GET['id']) ? $_GET['id'] : '');
	$Lottery = new LotterySystem($lotteryName);
	
	$results = $db->queryFetchSingle("SELECT COUNT(DISTINCT(`userid`)) AS users, COUNT(*) AS tickets FROM `lottery_tickets` WHERE `lottery_id` = ?", array($Lottery->getLotteryData('name')));	
	
	$lotteryName = $Lottery->getLotteryData('name');
	$lotteryStage = $Lottery->getLotteryData('current_stage');
	$totalUsers = $results['users'];
	$totalTickets = $results['tickets'];
	
	$initialJackpot = $Lottery->getLotteryData('current_jackpot');
	$accumulatedJackpot = $Lottery->getJackpot();
	$ticketsJackpot = $accumulatedJackpot-$initialJackpot;
	
	$ticketCost = $Lottery->_ticketCost;
	$ticketProfit = $Lottery->_ticketProfit;
	$totalTicketsProfit = $totalTickets*$ticketCost*($ticketProfit/100);
	
	$winners = $Lottery->getWinners();
	
	$m4 = count($winners['matches'][4]);
	$m3 = count($winners['matches'][3]);
	$m2 = count($winners['matches'][2]);
	$m1 = count($winners['matches'][1]);
	
	$em4 = ($m4 >= 1 ? floor(($accumulatedJackpot*($Lottery->getMatchWinPercent(4))/100)/$m4) : 0);
	$em3 = ($m3 >= 1 ? floor(($accumulatedJackpot*($Lottery->getMatchWinPercent(3))/100)/$m3) : 0);
	$em2 = ($m2 >= 1 ? floor(($accumulatedJackpot*($Lottery->getMatchWinPercent(2))/100)/$m2) : 0);
	$em1 = ($m1 >= 1 ? floor(($accumulatedJackpot*($Lottery->getMatchWinPercent(1))/100)/$m1) : 0);
	
	$t4 = $m4 * $em4;
	$t3 = $m3 * $em3;
	$t2 = $m2 * $em2;
	$t1 = $m1 * $em1;
	
	$totalGiven = ($t4+$t3+$t2+$t1);
	
	
	
	echo '<div class="row">';
		
		# lottery info
		echo '<div class="col-md-4">';
			echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading">General Info</div>';
				echo '<div class="panel-body">';
					echo '<table class="table">';
						echo '<tbody>';
							echo '<tr>';
								echo '<td>Lottery ID</td>';
								echo '<td style="color:red;font-weight:bold;" class="text-right">'.$lotteryName.'</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Users</td>';
								echo '<td class="text-right">'.$totalUsers.'</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Tickets</td>';
								echo '<td class="text-right">'.$totalTickets.'</td>';
							echo '</tr>';
							echo '<tr class="success">';
								echo '<td>Profit</td>';
								echo '<td class="text-right" style="font-weight:bold;">'.number_format($ticketsJackpot+$totalTicketsProfit-$totalGiven).'</td>';
							echo '</tr>';
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
		# jackpot info
		echo '<div class="col-md-4">';
			echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading">Jackpot Info</div>';
				echo '<div class="panel-body">';
					echo '<table class="table">';
						echo '<tbody>';
							echo '<tr>';
								echo '<td>Initial Jackpot</td>';
								echo '<td class="text-right">'.number_format($initialJackpot).'</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Accumulated Jackpot</td>';
								echo '<td class="text-right">'.number_format($accumulatedJackpot).'</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Tickets Jackpot</td>';
								echo '<td class="text-right">'.number_format($ticketsJackpot).'</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Tickets Profit</td>';
								echo '<td class="text-right">'.number_format($totalTicketsProfit).'</td>';
							echo '</tr>';
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
		# winners info
		echo '<div class="col-md-4">';
			echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading">Results</div>';
				echo '<div class="panel-body">';
					echo '<table class="table">';
						echo '<tbody>';
							echo '<tr>';
								echo '<td>4 Matches</td>';
								echo '<td class="text-right"><strong>'.$m4.'</strong> ('.number_format($em4).' each) ['.number_format($t4).']</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>3 Matches</td>';
								echo '<td class="text-right"><strong>'.$m3.'</strong> ('.number_format($em3).' each) ['.number_format($t3).']</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>2 Matches</td>';
								echo '<td class="text-right"><strong>'.$m2.'</strong> ('.number_format($em2).' each) ['.number_format($t2).']</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>1 Match</td>';
								echo '<td class="text-right"><strong>'.$m1.'</strong> ('.number_format($em1).' each) ['.number_format($t1).']</td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>TOTAL</td>';
								echo '<td class="text-right">'.number_format($totalGiven).'</td>';
							echo '</tr>';
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
		
	echo '</div>';
	
	
	# --------------------------
	
	
	echo '<div class="row">';
		
		# users
		
		$usersTickets = $db->queryFetch("SELECT `account_data`.`name`, COUNT(`lottery_tickets`.`userid`) AS `tickets` FROM `lottery_tickets` INNER JOIN `account_data` ON `lottery_tickets`.`userid` = `account_data`.`id` WHERE `lottery_tickets`.`lottery_id` = ? GROUP BY `account_data`.`name`", array($lotteryName));
		
		echo '<div class="col-md-4">';
			echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading">Users</div>';
				echo '<div class="panel-body">';
				if(is_array($usersTickets)) {
					echo '<table class="table table-condensed table-hover">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Account Id</th>';
								echo '<th>Tickets</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($usersTickets as $userTicket) {
							echo '<tr>';
								echo '<td>'.$userTicket['name'].'</td>';
								echo '<td>'.$userTicket['tickets'].'</td>';
							echo '</tr>';
						}
						echo '</tbody>';
					echo '</table>';
				} else {
					message('No tickets purchased yet.', 'warning');
				}
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
		# tickets
		
		$tickets = $db->queryFetch("SELECT `account_data`.`name`, `lottery_tickets`.`id`, `lottery_tickets`.`lottery_id`, `lottery_tickets`.`userid`, `lottery_tickets`.`buydate`, `lottery_tickets`.`number1`, `lottery_tickets`.`number2`, `lottery_tickets`.`number3`, `lottery_tickets`.`number4` FROM `lottery_tickets` INNER JOIN `account_data` ON `account_data`.`id` = `lottery_tickets`.`userid` WHERE `lottery_tickets`.`lottery_id` = ?", array($lotteryName));
		$luckyNumbers = array(
			$Lottery->getLotteryData('number1'),
			$Lottery->getLotteryData('number2'),
			$Lottery->getLotteryData('number3'),
			$Lottery->getLotteryData('number4')
		);
		if(is_array($tickets)) {
			$ticketsArray = array();
			foreach($tickets as $ticket) {
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
					$matchResults[$count][] = $key;
				}
			}
		}
		
		echo '<div class="col-md-8">';
			echo '<div class="panel panel-primary">';
				echo '<div class="panel-heading">Tickets</div>';
				echo '<div class="panel-body">';
				if(is_array($tickets)) {
					echo '<table class="table table-condensed table-hover">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Account Id</th>';
								echo '<th>Date Purchased</th>';
								echo '<th colspan="4">Numbers</th>';
								echo '<th class="text-center">Matches</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($tickets as $tindex => $ticketData) {
							
							$ticketMatches = "";
							
							if(is_array($matchResults[1])) if(in_array($tindex, $matchResults[1])) $ticketMatches = 1;
							if(is_array($matchResults[2])) if(in_array($tindex, $matchResults[2])) $ticketMatches = 2;
							if(is_array($matchResults[3])) if(in_array($tindex, $matchResults[3])) $ticketMatches = 3;
							if(is_array($matchResults[4])) if(in_array($tindex, $matchResults[4])) $ticketMatches = 4;
					
							
							if($ticketMatches == 3) {
								echo '<tr class="warning">';
							} elseif($ticketMatches == 4) {
								echo '<tr class="success">';
							} else {
								echo '<tr>';
							}
								echo '<td>'.$ticketData['name'].'</td>';
								echo '<td>'.$ticketData['buydate'].'</td>';
								echo '<td>'.$ticketData['number1'].'</td>';
								echo '<td>'.$ticketData['number2'].'</td>';
								echo '<td>'.$ticketData['number3'].'</td>';
								echo '<td>'.$ticketData['number4'].'</td>';
								echo '<td class="text-center">'.$ticketMatches.'</td>';
							echo '</tr>';
						}
						echo '</tbody>';
					echo '</table>';
				} else {
					message('No tickets purchased yet.', 'warning');
				}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	
	echo '</div>';
	
	
} catch (Exception $ex) {
	message($ex->getMessage(), 'error');
}