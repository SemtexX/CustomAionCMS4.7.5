<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="lottery-container">
	<?php
		
		try {
			
			$db = Handler::loadDB();
			
			$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_SESSION['username']));
			if(!is_array($accountData)) { die('x'); }
			
			$lottery = new LotterySystem();
			$lottery->setUserid($accountData['id']);
			
			
			echo '<ul class="lottery-menu">';
				echo '<li><a href="'.module_url('lottery/', true).'">Lottery Home</a></li>';
				echo '<li><a href="'.module_url('lottery/tickets/', true).'">My Tickets</a></li>';
				echo '<li><a href="'.module_url('lottery/stash/', true).'">My Stash</a></li>';
				echo '<li><a href="'.module_url('lottery/results/', true).'">Results</a></li>';
				echo '<li><a href="https://discord.gg/4nwpdqJ4MM" target="_blank">Help</a></li>';
			echo '</ul>';
			

			try {
				$ticketHistory = $lottery->getTicketHistory();
				if(!is_array($ticketHistory)) throw new Exception("You have not purchased any tickets.");
				
				echo '<h3 style="padding-left: 15px;">Ticket History (last 20):</h3><br />';
				echo '<div class="lottery-tickethistory">';
					echo '<table>';
						echo '<tr>';
							echo '<th>Purchase Date</th>';
							echo '<th>First Number</th>';
							echo '<th>Second Number</th>';
							echo '<th>Third Number</th>';
							echo '<th>Fourth Number</th>';
						echo '</tr>';
						foreach($ticketHistory as $ticket) {
							if($ticket['finished'] == 0) {
								echo '<tr class="active">';
							} else {
								echo '<tr>';
							}
								echo '<td>'.$ticket['buydate'].'</td>';
								echo '<td>'.$ticket['number1'].'</td>';
								echo '<td>'.$ticket['number2'].'</td>';
								echo '<td>'.$ticket['number3'].'</td>';
								echo '<td>'.$ticket['number4'].'</td>';
							echo '</tr>';
						}
					echo '</table>';
				echo '</div>';
			} catch(Exception $ex) {
				echo '<div class="lottery-error">'.$ex->getMessage().'</div>';
			}

		} catch(Exception $ex) {
			echo '<div class="lottery-error">'.$ex->getMessage().'</div>';
		}
	?>
</div>
<div class="lottery-conditions">
	<strong>CONDITIONS:</strong>
	<ul>
		<li>Ticket sale is limited to up to 5 active tickets per lottery (weekly).</li>
		<li>You may not use multiple accounts to purchase over 5 tickets.</li>
		<li>You may withdraw your credits anytime.</li>
		<li>Exploiting the system in any way will lead to a permanent ban.</li>
	</ul>
	<br /><br />
	Server Time: <?php echo date("F j, Y H:i"); ?>
</div>