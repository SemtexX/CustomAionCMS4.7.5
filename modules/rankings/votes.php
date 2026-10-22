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
		
		# Switch Server
		echo '<div class="row text-center">';
			echo '<div class="btn-group" role="group" aria-label="switchServer">';
				echo '<a href="'.module_url('usercp/voteranking/', true).'" class="btn btn-success">Participate</a>';
			echo '</div>';
		echo '</div>';
		
		# Rankings Menu
		echo '<div class="rankings-selection">';
			echo '<a href="'.module_url('rankings/abyss/', true).'">Abyss</a> / ';
			echo '<a href="'.module_url('rankings/glory/', true).'">Glory Points</a> / ';
			echo '<a href="'.module_url('rankings/kills/', true).'">Kills</a> / ';
			echo '<a href="'.module_url('rankings/legions/', true).'">Legions</a> / ';
			echo '<a href="'.module_url('rankings/votes/', true).'" class="active">Votes</a>';
		echo '</div>';
		
		// VOTES RANKING
		
		$rankingData = loadCacheFile('rankings.votes.cache');
		//if(!$rankingData) throw new Exception("There was a problem loading the ranking data, please try again later.");
		if(!$rankingData) throw new Exception("Ranking is being prepared!");
		
		$result = rankingCacheToArray($rankingData);
		
		echo '<table class="rankings-table" cellspacing="0">';
			echo '<thead>';
			echo '<tr>';
				echo '<th>Rank</th>';
				echo '<th>Name</th>';
				echo '<th>Level</th>';
				echo '<th>Race</th>';
				echo '<th>Class</th>';
				echo '<th>Gender</th>';
				echo '<th>Votes</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			
			$i = 1;
			foreach($result as $row) {
				echo '<tr>';
					echo '<td>'.$i.'</td>';
					echo '<td>'.$row[0].'</td>';
					echo '<td>'.expToLevel($row[1]).'</td>';
					echo '<td>'.getRaceImg($row[3]).'</td>';
					echo '<td>'.getClassImg($row[4]).'</td>';
					echo '<td>'.getGenderImg($row[2]).'</td>';
					echo '<td>'.$row[5].'</td>';
				echo '</tr>';
				
				$i++;
			}
			echo '</tbody>';
		echo '</table>';
		
	} catch(Exception $ex) {
		echo '<br />';
		message($ex->getMessage(), 'error');
	}
?>