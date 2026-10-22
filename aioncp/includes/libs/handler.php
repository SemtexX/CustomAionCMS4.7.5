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
	private static $lumiel;
	private static $sdb;
	private static $gabs;
	private static $aioncp;

	public static function loadModule(database $db, database $siel, database $lumiel, database $gabs, database $aioncp) {
		self::$db = $db;
		self::$siel = $siel;
		self::$lumiel = $lumiel;
		self::$gabs = $gabs;
		self::$aioncp = $aioncp;
		
		$sdb = ($_SESSION['aioncp']['server'] == 'siel' ? $siel : $lumiel);
		self::$sdb = $sdb;
		
		$module = (check(self::cleanModuleRequest($_GET['page'])) ? self::cleanModuleRequest($_GET['page']) : 'home');
		$submodule = self::cleanModuleRequest($_GET['subpage']);
		
		$request = explode("/", $_GET['request']);
		if(is_array($request)) {
			for($i = 0; $i < count($request); $i++) {
				if(check($request[$i])) {
					if(check($request[$i+1])) {
						//$_GET[$request[$i]] = filter_var($request[$i+1], FILTER_SANITIZE_STRING);
						$_GET[$request[$i]] = $request[$i+1];
					} else {
						$_GET[$request[$i]] = NULL;
					}
				}
				$i++;
			}
		}
		
		// Modules
		$modules = config('modules');
		
		// Modules Path
		$modulesPath = __PATH_MODULES__;
		
		if(check($submodule)) {
			// SUBMODULE
			if(array_key_exists($submodule, $modules[$module])) {
				$path = $modulesPath.$module.'/'.$submodule.'.php';
				if(file_exists($path)) {
					
					if($modules[$module][$submodule]['_access'] > $_SESSION['aioncp']['access_level']) throw new Exception('ACCESS FORBIDDEN');
					
					// title
					if(check($modules[$module][$submodule]['_title'])) {
						echo '<div class="content bg-gray-lighter">';
							echo '<div class="row items-push">';
								echo '<div class="col-sm-7">';
									echo '<h1 class="page-heading">'.$modules[$module][$submodule]['_title'].'</h1>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					}
					
					// content
					echo '<div class="content">';
						include($path);
					echo '</div>';
					$_SESSION['aioncp']['last_location'] = $module.'/'.$submodule;
				} else {
					echo '<div class="content">';
						include($modulesPath.'404.php');
					echo '</div>';
				}
			} else {
				echo '<div class="content">';
					include($modulesPath.'404.php');
				echo '</div>';
			}
		} else {
			// MODULE
			if(array_key_exists($module, $modules)) {
				if(file_exists($modulesPath.$module.'.php')) {
					
					if($modules[$module]['_access'] > $_SESSION['aioncp']['access_level']) throw new Exception('ACCESS FORBIDDEN');
					
					// title
					if(check($modules[$module]['_title'])) {
						echo '<div class="content bg-gray-lighter">';
							echo '<div class="row items-push">';
								echo '<div class="col-sm-7">';
									echo '<h1 class="page-heading">'.$modules[$module]['_title'].'</h1>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					}
					
					// content
					echo '<div class="content">';
						include($modulesPath.$module.'.php');
					echo '</div>';
					$_SESSION['aioncp']['last_location'] = $module;
				} else {
					echo '<div class="content">';
						include($modulesPath.'404.php');
					echo '</div>';
				}
			} else {
				echo '<div class="content">';
					include($modulesPath.'404.php');
				echo '</div>';
			}
		}
	}
	
	private static function cleanModuleRequest($input) {
		return preg_replace("/[^a-zA-Z0-9\s\/]/", "", $input);
	}
	
	public static function loadDB($req="") {
		switch($req) {
			case 'siel':
				return self::$siel;
				break;
			case 'lumiel':
				return self::$lumiel;
				break;
			case 'sdb':
				return self::$sdb;
				break;
			case 'gabs':
				return self::$gabs;
				break;
			case 'aioncp':
				return self::$aioncp;
				break;
			default:
				return self::$db;
		}
	}
	
	public static function userIP() {
		$ip = filter_input(INPUT_SERVER, "REMOTE_ADDR", FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
		if(!$ip) return "0.0.0.0";
		return $ip;
	}
	
	public static function buildNavbar() {
		$modules = config('modules');
		if(is_array($modules)) {
			
			echo '<ul class="nav-main">';
			
				foreach($modules as $module => $moduleData) {
					
					# FILTERS
					if(!is_array($moduleData)) continue;
					if($moduleData['_access'] > $_SESSION['aioncp']['access_level']) continue;
					if(array_key_exists('_hide', $moduleData) && $moduleData['_hide'] == true) continue;
					
					if(array_key_exists('_title', $moduleData)) {
						
						# UNCATEGORIZED MODULES (WITH ICONS)
						echo '<li><a class="active" href="'.__BASE_URL__ . $module . '/"><i class="si si-speedometer"></i><span class="sidebar-mini-hide">'.$moduleData['_title'].'</span></a></li>';
						
					} else {
						
						# GROUP TITLE
						echo '<li class="nav-main-heading"><span class="sidebar-mini-hide">'.$moduleData['_groupname'].'</span></li>';
						
						# SUB-MODULES
						foreach($moduleData as $submodule => $submoduleData) {
							if($submodule == '_groupname') continue;
							if($submodule == '_access') continue;
							if($submodule == '_icon') continue;
							
							if(array_key_exists('_hide', $submoduleData) && $submoduleData['_hide'] == true) continue;
							if(array_key_exists('_access', $submoduleData) && $submoduleData['_access'] > $_SESSION['aioncp']['access_level']) continue;
							
							echo '<li><a '.($submodule == $_GET['subpage'] ? 'class="active" ' : 'null').' href="'.__BASE_URL__ . $module . '/' . $submodule . '/"><span class="sidebar-mini-hide">'.$submoduleData['_title'].'</span></a></li>';
						}
					}
					
				}
			
			echo '</ul>';
			
		}
	}
	
	public static function buildNavbar_new() {
		$modules = config('modules');
		if(is_array($modules)) {
			
			echo '<ul class="nav-main">';
				
				echo '<li class="nav-main-heading"><span class="sidebar-mini-hide">AionCP Menu</span></li>';
				foreach($modules as $module => $moduleData) {
					
					# FILTERS
					if(!is_array($moduleData)) continue;
					if($moduleData['_access'] > $_SESSION['aioncp']['access_level']) continue;
					if(array_key_exists('_hide', $moduleData) && $moduleData['_hide'] == true) continue;
					if(array_key_exists('_title', $moduleData)) continue;
					
					echo (check($_GET['page']) && $_GET['page'] == $module ? '<li class="open">' : '<li>');
						echo '<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si '.$moduleData['_icon'].'"></i><span class="sidebar-mini-hide">'.$moduleData['_groupname'].'</span></a>';
						echo '<ul>';
						foreach($moduleData as $submodule => $submoduleData) {
							if($submodule == '_groupname') continue;
							if($submodule == '_access') continue;
							if($submodule == '_icon') continue;
							
							if(array_key_exists('_hide', $submoduleData) && $submoduleData['_hide'] == true) continue;
							if(array_key_exists('_access', $submoduleData) && $submoduleData['_access'] > $_SESSION['aioncp']['access_level']) continue;
							
							echo '<li><a '.($submodule == $_GET['subpage'] ? 'class="active" ' : 'null').' href="'.__BASE_URL__ . $module . '/' . $submodule . '/"><span class="sidebar-mini-hide">'.$submoduleData['_title'].'</span></a></li>';
						}
						echo '</ul>';
					echo '</li>';
					
				}
				
			echo '</ul>';
			
		}
	}
	
}