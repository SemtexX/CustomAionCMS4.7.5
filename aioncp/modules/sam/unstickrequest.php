<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


# UNSTICK REQUEST
if(check($_GET['id'])) {
	if($_GET['id'] == 'all') {
		# UNSTICK ALL
		try {
			$requestCheck = $sdb->queryFetch("SELECT * FROM `aioncms`.`unstick`", array());
			if(!is_array($requestCheck)) throw new Exception('No requests to process.');
			
			$i = 0;
			foreach($requestCheck as $request) {
				$unstick = unstickPlayer($request['player'], $request['race']);
				if(!$unstick) throw new Exception('Could not unstick player.');
				
				$deleteRequest = $sdb->query("DELETE FROM `aioncms`.`unstick` WHERE `id` = ?", array($request['id']));
				if(!$deleteRequest) throw new Exception('Could not delete unstick request ['.$request['id'].'].');
				
				$i++;
			}
			
			message('Task completed, '.$i.' players unstuck.', 'success');
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	} else {
		# SINGLE UNSTICK
		try {
			$requestCheck = $sdb->queryFetchSingle("SELECT * FROM `aioncms`.`unstick` WHERE `id` = ?", array($_GET['id']));
			if(!is_array($requestCheck)) throw new Exception('Invalid request id.');
			
			$unstick = unstickPlayer($requestCheck['player'], $requestCheck['race']);
			if(!$unstick) throw new Exception('Could not unstick player.');
			
			$deleteRequest = $sdb->query("DELETE FROM `aioncms`.`unstick` WHERE `id` = ?", array($_GET['id']));
			if(!$deleteRequest) throw new Exception('Could not delete unstick request.');
			
			message('Request completed.', 'success');
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
}

# LIST
$requestList = $sdb->queryFetch("SELECT * FROM `aioncms`.`unstick`", array());
if(is_array($requestList)) {
	
	echo '<table class="table table-condensed table-hover">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Account</th>';
				echo '<th>Player</th>';
				echo '<th>Race</th>';
				echo '<th></th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach($requestList as $row) {
			echo '<tr>';
				echo '<td>'.$row['account'].'</td>';
				echo '<td>'.$row['player'].'</td>';
				echo '<td>'.$row['race'].'</td>';
				echo '<td><a href="'.__BASE_URL__.'sam/unstickrequest/id/'.$row['id'].'" class="btn btn-xs btn-default">Unstick</a> <a href="'.__BASE_URL__.'sam/unstickrequest/delete/'.$row['id'].'" class="btn btn-xs btn-danger">Delete</a></td>';
			echo '</tr>';
		}
		echo '</tbody>';
	echo '</table>';
	
	echo '<a href="'.__BASE_URL__.'sam/unstickrequest/id/all" class="btn btn-lg btn-success">Unstick ALL</a>';
	
} else {
	message('No requests at the moment.', 'warning');
}