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
				$lottery->displayResults();
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