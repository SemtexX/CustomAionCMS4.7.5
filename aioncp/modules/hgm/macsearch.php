<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

if(!check($_GET['address'])) {
	
	# manual form
	if(check($_POST['address_submit'], $_POST['mac_address'])) {
		redirect('hgm/macsearch/address/' . $_POST['mac_address']);
	}
	echo '<div class="row">';
		echo '<div class="col-md-6">';
		
			echo '<form class="form-horizontal" method="post">';
				echo '<div class="form-group">';
					echo '<label for="input_0" class="col-sm-2 control-label">MAC</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_0" name="mac_address">';
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
	
} else {
	
	try {
		
		if(!check($_GET['address'])) throw new Exception('Invalid request.');
		
		echo '<div class="row">';
		
			echo '<div class="col-md-4">';
				# LAST LOGIN MAC
				echo '<div class="panel panel-primary">';
					echo '<div class="panel-heading">'.$_GET['address'].' Last Login MAC</div>';
					echo '<div class="panel-body">';
						$ipSearch = $db->queryFetch("SELECT * FROM `account_data` WHERE `last_mac` = ?", array($_GET['address']));
						if(is_array($ipSearch)) {
							echo '<table class="table table-condensed table-hover">';
								foreach($ipSearch as $accountData) {
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