<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class Handler {
	
	private static $db;
	private static $siel;
	private static $customModules = array(
		'home',
		'shop',
		'shop/list',
		'shop/item',
		'shop/history',
		'profile/legion'
	);
	
	public static function loadTemplate() {
		try {
			if(defined('access') or access) {
				if(access == "index") {
					self::loadTemplateIndex(config('template'));
				} elseif(access == "legionprofile") {
					self::loadTemplateIndex(config('template'));
				} elseif(access == "cron") {
					// do not load anything (for crons)
				} else {
					throw new Exception("No Access");
				}
			}
		} catch(Exception $ex) {
			die('[ERROR] '.$ex->getMessage());
		}
	}
	
	public static function loadModule($request) {
		$db = self::loadDB();
		
		$request = explode("/", $request);
		$request = array_filter($request); // remove empty values
		$_GET['module'] = (@check($request[0]) ? $request[0] : NULL);
		$_GET['submodule'] = "";
		
		if(count($request) > 1) {
			// Sub-Modules
			foreach($request as $reqKey => $thisReq) {
				if($reqKey != 0) {
					$parentModule = $request[$reqKey-1];
					$subModuleData = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_modules` WHERE file = ? AND parent = ? AND status IS TRUE", array($thisReq, $parentModule));
					if($subModuleData) {
						if($subModuleData['access'] == 2) {
							# check if logged in
							if(!isLoggedIn()) redirect('login/');
						}
						if(check($_GET['submodule'])) $_GET['submodule'] .= "/";
						$_GET['submodule'] .= $thisReq;
					} else {
						$parentModuleData = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_modules` WHERE file = ? AND parent IS NULL AND status IS TRUE", array($parentModule));
						if($parentModuleData['access'] == 2) {
							# check if logged in
							if(!isLoggedIn()) redirect('login/');
						}
						//$_GET['module'] = "404";
						//$_GET['submodule'] = "";
					}
				}
			}
			
			for($i = count(explode("/", $_GET['submodule']))+1; $i < count($request); $i++) {
				if(@check($request[$i])) {
					if(@check($request[$i+1])) {
						$_GET[$request[$i]] = filter_var($request[$i+1], FILTER_SANITIZE_STRING);
						$_GET[$request[$i]] = $request[$i+1];
					} else {
						$_GET[$request[$i]] = NULL;
					}
				}
				$i++;
			}
		} else {
			// Top Module
			$topModuleData = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_modules` WHERE file = ? AND parent IS NULL AND status IS TRUE", array($_GET['module']));
			if(check($_GET['module']) && !$topModuleData) {
				$_GET['module'] = "404";
			} else {
				if($topModuleData['access'] == 2) {
					# check if logged in
					if(!isLoggedIn()) redirect('login/');
				}
			}
		}
		
		$module = (check(self::cleanModuleRequest($_GET['module'])) ? self::cleanModuleRequest($_GET['module']) : 'home');
		$submodule = self::cleanModuleRequest($_GET['submodule']);
		
		# SESSION
		sessionControl::lastUserLocation($module.'/'.$submodule);
		if(!isLoggedIn()) {
			sessionControl::initSessionControl($db);
		} else {
			sessionControl::initSessionControl($db, "user");
		}
		
		// Modules Path
		$modulesPath = __PATH_MODULES__;
		
		if(check($submodule)) {
			$isCustom = (in_array($module.'/'.$submodule, self::$customModules) ? true : false);
			$path = $modulesPath.$module.'/'.$submodule.'.php';
			if(file_exists($path)) {
				self::loadPage($path, $isCustom);
			} else {
				self::loadPage($modulesPath.'404.php');
			}
		} else {
			$isCustom = (in_array($module, self::$customModules) ? true : false);
			if(file_exists($modulesPath.$module.'.php')) {
				self::loadPage($modulesPath.$module.'.php', $isCustom);
			} else {
				self::loadPage($modulesPath.'404.php');
			}
		}
	}
	
	private static function loadPage($path, $isCustom=false) {
		if($isCustom) {
			include($path);
		} else {
			echo '<div class="main-content">';
				echo '<div class="main-content-container">';
					echo '<div class="main-sidebar">';
					
						if(isLoggedIn()) {
							# usercp 
							echo '<div class="usercp-block">';
								echo '<div class="container">';
									$usercpSidebarMenu = config('usercp_sidebar_menu', true);
									if(is_array($usercpSidebarMenu)) {
										foreach($usercpSidebarMenu as $usercpSidebarElement) {
											echo '<div class="item">';
												echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/'.$usercpSidebarElement[2].'" /></div>';
												echo '<div class="itemlink"><a href="'.module_url($usercpSidebarElement[1], true).'">'.$usercpSidebarElement[0].'</a></div>';
											echo '</div>';
											echo '<div class="separator"></div>';
										}
									}
								echo '</div>';
							echo '</div>';
							
							echo '<div class="usercp-loggedin-block">';
								echo 'Welcome back <strong>'.$_SESSION['username'].'</strong>!';
								echo '<br />';
								if($_SESSION['is_staff'] == true) echo '<a href="'.__BASE_URL__.'aioncp/" target="_blank" style="background:#000;color:#ff0000;font-weight:bold;">aioncp</a> ';
								echo '<a href="'.module_url('usercp/', true).'">usercp</a> ';
								echo '<a href="'.module_url('logout/', true).'">logout</a>';
							echo '</div>';
						} else {
							# login box
							echo '<div class="login-box">';
								echo '<form action="'.module_url('', true).'login/" method="post">';
								echo '<input type="text" class="login-username" maxlength="25" name="login_username" autofocus/>';
								echo '<input type="password" class="login-password" name="login_password"/><br />';
								echo '<button type="submit" class="login-submit" name="login_submit" value="ok"></button>';
							echo '</form>';
							echo '</div>';
						}
						
						echo '<div class="sidebar-block">';
							echo '<a href="https://download.globalxcentra.net/GlobalXcentra_AION.zip" class="sidebar-download"></a>';
						echo '</div>';
						echo '<div class="sidebar-block">';
							//echo '<a href="https://aioncms.com/" target="_blank" class="sidebar-enchant"></a>';
						echo '</div>';
						
					echo '</div>';
					echo '<div class="main-page-container">';
						include($path);
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}
	}
	
	private static function loadTemplateIndex($input) {
		if(check($input) && file_exists(__PATH_TEMPLATES__.$input.'/index.php')) {
			if(!@include_once(__PATH_INCLUDES__.'template.functions.php')) throw new Exception('Could not load template functions.');
			include(__PATH_TEMPLATES__.$input.'/index.php');
		} else {
			throw new Exception('Could not load template.');
		}
	}
	
	private static function cleanModuleRequest($input) {
		return preg_replace("/[^a-zA-Z0-9\s\/]/", "", $input);
	}
	
	public static function loadDB($database="") {
		switch($database) {
			case 'siel':
				$siel = new database(config('DB_HOST'), config('DB_USER'), config('DB_PASS'), config('DB_NAME_2'), config('DB_PORT'));
				if($siel->offline) throw new Exception("Maintenance. [II]");
				return $siel;
				break;
			case 'lumiel':
				$siel = new database(config('DB_HOST'), config('DB_USER'), config('DB_PASS'), config('DB_NAME_2'), config('DB_PORT'));
				if($siel->offline) throw new Exception("Maintenance. [II]");
				return $siel;
				break;
			default:
				$db = new database(config('DB_HOST'), config('DB_USER'), config('DB_PASS'), config('DB_NAME'), config('DB_PORT'));
				if($db->offline) throw new Exception('Maintenance. [I]');
				return $db;
		}
	}
	
	public static function userIP() {
		//$ip = filter_input(INPUT_SERVER, "REMOTE_ADDR", FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
		$ip = $_SERVER['REMOTE_ADDR'];
		if(!$ip) return "0.0.0.0";
		return $ip;
	}
}