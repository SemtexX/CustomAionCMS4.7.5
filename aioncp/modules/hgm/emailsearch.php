<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

echo '<div class="row">';
	echo '<div class="col-md-6">';
	
		echo '<form class="form-horizontal" method="post">';
			echo '<div class="form-group">';
				echo '<label for="input_0" class="col-sm-2 control-label">Email Address</label>';
				echo '<div class="col-sm-10">';
					echo '<input type="text" class="form-control" id="input_0" name="email_address">';
				echo '</div>';
			echo '</div>';

			echo '<div class="form-group">';
				echo '<div class="col-sm-offset-2 col-sm-10">';
					echo '<button type="submit" name="address_submit" value="ok" class="btn btn-primary">Search</button>';
				echo '</div>';
			echo '</div>';
		echo '</form>';
	
	echo '</div>';
echo '</div>';
	
if(check($_POST['email_address'])) {
	
	try {
		
		$emailAddress = preg_replace('/[^A-Za-z0-9@.]/', '', $_POST['email_address']); // clean var
		
		if(!check($emailAddress)) throw new Exception('Invalid request.');
		if(!Validator::Email($emailAddress)) throw new Exception('Invalid email address.');
		
		echo '<div class="row">';
		
			echo '<div class="col-md-4">';
				echo '<div class="panel panel-primary">';
					echo '<div class="panel-heading">Result(s):</div>';
					echo '<div class="panel-body">';
						$emailSearch = $db->queryFetch("SELECT * FROM `account_data` WHERE `email` = ?", array($emailAddress));
						if(is_array($emailSearch)) {
							echo '<table class="table table-condensed table-hover">';
								foreach($emailSearch as $accountData) {
									echo '<tr>';
										echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$accountData['name'].'">'.$accountData['name'].'</a></td>';
									echo '</tr>';
								}
							echo '</table>';
						} else {
							message('No accounts found.', 'warning');
						}
					echo '</div>';
				echo '</div>';
				
			echo '</div>';
			
		echo '</div>';

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}