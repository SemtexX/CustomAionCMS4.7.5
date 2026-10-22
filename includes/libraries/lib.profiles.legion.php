<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


class LegionProfile {
	
	private $_profilesTable = '`aioncms`.`profiles_legion`';
	private $_id;
	private $_allowedColors = array('gray','red','green','blue');
	private $_customMessageMaxLen = 250;
	private $_allowedImageExtensions = array('jpg','jpeg','gif','png');
	
	private $_customColor;
	private $_customMessage;
	private $_youtubeVideoId;
	private $_customBackground;
	
	function __construct() {
		
		// load database
		$this->db = Handler::loadDB();
		$this->sdb = Handler::loadDB('siel');
		
	}
	
	public function setId($id) {
		$this->_id = $id;
	}
	
	public function setCustomColor($color) {
		if(!in_array($color, $this->_allowedColors)) throw new Exception('The selected color is not valid.');
		$this->_customColor = $color;
	}
	
	public function setCustomMessage($message) {
		if(!Validator::Length($message, $this->_customMessageMaxLen, 1)) throw new Exception('The custom message exceeds the length limits.');
		$this->_customMessage = $message;
	}
	
	public function setYoutubeVideo($url) {
		$videoId = getYouTubeVideoIdFromUrl($url);
		if(!check($videoId)) throw new Exception('The provided YouTube video url is not valid.');
		$this->_youtubeVideoId = $videoId;
	}
	
	public function setCustomBackground($url) {
		$imageExtension = getImageExtensionFromUrl($url);
		if(!check($imageExtension)) throw new Exception('The provided custom background image url is not valid.');
		if(!in_array($imageExtension, $this->_allowedImageExtensions)) throw new Exception('The provided custom background image url is not valid.');
		$this->_customBackground = $url;
	}
	
	public function saveProfile() {
		if(!check($this->_id)) throw new Exception('Could not save changes, missing legion id.');
		
		$data['id'] = $this->_id;
		$query = "UPDATE ".$this->_profilesTable." SET ";
		if(check($this->_customColor)) {
			$query .= " `custom_color` = :color";
			$data['color'] = $this->_customColor;
		}
		if(check($this->_customMessage)) {
			$query .= ", `custom_message` = :message";
			$data['message'] = $this->_customMessage;
		} else {
			$query .= ", `custom_message` = null";
		}
		if(check($this->_youtubeVideoId)) {
			$query .= ", `youtube_video` = :video";
			$data['video'] = $this->_youtubeVideoId;
		} else {
			$query .= ", `youtube_video` = null";
		}
		if(check($this->_customBackground)) {
			$query .= ", `new_background` = :background";
			$query .= ", `requires_approval` = 1";
			$data['background'] = $this->_customBackground;
		}
		$query .= " WHERE `id` = :id";
		
		$result = $this->db->query($query, $data);
		if(!check($result)) throw new Exception('There was an issue updating your legion\'s profile, contact support.');
	}
	
	public function getProfileInfo() {
		if(!check($this->_id)) throw new Exception('Could not load profile, request is not complete.');
		
		// load info
		$profileInfo = $this->_loadProfileInfo();
		
		// doesnt exist, create it
		if(!is_array($profileInfo)) {
			if(!$this->_createProfileInfo()) throw new Exception('There was an error creating the profile, contact support.');
			$profileInfo = $this->_loadProfileInfo();
		}
		
		// still doesnt exist, error
		if(!is_array($profileInfo)) throw new Exception('Could not load profile, contact support.');
		
		// check cache
		if(!$this->_cacheExists()) {
			$this->_cacheData();
			$this->_updateLastCacheDate();
		}
		
		// load cache
		$cacheData = $this->_loadCache();
		if(!is_array($cacheData)) return;
		
		// add profile info
		$cacheData['profile'] = array(
			'custom_message' => $profileInfo['custom_message'],
			'custom_background' => $profileInfo['custom_background'],
			'custom_font' => $profileInfo['custom_font'],
			'custom_color' => $profileInfo['custom_color'],
			'youtube_video' => $profileInfo['youtube_video'],
			'requires_approval' => $profileInfo['requires_approval'],
			'banned' => $profileInfo['banned']
		);
		
		return $cacheData;
	}
	
	public function getPendingApprovalList() {
		$result = $this->db->queryFetch("SELECT * FROM ".$this->_profilesTable." WHERE `requires_approval` = 1");
		if(!is_array($result)) return;
		return $result;
	}
	
	public function approveRequest() {
		if(!check($this->_id)) return;
		$result = $this->db->query("UPDATE ".$this->_profilesTable." SET `custom_background` = `new_background`, `new_background` = null, `requires_approval` = 0 WHERE `id` = ?", array($this->_id));
		if(!$result) return;
		return true;
	}
	
	public function denyRequest() {
		if(!check($this->_id)) return;
		$result = $this->db->query("UPDATE ".$this->_profilesTable." SET `new_background` = null, `requires_approval` = 0 WHERE `id` = ?", array($this->_id));
		if(!$result) return;
		return true;
	}
	
	private function _loadProfileInfo() {
		if(!check($this->_id)) return;
		$result = $this->db->queryFetchSingle("SELECT * FROM ".$this->_profilesTable." WHERE `id` = ?", array($this->_id));
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _createProfileInfo() {
		if(!check($this->_id)) return;
		$result = $this->db->query("INSERT INTO ".$this->_profilesTable." (`id`) VALUES (?)", array($this->_id));
		if(!$result) return;
		return true;
	}
	
	private function _getLegionInfo() {
		if(!check($this->_id)) return;
		$result = $this->sdb->queryFetchSingle("SELECT * FROM `legions` WHERE `id` = ?", array($this->_id));
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _getLegionMemberList() {
		if(!check($this->_id)) return;
		$result = $this->sdb->queryFetch("SELECT `players`.`id`, `players`.`name`, `players`.`exp`, `players`.`race`, `players`.`player_class`, `players`.`gender`, `legion_members`.`rank`  FROM `legion_members` INNER JOIN `players` ON `legion_members`.`player_id` = `players`.`id` WHERE `legion_members`.`legion_id` = ?", array($this->_id));
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _restructureLegionMemberListArray($data) {
		if(!is_array($data)) return;
		$result = array();
		foreach($data as $row) {
			$result[$row['rank']][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'exp' => $row['exp'],
				'race' => $row['race'],
				'player_class' => $row['player_class'],
				'gender' => $row['gender'],
				'rank' => $row['rank']
			);
		}
		
		return $result;
	}
	
	private function _loadCache() {
		if(!check($this->_id)) return;
		$filePath = __PATH_LEGION_PROFILES_CACHE__ . $this->_id . '.cache';
		$cache = file_get_contents($filePath);
		if(!check($cache)) return;
		$result = json_decode($cache, true);
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _cacheExists() {
		if(!check($this->_id)) return;
		$filePath = __PATH_LEGION_PROFILES_CACHE__ . $this->_id . '.cache';
		if(!file_exists($filePath)) return;
		return true;
	}
	
	private function _cacheData() {
		if(!check($this->_id)) return;
		
		$legionInfo = $this->_getLegionInfo();
		$legionMemberList = $this->_getLegionMemberList();
		$legionMemberListFinal = $this->_restructureLegionMemberListArray($legionMemberList);
		
		$cacheData = array(
			'name' => $legionInfo['name'],
			'level' => $legionInfo['level'],
			'contribution_points' => $legionInfo['contribution_points'],
			'members' => $legionMemberListFinal
		);
		
		$cacheDataJson = json_encode($cacheData);
		if(!check($cacheDataJson)) return;
		
		$filePath = __PATH_LEGION_PROFILES_CACHE__ . $this->_id . '.cache';
		$fp = fopen($filePath, 'w');
		fwrite($fp, $cacheDataJson);
		fclose($fp);
	}
	
	private function _updateLastCacheDate() {
		if(!check($this->_id)) return;
		$result = $this->db->query("UPDATE ".$this->_profilesTable." SET `last_cache` = now() WHERE `id` = ?", array($this->_id));
		if(!$result) return;
		return true;
	}
	
}