<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

# manual form
if(check($_POST['player_submit'], $_POST['player_name'])) {
	redirect('admin/playerskills/name/' . $_POST['player_name']);
}
echo '<div class="row">';
	echo '<div class="col-md-6">';
	
		echo '<form class="form-horizontal" method="post">';
			echo '<div class="form-group">';
				echo '<label for="input_0" class="col-sm-2 control-label">Player</label>';
				echo '<div class="col-sm-10">';
					echo '<input type="text" class="form-control" id="input_0" name="player_name">';
				echo '</div>';
			echo '</div>';

			echo '<div class="form-group">';
				echo '<div class="col-sm-offset-2 col-sm-10">';
					echo '<button type="submit" name="player_submit" value="ok" class="btn btn-primary">Search</button>';
				echo '</div>';
			echo '</div>';
		echo '</form>';
	
	echo '</div>';
echo '</div>';
	
if(check($_GET['name'])) {
	
	try {
		
		if(!check($_GET['name'])) throw new Exception('Invalid request.');
		
		$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ?", array($_GET['name']));
		if(!is_array($playerData)) throw new Exception('Player name does not exist. (are you searching the correct server?)');
		
		$playerSkills = $sdb->queryFetch("SELECT * FROM `player_skills` WHERE `player_id` = ?", array($playerData['id']));
		if(!is_array($playerSkills)) throw new Exception('No skills found.');
		
		//debug($sdb->query("DELETE FROM `player_skills` WHERE `player_id` = ?", array($playerData['id'])));
		//die();
		
		//echo '<textarea>';
		foreach($playerSkills as $skill) {
			
			$skillData = $db->queryFetchSingle("SELECT * FROM `aion_skilllist` WHERE `skill_id` = ?", array($skill['skill_id']));
			if(!is_array($skillData)) continue;
			
			debug('[' . $skill['skill_id'] . '] ' .$skillData['skill_name'] . ' ('.$skill['skill_level'].')');
			
			//echo $skill['skill_id'] . ',' . $skill['skill_level'] . ',' . $skill['skill_type'] . '|';
			
		}
		//echo '</textarea>';
		
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}