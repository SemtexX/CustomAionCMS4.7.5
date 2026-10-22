<?php
// Am Anfang jeder Seite
include('countdown_timer.php');
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
			

			//echo '<div class="lottery-head"></div>';
			echo '<div style="text-align:center;padding: 30px 0px;">';
				echo '<span style="font-size: 45px;color: #ffc000;">GlobalXcentra</span><br />';
				echo '<span style="font-size: 24px;">CUSTOM_LOTTERY</span>';
			echo '</div>';
			echo '<br /><br />';
			
			echo '<div class="lottery-jackpot">';
				echo 'JACKPOT<br /><br /><span>'.number_format($lottery->getJackpot(), 0).'</span><br />' . $lottery->getCurrencyName();
			echo '</div>';
			
			echo '<br /><br />';
			echo '<div class="lottery-balls">';
				echo '<div class="lottery-ball-number">'.$lottery->showLuckyNumber(1).'</div>';
				echo '<div class="lottery-ball-number">'.$lottery->showLuckyNumber(2).'</div>';
				echo '<div class="lottery-ball-number">'.$lottery->showLuckyNumber(3).'</div>';
				echo '<div class="lottery-ball-number">'.$lottery->showLuckyNumber(4).'</div>';
			echo '</div>';
			echo '<br /><br />';
			
			echo '<div style="text-align:center;">';
				echo '<div class="lottery-timeleft">';
					echo '<span>Lottery Start</span><br />';
					echo $lottery->getStartDate();
					echo '<br /><br />';
					echo '<span>Lottery End</span><br />';
					echo $lottery->getEndDate();
				echo '</div>';
			echo '</div>';
			
			echo '<br /><br />';
			echo '<div class="lottery-buyticket">';
				echo '<a href="'.module_url('lottery/buy/', true).'">[buy ticket]</a>';
			echo '</div>';
			
			echo '<br /><br />';
			echo '<br /><br />';
					
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