<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	
	$Tickets = new Tickets();
	$ticketsList = $Tickets->getClosedTickets();
	if(!is_array($ticketsList)) throw new Exception('There are no closed tickets.');
	
	// number_format(count($ticketsList))

	echo '<table class="table table-striped table-hover">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Ticket Id</th>';
				echo '<th>Subject</th>';
				echo '<th>Account</th>';
				echo '<th>Date Created</th>';
				echo '<th>Last Message By</th>';
				echo '<th>Last Message Date</th>';
				echo '<th></th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			foreach($ticketsList as $ticketData) {
				
				echo '<tr>';
					echo '<td>#'.$ticketData['id'].'</td>';
					echo '<td>'.$ticketData['subject'].'</td>';
					echo '<td>'.$ticketData['username'].'</td>';
					echo '<td>'.$ticketData['create_date'].'</td>';
					echo '<td>'.$ticketData['last_reply_by'].'</td>';
					echo '<td>'.$ticketData['last_reply_date'].'</td>';
					echo '<td class="text-right">';
						echo '<a href="'.__BASE_URL__.'tickets/view/id/'.$ticketData['id'].'" class="btn btn-sm btn-primary">View</a>';
					echo '</td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';

	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}