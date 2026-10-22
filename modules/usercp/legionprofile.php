<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block usercp"></div>
<br /><br />

<h3>Customize My Legion Profile</h3>
<p>Customize your legion's profile with the settings displayed below.</p>
<br /><br />
<?php
try {
	
	// check id
	if(!check($_GET['id'])) throw new Exception('The provided legion id is not valid.');
	if(!Validator::UnsignedNumber($_GET['id'])) throw new Exception('The provided legion id is not valid.');
	
	// legion info
	$LegionProfile = new LegionProfile();
	$LegionProfile->setId($_GET['id']);
	$cacheData = $LegionProfile->getProfileInfo();
	if(!is_array($cacheData)) throw new Exception('There was an error loading your legion\'s profile, contact support.');
	
	// is banned from customizing ?
	if($cacheData['profile']['banned'] == 1) throw new Exception('You have been banned from customizing your legion\'s profile, contact support.');
	
	// get playerlist
	$sdb = Handler::loadDB('siel');
	$characters = $sdb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($_SESSION['userid']));
	if(!is_array($characters)) throw new Exception('You don\'t have any characters in your account.');
	foreach($characters as $character) {
		$characterList[] = strtolower($character['name']);
	}
	
	// check owner
	if(!in_array(strtolower($cacheData['members']['BRIGADE_GENERAL'][0]['name']), $characterList)) throw new Exception('You do not have permission to customize this legion\'s profile.');
	
	// pending approval
	if($cacheData['profile']['requires_approval'] == 1) {
		message('Your custom profile background is pending approval, submitting a new custom background is disabled until your request is processed. You may still change any other settings.', 'warning');
	}
	
	// form submit
	if(check($_POST['profile_submit'])) {
		try {
			
			$LegionProfileUpdate = new LegionProfile();
			$LegionProfileUpdate->setId($_GET['id']);
			if(check($_POST['profile_color'])) $LegionProfileUpdate->setCustomColor($_POST['profile_color']);
			if(check($_POST['profile_message'])) $LegionProfileUpdate->setCustomMessage($_POST['profile_message']);
			if(check($_POST['profile_youtube'])) $LegionProfileUpdate->setYoutubeVideo($_POST['profile_youtube']);
			if($cacheData['profile']['requires_approval'] == 0) {
				if(check($_POST['profile_background'])) $LegionProfileUpdate->setCustomBackground($_POST['profile_background']);
			}
			$LegionProfileUpdate->saveProfile();
			message('Your legion\'s profile has been successfully updated!', 'success');
			
			// reload info
			$cacheData = $LegionProfileUpdate->getProfileInfo();
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	// settings
	echo '<form action="'.module_url('usercp/legionprofile/id/' . $_GET['id'], true).'" method="post">';
	echo '<table class="my-account-table">';
		echo '<tr>';
			echo '<td>Legion</td>';
			echo '<td><a href="'.generateLegionProfileUrl($_GET['id'], $cacheData['name']).'" target="_blank">'.$cacheData['name'].'</a></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="2"><br /></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>Main Color</td>';
			echo '<td>';
				echo '<select name="profile_color" class="form-control">';
					echo '<option value="gray" '.($cacheData['profile']['custom_color'] == 'gray' ? 'selected' : null).'>Gray (default)</option>';
					echo '<option value="red" '.($cacheData['profile']['custom_color'] == 'red' ? 'selected' : null).'>Red</option>';
					echo '<option value="green" '.($cacheData['profile']['custom_color'] == 'green' ? 'selected' : null).'>Green</option>';
					echo '<option value="blue" '.($cacheData['profile']['custom_color'] == 'blue' ? 'selected' : null).'>Blue</option>';
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="2"><br /></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>Custom Message</td>';
			echo '<td><textarea name="profile_message" class="form-control" maxlength="250" style="height:150px;">'.$cacheData['profile']['custom_message'].'</textarea></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td></td>';
			echo '<td><span style="font-size:11px;">Your custom message can contain 250 characters max.</span></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="2"><br /></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>YouTube Video</td>';
			echo '<td><input type="text" name="profile_youtube" class="form-control" value="'.$cacheData['profile']['youtube_video'].'"/></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td><span style="font-size:11px;">Provide the full video url, example:<br /><span style="color:red;font-weight:bold;">https://www.youtube.com/watch?v=dQw4w9WgXcQ</span></span></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="2"><br /></td>';
		echo '</tr>';
		
		if($cacheData['profile']['requires_approval'] == 0) {
			echo '<tr>';
				echo '<td>Custom Background</td>';
				echo '<td><input type="text" name="profile_background" class="form-control" placeholder="Leave empty to keep the current one..."/></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td></td>';
				echo '<td><span style="font-size:11px;">Provide a direct image link, example:<br /><span style="color:red;font-weight:bold;">https://i.imgur.com/qdvB89p.jpg</span><br ><br />Image Hosts: <a href="https://imgur.com/" target="_blank">Imgur</a></span></td>';
			echo '</tr>';
		}
		
		echo '<tr>';
			echo '<td></td>';
			echo '<td><button type="submit" name="profile_submit" value="ok" class="btn btn-primary">Save Changes</button></td>';
		echo '</tr>';
	echo '</table>';
	echo '</form>';
	
	
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
	//redirect('usercp/');
}

?>