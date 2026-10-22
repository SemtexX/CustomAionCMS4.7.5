<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

# manual form
if(check($_POST['search_submit'], $_POST['search_type'], $_POST['search_by'], $_POST['search_request'])) {
	redirect('bans/search/method/' . $_POST['search_by'] . '/type/' . $_POST['search_type'] . '/request/' . $_POST['search_request']);
}
echo '<div class="row">';
	echo '<div class="col-md-6">';
		echo '<form class="form-inline" method="post">';
			echo '<div class="form-group">';
				echo '<select class="form-control" name="search_type">';
					echo '<option value="permanent">Permanent</option>';
					echo '<option value="temporal">Temporal</option>';
				echo '</select>';
			echo '</div>';
			echo '<div class="form-group">';
				echo '<select class="form-control" name="search_by">';
					echo '<option value="id">Ban Id</option>';
					echo '<option value="account">Account Name</option>';
					echo '<option value="staff">Banned By</option>';
					echo '<option value="reason">Reason</option>';
					echo '<option value="lastmessage">Last Message By</option>';
				echo '</select>';
			echo '</div>';
			echo '<div class="form-group">';
				echo '<input type="text" class="form-control" name="search_request">&nbsp;';
			echo '</div>';
			echo '<button type="submit" name="search_submit" value="ok" class="btn btn-primary">Search</button>';
		echo '</form>';
	echo '</div>';
echo '</div>';

echo '<br />';
	
if(check($_GET['method'], $_GET['type'], $_GET['request'])) {
	
	try {
		
		echo '<div class="row">';
			echo '<div class="col-md-12">';
				echo '<div class="block">';
					echo '<div class="block-content">';
						
						echo '<div class="block-header"><h3 class="block-title">Search Results:</h3></div>';
						
						$search = $_GET['request'];
						$type = ($_GET['type'] == 'permanent' ? 'permanent_bans' : 'temporal_bans');
						
						switch($_GET['method']) {
							case 'id':
								$result = $gabs->queryFetch("SELECT * FROM `$type` WHERE `id` LIKE '%$search%'", array());
								break;
							case 'account':
								$result = $gabs->queryFetch("SELECT * FROM `$type` WHERE `account` LIKE '%$search%'", array());
								break;
							case 'staff':
								$result = $gabs->queryFetch("SELECT * FROM `$type` WHERE `staff` LIKE '%$search%'", array());
								break;
							case 'reason':
								$result = $gabs->queryFetch("SELECT * FROM `$type` WHERE `reason` LIKE '%$search%'", array());
								break;
							case 'lastmessage':
								$result = $gabs->queryFetch("SELECT * FROM `$type` WHERE `last_message_by` LIKE '%$search%'", array());
								break;
							default:
								throw new Exception('Invalid search method.');
						}
						
						if(!is_array($result)) throw new Exception('0 results found.');
						
						echo '<table class="table">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Id</th>';
								echo '<th>Account</th>';
								echo '<th>By</th>';
								echo '<th>Reason</th>';
								if($_GET['type'] == 'permanent') {
									echo '<th>Date</th>';
								} else {
									echo '<th>S. Date</th>';
									echo '<th>E. Date</th>';
								}
								echo '<th>Status</th>';
								echo '<th>Last Msg.</th>';
								echo '<th></th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($result as $row) {
							echo '<tr>';
								echo '<td>'.$row['id'].'</td>';
								echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['account'].'">'.$row['account'].'</a></td>';
								echo '<td>'.$row['staff'].'</td>';
								echo '<td>'.$row['reason'].'</td>';
								if($_GET['type'] == 'permanent') {
									echo '<td>'.$row['date'].'</td>';
								} else {
									echo '<td>'.$row['start_date'].'</td>';
									echo '<td>'.$row['end_date'].'</td>';
								}
								echo '<td>'.($row['staff_lock'] == 1 ? '<span class="label label-danger">Locked</span>' : '<span class="label label-success">Open</span>').'</td>';
								echo '<td>'.$row['last_message_by'].'</td>';
								echo '<td><a href="'.__BASE_URL__.'bans/view/type/'.$_GET['type'].'/id/'.$row['id'].'" class="btn btn-primary btn-xs">Open</a></td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table>';
					
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
	
}
	