<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	
	if(check($_GET['request'])) {
		$requestElements = explode("/", $_GET['request']);
		if(is_array($requestElements)) {
			
			# security filters
			if(count($requestElements) != 4) redirect();
			if($requestElements[0] != 'profile') redirect();
			if($requestElements[1] != 'legion') redirect();
			if(!Validator::Number($requestElements[2])) redirect();
			if(!Validator::UnsignedNumber($requestElements[2])) redirect();
			if(!Validator::Chars($requestElements[3], array("a-z", "A-Z", "-"))) redirect();
			
			$sdb = Handler::loadDB('siel');
			$serverName = 'siel';
			$legionId = $requestElements[2];
			
			# get legion info
			$LegionProfile = new LegionProfile();
			$LegionProfile->setId($legionId);
			$cacheData = $LegionProfile->getProfileInfo();
			
			# get legion emblem
			$legionEmblem = legionEmblem($legionId);
			
			# build legion members array
			$legionMembersList = $cacheData['members'];
			
			# brigade general
			$legionBG = $legionMembersList['BRIGADE_GENERAL'][0]['name'];
			
			# members rank display order
			$legionmembersDisplayOrder = array(
				'BRIGADE_GENERAL',
				'DEPUTY',
				'CENTURION',
				'LEGIONARY',
				'VOLUNTEER'
			);
			
			# theme color
			$profileColor = check($cacheData['profile']['custom_color']) ? $cacheData['profile']['custom_color'] : 'gray';
			
			# background
			$profileBackground = check($cacheData['profile']['custom_background']) ? $cacheData['profile']['custom_background'] : ''.__BASE_URL__.'static/profiles/legion/default.jpg';
		}
	}

	echo '<div class="legion-profile-container">';
		echo '<div class="legion-profile-background" style="background:url(\''.$profileBackground.'\') no-repeat top center;">';
			echo '<div class="leagion-profile-header '.$profileColor.'">';
				echo '<div class="legion-emblem">';
					if($legionEmblem) {
						echo '<img src="'.$legionEmblem.'" width="144" height="144">';
					} else {
						echo '<br />';
					}
				echo '</div>';
				echo '<div class="legion-info">';
					echo '<div class="row">';
						echo '<div class="col-md-12">';
							echo '<span class="legion-name">'.$cacheData['name'].'</span><br />';
						echo '</div>';
						
						echo '<div class="col-md-12">';
							echo '<br />';
						echo '</div>';
						
						echo '<div class="col-md-4">';
							echo '<span class="legion-alt-title">Brigade General</span><br />';
							echo '<strong>'.$legionBG.'</strong>';
						echo '</div>';
						echo '<div class="col-md-4">';
							echo '<span class="legion-alt-title">Level</span><br />';
							echo '<strong>'.$cacheData['level'].'</strong>';
						echo '</div>';
						echo '<div class="col-md-4">';
							echo '<span class="legion-alt-title">Contribution Points</span><br />';
							echo '<strong>'.number_format($cacheData['contribution_points']).'</strong>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
			// CUSTOM MESSAGE
			if(check($cacheData['profile']['custom_message'])) {
				echo '<div class="leagion-profile-block '.$profileColor.'">';
						echo '<p style="text-align:center;font-size:20px;color:#ffffff;">'.$cacheData['profile']['custom_message'].'</p>';
				echo '</div>';
			}
			
			// YOUTUBE VIDEO
			if(check($cacheData['profile']['youtube_video'])) {
				echo '<div class="leagion-profile-block '.$profileColor.'">';
						echo '<iframe width="718" height="403" src="https://www.youtube.com/embed/'.$cacheData['profile']['youtube_video'].'?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
				echo '</div>';
			}
			
			// LEGION MEMBERS
			echo '<div class="leagion-profile-block '.$profileColor.'">';
					echo '<div class="legion-members-title">Members</div>';
					if(is_array($legionMembersList)) {
						echo '<table class="legion-members-table">';
						echo '<tr>';
							echo '<th style="width:370px;"></th>';
							echo '<th style="width:70px;"></th>';
							echo '<th style="width:158px;" colspan="3"></th>';
							echo '<th style="width:120px;"></th>';
						echo '</tr>';
						
						foreach($legionmembersDisplayOrder as $rank) {
							if(is_array($legionMembersList[$rank])) {
								foreach($legionMembersList[$rank] as $row) {
									echo '<tr>';
										echo '<td>'.$row['name'].'</td>';
										echo '<td><span data-toggle="tooltip" data-placement="top" title="Level" alt="Level">'.expToLevel($row['exp']).'</span></td>';
										echo '<td>'.getRaceImg($row['race']).'</td>';
										echo '<td>'.getClassImg($row['player_class']).'</td>';
										echo '<td>'.getGenderImg($row['gender']).'</td>';
										echo '<td>'.($rank == 'BRIGADE_GENERAL' ? 'Brigade General' : ucfirst(strtolower($rank))).'</td>';
									echo '</tr>';
								}
							}
						}
						
						echo '</table>';
					} else {
						message('This legion has no members.', 'warning');
					}
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
} catch(Exception $ex) {
	
	echo '<div class="legion-profile-container">';
		echo '<div class="legion-profile-background">';
			
			message($ex->getMessage(), 'warning');
			
		echo '</div>';
	echo '</div>';
	
}

//debug($cacheData);