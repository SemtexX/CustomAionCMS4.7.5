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

<?php
try {
	
	// ticket object
	$Tickets = new Tickets();
	$Tickets->setId($_GET['id']);
	
	// load ticket data
	$ticketData = $Tickets->getTicketData();
	if(!is_array($ticketData)) throw new Exception('We could not find the ticket you requested.');
	if($ticketData['username'] != $_SESSION['username']) throw new Exception('We could not find the ticket you requested.');
	
	// ticket reply
	if($ticketData['closed'] != 1) {
		if(check($_POST['reply_submit'])) {
			try {
				$TicketReply = new Tickets();
				$TicketReply->setId($ticketData['id']);
				$TicketReply->setUsername($ticketData['username']);
				$TicketReply->setMessage($_POST['reply_message']);
				$TicketReply->submitMessage();
				redirect('tickets/view/id/' . $ticketData['id']);
				
			} catch(Exception $ex) {
				message($ex->getMessage(), 'error');
			}
		}
	}
	
	// load messages
	$ticketMessages = $Tickets->getTicketMessages();
	if(!is_array($ticketMessages)) throw new Exception('We could not load your ticket messages, try again later.');
	
	// ticket status
	if($ticketData['closed'] == 1) {
		message('This ticket status is <strong>closed</strong>, if you need further support please submit a new ticket.', 'warning');
	}
	
	// title
	echo '<h2>#'.$ticketData['id'].' '.$ticketData['subject'].'</h2>';
	echo '<hr>';
	
	// messages
	foreach($ticketMessages as $ticketMessage) {
		if($ticketMessage['username'] != $_SESSION['username']) {
			echo '<div class="panel panel-danger">';
		} else {
			echo '<div class="panel panel-default">';
		}
			echo '<div class="panel-heading"><strong>'.$ticketMessage['username'].':</strong><span class="pull-right text-muted small">'.$ticketMessage['create_date'].'</span></div>';
			echo '<div class="panel-body">';
				echo '<p style="word-wrap:break-word;">';
					echo nl2br(htmlspecialchars($ticketMessage['message']));
				echo '</p>';
			echo '</div>';
		echo '</div>';
	}
	
	// reply form
	if($ticketData['closed'] != 1) {
		echo '<hr>';
		echo '<form action="" method="post">';
			echo '<div class="form-group">';
				echo '<label for="t2">Message</label>';
				echo '<textarea class="form-control" id="t2" style="height:150px;" name="reply_message" required></textarea>';
			echo '</div>';
			echo '<button type="submit" name="reply_submit" value="submit" class="btn btn-primary">Submit</button> ';
			echo '<a href="'.__BASE_URL__.'page/tickets/list" class="btn btn-secondary">Cancel</a>';
		echo '</form>';
	}
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>