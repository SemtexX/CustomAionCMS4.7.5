<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$LegionProfile = new LegionProfile();
$pendingApproval = $LegionProfile->getPendingApprovalList();

if(check($_GET['approve'])) {
	try {
		$LegionProfile->setId($_GET['approve']);
		$LegionProfile->approveRequest();
		redirect('sam/legionprofiles/');
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

if(check($_GET['deny'])) {
	try {
		$LegionProfile->setId($_GET['deny']);
		$LegionProfile->denyRequest();
		redirect('sam/legionprofiles/');
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

if(is_array($pendingApproval)) {
	
	message('The background width should have a minimum of 940px to cover the entire profile area horizontally.', 'info');
	
	echo '<table class="table">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Legion Name</th>';
				echo '<th>Requested Background</th>';
				echo '<th>Profile</th>';
				echo '<th>Actions</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			
		foreach($pendingApproval as $row) {
			
			$legionData = $sdb->queryFetchSingle("SELECT * FROM `legions` WHERE `id` = ?", array($row['id']));
			if(!is_array($legionData)) continue;
			if(!check($row['new_background'])) continue;
			
			$profileLink = generateLegionProfileUrl($legionData['id'], $legionData['name']);
			$approveLink = __BASE_URL__ . 'sam/legionprofiles/approve/' . $legionData['id'];
			$denyLink = __BASE_URL__ . 'sam/legionprofiles/deny/' . $legionData['id'];
			
			echo '<tr>';
				echo '<td>'.$legionData['name'].'</td>';
				echo '<td><a href="'.$row['new_background'].'" target="_blank">'.$row['new_background'].'</a></td>';
				echo '<td><a href="'.$profileLink.'" target="_blank" class="btn btn-xs btn-default">View Profile</a></td>';
				echo '<td>';
					echo '<a href="'.$approveLink.'" class="btn btn-xs btn-success">Approve</a> ';
					echo '<a href="'.$denyLink.'" class="btn btn-xs btn-danger">Deny</a>';
				echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
	echo '</table>';

} else {
	message('There are no profiles pending approval.', 'warning');
}