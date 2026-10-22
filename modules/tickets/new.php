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

<h3>Submit New Ticket</h3>
<br /><br />

<?php
if(check($_POST['submit'])) {
	try {
		
		$Tickets = new Tickets();
		$Tickets->setSubject($_POST['ticket_subject']);
		$Tickets->setMessage($_POST['ticket_message']);
		$Tickets->setUsername($_SESSION['username']);
		$Tickets->submitTicket();
		$Tickets->redirectToTicket();
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<form action="" method="post">';
	echo '<div class="form-group">';
		echo '<label for="t1">Subject</label>';
		echo '<input type="text" class="form-control" id="t1" name="ticket_subject" required autofocus>';
	echo '</div>';
	echo '<div class="form-group">';
		echo '<label for="t2">Message</label>';
		echo '<textarea class="form-control" id="t2" style="height:250px;" name="ticket_message" required></textarea>';
	echo '</div>';
	echo '<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit Support Ticket</button>';
echo '</form>';
?>