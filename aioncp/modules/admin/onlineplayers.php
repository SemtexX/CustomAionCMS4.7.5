<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

$onlinePlayers = $sdb->queryFetch("SELECT * FROM `players` WHERE `online` = ?", array(1));
if(is_array($onlinePlayers)) {
	echo '<ul>';
		foreach($onlinePlayers as $player) {
			echo '<li><a href="'.__BASE_URL__.'tools/playerdetails/name/'.$player['name'].'">'.$player['name'].'</a></li>';
		}
	echo '</ul>';
}