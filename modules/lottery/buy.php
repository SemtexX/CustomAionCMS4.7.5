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
			
			// BUY TICKET
			
			try {
				if(!$lottery->canBuyTicket()) throw new Exception('Ticket sales are closed, please try again when the lottery starts.');
				$lottery->checkActiveTicketLimit();
				if(check($_POST['buyticket_submit'], $_POST['buyticket_numbers'])) {
					$ticketNumbers = explode(",", $_POST['buyticket_numbers']);
					if(!is_array($ticketNumbers)) throw new Exception('Please select 4 numbers.');
					if(count($ticketNumbers) != 4) throw new Exception('Please select 4 numbers.');
					
					$lottery->setLuckyNumber($ticketNumbers[0]);
					$lottery->setLuckyNumber($ticketNumbers[1]);
					$lottery->setLuckyNumber($ticketNumbers[2]);
					$lottery->setLuckyNumber($ticketNumbers[3]);
					$lottery->buyTicket();
					
					logSystem::add('purchased lottery tickets');
					
					//echo '<div class="lottery-success">Ticket successfully bought! Good luck!<br />and May the Force be with you!</div>';
					redirect('lottery/tickets/');

				}
				
				
			} catch(Exception $ex) {
				echo '<div class="lottery-error">'.$ex->getMessage().'</div>';
			}
			?>
			<script>
			window.onload = function() {
				var ul = document.getElementById('ticketnumbers');
				var sNumbers = [];

				ul.addEventListener('click', function(e) {
					if (e.target.tagName === 'LI') {
						if(document.getElementById(e.target.id).className == "lottery-selected-number") {
							document.getElementById(e.target.id).className = "";
							sNumbers.splice(sNumbers.indexOf(e.target.id), 1);
						} else {
							if(sNumbers.length < 4) {
								document.getElementById(e.target.id).className = "lottery-selected-number";
								sNumbers.push(e.target.id);
							}
						}
						document.getElementById("buyticket_numbers").value = sNumbers;
					}
				});
			}
			</script>
			
			<form action="" method="post">
				<input type="hidden" id="buyticket_numbers" name="buyticket_numbers"/>
				<div class="lottery-ticket">
					<span class="title">GlobalXcentra</span>
					<p>SELECT 4 NUMBERS</p>
					<ul id="ticketnumbers">
						<?php
						for($i=$lottery->getMinNumber(); $i<=$lottery->getMaxNumber(); $i++) {
							echo '<li id="'.$i.'">'.$i.'</li>';
						}
						?>
					</ul>
					<button class="lottery-buyticket-submit" type="submit" name="buyticket_submit" value="ok">BUY</button>
				</div>
			</form>
			<div style="padding:10px;">cost<br /><?php echo $lottery->getTicketCost(); ?> credits</div>
			<?php

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