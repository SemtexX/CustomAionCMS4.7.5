<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block support"></div>
<br /><br />

<h3>My Support Tickets</h3>
<br /><br />

<?php
echo '<a href="'.__BASE_URL__.'page/tickets/new/" class="btn btn-sm btn-info">Submit New Ticket</a>';
echo '<hr>';

try {
	
	$Tickets = new Tickets();
	$Tickets->setUsername($_SESSION['username']);
	
	$ticketsList = $Tickets->getAccountTickets();
	if(!is_array($ticketsList)) throw new Exception('You don\'t have any tickets.');
	
	echo '<table class="table">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Id</th>';
				echo '<th>Subject</th>';
				echo '<th>Last Message</th>';
				echo '<th>Last Message Date</th>';
				echo '<th>Status</th>';
				echo '<th></th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			foreach($ticketsList as $ticketData) {
				$status = $ticketData['closed'] == 1 ? '<span class="label label-danger">Closed</span>' : '<span class="label label-success">Open</span>';
				
				echo '<tr>';
					echo '<td>#'.$ticketData['id'].'</td>';
					echo '<td>'.$ticketData['subject'].'</td>';
					echo '<td>'.$ticketData['last_reply_by'].'</td>';
					echo '<td>'.$ticketData['last_reply_date'].'</td>';
					echo '<td>'.$status.'</td>';
					echo '<td class="text-right">';
						echo '<a href="'.__BASE_URL__.'page/tickets/view/id/'.$ticketData['id'].'" class="btn btn-sm btn-primary">View</a>';
					echo '</td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>