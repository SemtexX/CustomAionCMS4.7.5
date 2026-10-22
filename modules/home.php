<?php
// Am Anfang jeder Seite
include('countdown_timer.php');
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
<div class="home-main-content">
	<div class="left-side">
		<div class="left-side-container">
			<?php if(!isLoggedIn()) { ?>
			<div class="login-box">
				<form action="<?php module_url(); ?>login/" method="post">
					<input type="text" class="login-username" maxlength="25" name="login_username" autofocus/>
					<input type="password" class="login-password" name="login_password"/><br />
					<!--<a href="<?php base_url(); ?>lock/" target="_blank" class="btn btn-xs btn-primary login-lock"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></a>-->
					<button type="submit" class="login-submit" name="login_submit" value="ok"></button>
				</form>
			</div>
			<?php } else { ?>
			<div class="account-box">
				<div class="account-box-content">
					<div style="text-align:center;padding-top:20px;">
						Welcome back, <?php echo $_SESSION['username']; ?><br />
						<a href="<?php module_url(); ?>usercp/" style="color:#63c2ff;text-decoration:none;">[ usercp ]</a> | <a href="<?php module_url(); ?>logout/" style="color:#ff6363;text-decoration:none;">[ logout ]</a>
						<br />
						<div style="font-size:11px;margin-top: 10px;">
							<span style="color:#cccccc;">Server Time</span><br />
							<span style="color:#00ff00;"><?php echo date("d-m-Y h:i"); ?></span>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="left-text-container">
				<p>Welcome to <span style="color:#d6be93;">GlobalXcentra</span> Private Server.</p>
				<p>Aion Online 4.7.5 (Steel Cavalry) is now Live!</p>
				<div class="left-text-container-newsblock">
				<div style="overflow-y: scroll; height: 150px;">
				<center><span style="color:#d6be93;font-size:18px;">Latest News: 22.06.2024</span><br></center>
				<center><span style="color:#d6be93;font-size:14px;">Updated News<br><br>
				<center><span style="color:#d6be93;font-size:17px;">Website Updates:</span><br></center><br>
				• | A bug has been fixed where items like the Ring of Ancient Magic disappeared after a single use when purchased from the webshop.<br><br>
				• | The appearance of the timetable on the website has been changed.<br><br>
				• | The appearance of the server info page on our website has been updated.<br><br>
				• | The appearance of our Unique Info page has been adjusted.<br><br>
				• | Vote Reward System has been implemented, initially with only 2 voting sites.<br><br>
				• |	The lottery system has been implemented.<br><br>
				<center><span style="color:#d6be93;font-size:17px;">Core Updates:</span><br></center><br>
				• | A bug has been fixed where players were dismounted while riding upon changing zones.<br><br>
				• | A bug has been fixed that prevented summoned energies from a summoner from dealing damage.<br><br>
				• | A new mesh pathfinding system has been added and further optimized.<br><br>
				• | A server-wide message has been added when a player enchants jewelry or feathers to +5 or higher.<br><br>
				• | A bug has been fixed where many teleport statues were not functioning properly.<br><br>
				• | The summoner summon service has been revised to decrease the likelihood of the spirits' skills being replaced by the summoner's skills. This update aims to give players more control over their spirits' abilities.<br><br>
				• | A bug has been fixed where the summoner spirit's taunt abilities did not generate sufficient aggro to draw enemy attention. With this issue resolved, the summoner spirit's skills should now be more effective in attracting enemies and improving the character's survivability.<br><br>
				• | A bug has been fixed where players couldn't socket more than one item simultaneously with empty mana stone slots.<br><br>
				• | Missing Windstream animation added. A bug has been fixed where incorrect speed values were received when leaving a Windstream.<br><br>
				• | All godstones have been reactivated, and additionally, Illusion Godstones now possess their original function of being destroyed with a low probability.<br><br>
				• | A bug has been fixed where the skill "Chain of Suffering" persisted indefinitely.<br><br>
				• | The drop lists of Kaldor and Levinshor have been adjusted.<br><br>
				<center><span style="color:#d6be93;font-size:16px;">Skill Updates:</span><br></center><br>
				• | A bug has been fixed where some skills were not triggering special effects such as Airbind, Knockback, Knockdown, and Stun.<br><br>
				• | A bug has been fixed where skills that reduced cooldowns were not functioning correctly.<br><br>
				• | A bug has been fixed where some portals to the underground of Katalam had incorrect IDs after capturing the corresponding garrison.<br><br>
				<center><span style="color:#d6be93;font-size:17px;">Base Updates:</span><br></center><br>
				• | A bug has been fixed where certain NPCs in the garrisons of North & South Katalam were assigned to an inappropriate tribe.<br><br>
				• | A bug has been fixed where incorrect NPCs were spawned when garrisons were controlled by the Balaur.<br><br>
				• | A bug has been fixed where certain garrison monsters appeared with inaccurate levels and stats.<br><br>
				<center><span style="color:#d6be93;font-size:17px;">Instance Updates:</span><br></center><br>
				• | Survival Arena up to level 20 has been implemented.<br><br>
				• | 3 additional AIs have been added for the new Survival Arena.<br><br>
				• | Prestige Coins have been added as rewards for defeating Survival Arena opponents.<br><br>
				• | An extra round has been implemented. After completing this round, NPCs will spawn where you can heal and reset your skills.<br><br>
				• | The cooldown of the Survival Arena instance has been reduced to 30 minutes.<br><br>
				• | A final boss round has been added to the New Survival Arena.<br><br>
				<center><span style="color:#d6be93;font-size:17px;">Client Updates:</span><br></center><br>
				• | 41 new NPCs have been implemented.<br><br>
				• | With this Patch we released a New Client, u can download/update the Client with our Launcher<br><br>
				
				
				</center></span>
				</div>

					<?php
					$forumRss = 'FORUM_NEWS_RSS_LINK';
					$loadRssXml = @simplexml_load_file($forumRss);
					if($loadRssXml) {
						$eventsFeed = $loadRssXml->channel;
						
						echo '<span class="newsheader">Latest News:</span>';
						echo '<table class="newstable">';
							$newsIndex = 0;
							foreach($eventsFeed->item as $item) {
								if($newsIndex > 8) continue;
								//if(!preg_match('/Carl/', $item->author) && !preg_match('/Lautaro/', $item->author)) continue;
								$timestamp = strtotime($item->pubDate);
								
								echo '<tr>';
									echo '<td class="newstitle"><a href="'.$item->link.'" target="_blank">'.$item->title.'</a></td>';
									echo '<td class="newsdate">'.date("M j", $timestamp).'</td>';
								echo '</tr>';
								
								$newsIndex++;
							}
						echo '</table>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="middle">
		<div class="middle-container">
			<a href="<?php module_url(); ?>register/" class="register-button"></a>
			
			<div class="home-rankings-container">
				
				<div class="home-rankings">
				<!-- Nav tabs -->
				<div class="text-center">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation"><a href="#gp" aria-controls="gp" role="tab" data-toggle="tab">GP</a></li>
						<li role="presentation"><a href="#abyss" aria-controls="abyss" role="tab" data-toggle="tab">Abyss</a></li>
						<li role="presentation"><a href="#kills" aria-controls="kills" role="tab" data-toggle="tab">Kills</a></li>
						<li role="presentation"><a href="#legions" aria-controls="legions" role="tab" data-toggle="tab">Legions</a></li>
						<li role="presentation"><a href="#votes" aria-controls="votes" role="tab" data-toggle="tab">Votes</a></li>
					</ul>
				</div>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane" id="abyss">
					<?php
					try {
						
						//$db = Handler::loadDB();
						//$sdb = Handler::loadDB('siel');

						// ABYSS RANKING
						$rankingData = loadCacheFile('rankings.abyss.siel.cache');
						if(!$rankingData) throw new Exception("There was a problem loading the ranking data, please try again later.");
						
						$result = rankingCacheToArray($rankingData);
						
						echo '<table class="abysstable">';
							echo '<tr>';
								echo '<th>Name</th>';
								echo '<th>Abyss Points</th>';
								echo '<th></th>';
								echo '<th></th>';
								echo '<th></th>';
							echo '</tr>';
							$i = 1;
							foreach($result as $row) {
								if($i >= 10) continue;
								echo '<tr>';
									echo '<td>'.$row[1].'</td>';
									echo '<td>'.number_format($row[6]).'</td>';
									echo '<td>'.getRaceImg($row[3]).'</td>';
									echo '<td>'.getClassImg($row[4]).'</td>';
									echo '<td>'.getGenderImg($row[5]).'</td>';
								echo '</tr>';
								
								$i++;
							}
						echo '</table>';
						
					} catch(Exception $ex) {
						//message($ex->getMessage(), 'error');
					}
					?>
					</div>
					<div role="tabpanel" class="tab-pane active" id="gp">
					<?php
						try {

							// GLORY RANKING
							$rankingData = loadCacheFile('rankings.glory.siel.cache');
							if(!$rankingData) throw new Exception("There was a problem loading the ranking data, please try again later.");
							
							$result = rankingCacheToArray($rankingData);
							
							echo '<table class="abysstable">';
								echo '<tr>';
									echo '<th>Name</th>';
									echo '<th>Glory Points</th>';
									echo '<th></th>';
									echo '<th></th>';
									echo '<th></th>';
								echo '</tr>';
								$i = 1;
								foreach($result as $row) {
									if($i >= 10) continue;
									echo '<tr>';
										echo '<td>'.$row[1].'</td>';
										echo '<td>'.number_format($row[6]).'</td>';
										echo '<td>'.getRaceImg($row[3]).'</td>';
										echo '<td>'.getClassImg($row[4]).'</td>';
										echo '<td>'.getGenderImg($row[5]).'</td>';
									echo '</tr>';
									
									$i++;
								}
							echo '</table>';
							
						} catch(Exception $ex) {
							//message($ex->getMessage(), 'error');
						}
					?>
					</div>
					<div role="tabpanel" class="tab-pane" id="kills">
					<?php
						try {
							
							//$db = Handler::loadDB();
							//$sdb = Handler::loadDB('siel');
							
							// KILLS RANKING
							$rankingData = loadCacheFile('rankings.kills.siel.cache');
							if(!$rankingData) throw new Exception("There was a problem loading the ranking data, please try again later.");
							
							$result = rankingCacheToArray($rankingData);
							
							echo '<table class="abysstable">';
								echo '<tr>';
									echo '<th>Name</th>';
									echo '<th>Kills</th>';
									echo '<th></th>';
									echo '<th></th>';
									echo '<th></th>';
								echo '</tr>';
								
								$i = 1;
								foreach($result as $row) {
									if($i >= 10) continue;
									echo '<tr>';
										echo '<td>'.$row[1].'</td>';
										echo '<td>'.number_format($row[6]).'</td>';
										echo '<td>'.getRaceImg($row[3]).'</td>';
										echo '<td>'.getClassImg($row[4]).'</td>';
										echo '<td>'.getGenderImg($row[5]).'</td>';
									echo '</tr>';
									
									$i++;
								}
							echo '</table>';
							
						} catch(Exception $ex) {
							//message($ex->getMessage(), 'error');
						}
					?>
					</div>
					<div role="tabpanel" class="tab-pane" id="legions">
					<?php
						try {
							
							// LEGIONS RANKING
							$rankingData = loadCacheFile('rankings.legions.siel.cache');
							if(!$rankingData) throw new Exception("There was a problem loading the ranking data, please try again later.");
							
							$result = rankingCacheToArray($rankingData);
							
							echo '<table class="legionstable">';
								echo '<tr>';
									echo '<th>Legion</th>';
									echo '<th>Points</th>';
								echo '</tr>';
								
								$i = 1;
								foreach($result as $row) {
									if($i >= 10) continue;
									echo '<tr>';
										echo '<td>'.$row[1].'</td>';
										echo '<td>'.number_format($row[3]).'</td>';
									echo '</tr>';
									
									$i++;
								}
							echo '</table>';
							
						} catch(Exception $ex) {
							//message($ex->getMessage(), 'error');
						}
					?>
					</div>
					<div role="tabpanel" class="tab-pane" id="votes">
					<?php
					try {
						
						// VOTES RANKING
						$rankingData = loadCacheFile('rankings.votes.cache');
						if(!$rankingData) throw new Exception("There was a problem loading the ranking data, please try again later.");
						
						$result = rankingCacheToArray($rankingData);
						
						echo '<table class="abysstable">';
							echo '<tr>';
								echo '<th>Name</th>';
								echo '<th>Votes</th>';
								echo '<th></th>';
								echo '<th></th>';
								echo '<th></th>';
							echo '</tr>';
							$i = 1;
							foreach($result as $row) {
								if($i >= 10) continue;
								echo '<tr>';
									echo '<td>'.$row[0].'</td>';
									echo '<td>'.$row[5].'</td>';
									echo '<td>'.getRaceImg($row[3]).'</td>';
									echo '<td>'.getClassImg($row[4]).'</td>';
									echo '<td>'.getGenderImg($row[2]).'</td>';
									
								echo '</tr>';
								
								$i++;
							}
						echo '</table>';
						
					} catch(Exception $ex) {
						//message($ex->getMessage(), 'error');
					}
					?>
					</div>
				</div>
				</div>
				
			</div>
		</div>
	</div>
	<div class="right-side">
		<div class="right-side-container">
			<a href="https://download.globalxcentra.net/GlobalXcentra_AION.zip" class="download-button"></a>
			
			<div class="connect-title"></div>
			<div class="right-text-container">
			<div style="overflow-y: scroll; height: 225px;">
				<!--<p><span style="color:#d6be93;font-size:17px;">Community Discord</span><br />
				1. Join Server <a href="https://discord.gg/4nwpdqJ4MM" target="_blank" class="alt">GlobalXcentra Discord</a><br /></p>-->
	
				<p><span style="color:#d6be93;font-size:17px;">Account Registration</span><br />
				Create your Account <a href="<?php module_url('register/'); ?>" class="alt">Click Here to Register</a></p>

				<p><span style="color:#d6be93;font-size:17px;">Download and Install Aion</span><br />
				1. Download Aion <a href="https://download.globalxcentra.net/GlobalXcentra_AION.zip" target="_blank" class="alt">Webspace</a><br />
				2. Download our Launcher <a href="https://download.globalxcentra.net/GlobalXcentra_Launcher.zip" target="_blank" class="alt">Here</a></p>

				<p><span style="color:#d6be93;font-size:17px;">Connecting to Aion</span><br />
				1. Open the AION Folder<br />
				2. Run GlobalXcentra Launcher to connect!</p>
			</div>
			</div>
		</div>
	</div>
</div>