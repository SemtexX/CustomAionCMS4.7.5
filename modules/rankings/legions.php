<?php
// Am Anfang jeder Seite
include(__DIR__ . '/../countdown_timer.php');
?>
<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block rankings"></div>
<br /><br />

<?php
	try {
		
		$rankingType = $_GET['submodule'];
		$rankingServer = 'siel';
		
		# Rankings Menu
		echo '<div class="rankings-selection">';
			echo '<a href="'.module_url('rankings/abyss/', true).'">Abyss</a> / ';
			echo '<a href="'.module_url('rankings/glory/', true).'">Glory Points</a> / ';
			echo '<a href="'.module_url('rankings/kills/', true).'">Kills</a> / ';
			echo '<a href="'.module_url('rankings/legions/', true).'" class="active">Legions</a> / ';
			echo '<a href="'.module_url('rankings/votes/', true).'">Votes</a>';
		echo '</div>';
		
		// LEGIONS RANKING
		
		$rankingData = loadCacheFile('rankings.legions.'.$rankingServer.'.cache');
		if(!$rankingData) throw new Exception("There was a problem loading the ranking data, please try again later.");
		
		$result = rankingCacheToArray($rankingData);
		
		echo '<table class="rankings-table" cellspacing="0">';
			echo '<thead>';
			echo '<tr>';
				echo '<th>Rank</th>';
				echo '<th></th>';
				echo '<th>Legion</th>';
				echo '<th>Level</th>';
				echo '<th>Members</th>';
				echo '<th>Points</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			
			$i = 1;
			foreach($result as $row) {
				
				$emblem = legionEmblem($row[0]);
				$ledionProfileName = preg_replace('/\s+/', '-', trim($row[1]));
				$profileLink = __BASE_URL__ . 'legion/' . $row[0] . '/' . $ledionProfileName;
				
				echo '<tr>';
					echo '<td>'.$i.'</td>';
					echo '<td><img src="'.$emblem.'" width="40" height="40"/></td>';
					echo '<td><a href="'.$profileLink.'">'.$row[1].'</a></td>';
					echo '<td>'.$row[2].'</td>';
					echo '<td>'.$row[4].'</td>';
					echo '<td>'.number_format($row[3]).'</td>';
				echo '</tr>';
				
				$i++;
			}
			echo '</tbody>';
		echo '</table>';
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
?>