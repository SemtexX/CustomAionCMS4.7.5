<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	
	if(check($_GET['unban'])) {
		try {
			$TicketsUnban = new Tickets();
			$TicketsUnban->setUsername($_GET['unban']);
			$TicketsUnban->unbanFromTicketSystem();
			message('Successfully unbanned, the account can now use the ticket system again!', 'success');
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	if(check($_POST['ban_submit'], $_POST['ban_account'])) {
		try {
			$TicketsBan = new Tickets();
			$TicketsBan->setUsername($_POST['ban_account']);
			$TicketsBan->banFromTicketSystem();
			message('Successfully banned!', 'success');
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	$Tickets = new Tickets();
	$bannedAccounts = $Tickets->getBannedAccountsList();

	echo '<div class="row">';
		echo '<div class="col-md-8">';
			echo '<div class="block">';
				echo '<div class="block-content">';
				if(is_array($bannedAccounts)) {
					echo '<table class="table table-striped table-hover">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Username</th>';
								echo '<th></th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
							foreach($bannedAccounts as $row) {
								echo '<tr>';
									echo '<td>'.$row['username'].'</td>';
									echo '<td class="text-right">';
										echo '<a href="'.__BASE_URL__.'tickets/banned/unban/'.$row['username'].'" class="btn btn-sm btn-primary">unban</a>';
									echo '</td>';
								echo '</tr>';
							}
						echo '</tbody>';
					echo '</table>';
				} else {
					message('There are no banned accounts from using the ticket system.', 'warning');
				}
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
		echo '<div class="col-md-4">';
			echo '<div class="block">';
				echo '<div class="block-content">';
					
					echo '<form action="'.__BASE_URL__.'tickets/banned/" method="post">';
						echo '<div class="form-group">';
							echo '<input type="text" name="ban_account" class="form-control" placeholder="Username...">';
						echo '</div>';
						echo '<button type="submit" class="btn btn-danger" name="ban_submit" value="ok">Ban From Ticket System</button>';
					echo '</form><br />';
				
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
	echo '</div>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}