<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

if(!check($_GET['id'])) {
	
	# Search form
	echo '<div class="row">';
		echo '<div class="col-lg-6">';
		
			echo '<form class="form-horizontal" method="post">';
				echo '<div class="form-group">';
					echo '<label for="input_0" class="col-sm-2 control-label">Legion Name</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_0" name="legion_name" autofocus/>';
					echo '</div>';
				echo '</div>';

				echo '<div class="form-group">';
					echo '<div class="col-sm-offset-2 col-sm-10">';
						echo '<button type="submit" name="legion_submit" value="ok" class="btn btn-primary">Search</button>';
					echo '</div>';
				echo '</div>';
			echo '</form>';
		
		echo '</div>';
	echo '</div>';
	
	# Search results
	if(check($_POST['legion_submit'], $_POST['legion_name'])) {
		try {
			
			if(strlen($_POST['legion_name']) < 3) throw new Exception('3 characters minimum.');
			
			$legionSearch = $sdb->queryFetch("SELECT * FROM `legions` WHERE `name` LIKE '%".$_POST['legion_name']."%'");
			if(!is_array($legionSearch)) throw new Exception('No results.');
			
			echo '<div class="row">';
				echo '<div class="col-lg-4">';
					echo '<div class="panel panel-info">';
						echo '<div class="panel-heading">Results</div>';
						echo '<div class="panel-body">';
							echo '<table class="table">';
							foreach($legionSearch as $legion) {
								echo '<tr>';
									echo '<td>'.$legion['name'].'</td>';
									echo '<td width="25px"><a href="'.__BASE_URL__.'tools/legiondetails/id/'.$legion['id'].'/" class="btn btn-primary btn-xs">Info.</a></td>';
								echo '</tr>';
							}
							echo '</table>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
} else {
	
	try {
		if(!check($_GET['id'])) throw new Exception('Invalid request.');
		
		$legionData = $sdb->queryFetchSingle("SELECT * FROM `legions` WHERE `id` = ?", array($_GET['id']));
		if(!is_array($legionData)) throw new Exception('Legion id does not exist. (are you searching the correct server?)');
		
		$legionMembers = $sdb->queryFetch("SELECT * FROM `legion_members` INNER JOIN `players` ON `legion_members`.`player_id` = `players`.`id` WHERE `legion_members`.`legion_id` = ? ORDER BY `legion_members`.`rank` ASC", array($_GET['id']));
		//if(!is_array($legionMembers)) throw new Exception('Legion has no members.');
		
		$legionHistory = $sdb->queryFetch("SELECT * FROM `legion_history` WHERE `legion_id` = ? ORDER BY `date` DESC LIMIT 20", array($_GET['id']));
		echo '<div class="row">';
			echo '<div class="col-lg-4">';
			
				# LEGION INFO
				echo '<div class="panel panel-success">';
					echo '<div class="panel-heading">'.$legionData['name'].'\'s Information</div>';
					echo '<div class="panel-body">';
						echo '<ul class="list-group">';
							echo '<li class="list-group-item"><strong>Name</strong><span class="pull-right text-muted small">'.$legionData['name'].'</span></li>';
							echo '<li class="list-group-item"><strong>Id</strong><span class="pull-right text-muted small">'.$legionData['id'].'</span></li>';
							echo '<li class="list-group-item"><strong>Level</strong><span class="pull-right text-muted small">'.$legionData['level'].'</span></li>';
							echo '<li class="list-group-item"><strong>Contribution Points</strong><span class="pull-right text-muted small">'.number_format($legionData['contribution_points']).'</span></li>';
						echo '</ul>';
					echo '</div>';
				echo '</div>';
			
				# LEGION INFO
				echo '<div class="panel panel-default">';
					echo '<div class="panel-heading">History (last 20)</div>';
					echo '<div class="panel-body">';
						if(is_array($legionHistory)) {
							echo '<table class="table table-hover table-condensed">';
							echo '<tbody>';
							foreach($legionHistory as $row) {
								echo '<tr>';
									echo '<td style="font-size:11px;">['.$row['date'].'] ['.$row['history_type'].'] '.$row['name'].'</td>';
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
						} else {
							message('No legion members found.','warning');
						}
					echo '</div>';
				echo '</div>';
				
			echo '</div>';
			
			echo '<div class="col-lg-8">';
			
				echo '<div class="panel panel-info">';
					echo '<div class="panel-heading">Legion Members</div>';
					echo '<div class="panel-body">';
						if(is_array($legionMembers)) {
							echo '<table class="table table-hover table-condensed">';
							echo '<thead>';
								echo '<tr>';
									echo '<th>Name</th>';
									echo '<th>Rank</th>';
									echo '<th>Level</th>';
									echo '<th>Race</th>';
									echo '<th>Class</th>';
									echo '<th>Last Online</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							foreach($legionMembers as $member) {
								echo '<tr>';
									echo '<td><a href="'.__BASE_URL__.'tools/playerdetails/name/'.$member['name'].'/">'.$member['name'].'</a></td>';
									echo '<td>'.$member['rank'].'</td>';
									echo '<td>'.expToLevel($member['exp']).'</td>';
									echo '<td>'.$member['race'].'</td>';
									echo '<td>'.$member['player_class'].'</td>';
									echo '<td>'.($member['online'] == 1 ? '<span class="label label-success">Online</span>' : $member['last_online']).'</td>';
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
						} else {
							message('No legion members found.','warning');
						}
					echo '</div>';
				echo '</div>';
				
			echo '</div>';
			
		echo '</div>';

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}