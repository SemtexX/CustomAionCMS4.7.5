<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class itemEnchantment {
	
	protected $_username;
	protected $_server;
	protected $_character;
	protected $_userTokens = array(
		'regular' => 0,
		'magic' => 0,
		'ultra' => 0
	);
	protected $_selectedToken;
	public $itemList = array();
	
	function __construct() {
		$this->db = Handler::loadDB();
		$this->sdb = null;
		
	}
	
	public function setUsername($username) {
		if(!check($username)) throw new Exception('Invalid username.');
		
		$this->_username = $username;
		$this->_loadUserTokens();
	}
	
	public function setToken($token) {
		if(!check($token)) throw new Exception('The selected token is not valid.');
		if(!array_key_exists($token, $this->_userTokens)) throw new Exception('The selected token is not valid [2].');
		if($this->_userTokens[$token] < 1) throw new Exception('You don\'t have enough tokens.');
		
		$this->_selectedToken = $token;
	}
	
	public function getToken() {
		if(!check($this->_selectedToken)) return;
		return $this->_selectedToken;
	}
	
	private function _loadUserTokens() {
		if(!check($this->_username)) return;
		
		$getTokens = $this->db->queryFetchSingle("SELECT * FROM `enchantment_tokens` WHERE `name` = ?", array($this->_username));
		if(!is_array($getTokens)) return;
		
		$this->_userTokens['regular'] = $getTokens['regular_tokens'];
		$this->_userTokens['magic'] = $getTokens['magic_tokens'];
		$this->_userTokens['ultra'] = $getTokens['ultra_tokens'];
	}
	
	public function getUserTokens($token="") {
		if(check($token)) {
			if(!array_key_exists($token, $this->_userTokens)) throw new Exception('The selected token is not valid [3].');
			return $this->_userTokens[$token];
		} else {
			return $this->_userTokens;
		}
	}
	
	public function setServer($server) {
		if(!check($server)) return;
		$allowed = array('siel', 'lumiel');
		if(!in_array($server, $allowed)) return;
		
		if($server == 'siel') {
			$this->sdb = Handler::loadDB('siel');
		} else {
			$this->sdb = Handler::loadDB('lumiel');
		}
		
		$this->_server = $server;
	}
	
	public function setCharacter($character) {
		if(!check($character)) throw new Exception('The character yo selected is not valid [N].');
		if(!check($this->_server)) throw new Exception('The character yo selected is not valid [SRV].');
		
		$characterInfo = $this->sdb->queryFetchSingle("SELECT * FROM `players` WHERE `account_id` = ? AND `name` = ?", array($_SESSION['userid'], $character));
		if(!is_array($characterInfo)) throw new Exception('The character you selected is not valid.');
		
		$this->_character = $character;
	}
	
	# STEP 1
	public function screen_tokenSelection() {
		
		$regularTokenLink = ($this->_userTokens['regular'] >= 1 ? module_url('usercp/enchant/t/regular/', true) : '#');
		$magicTokenLink = ($this->_userTokens['magic'] >= 1 ? module_url('usercp/enchant/t/magic/', true) : '#');
		$ultraTokenLink = ($this->_userTokens['ultra'] >= 1 ? module_url('usercp/enchant/t/ultra/', true) : '#');
		
		$regularTokenImg = ($this->_userTokens['regular'] >= 1 ? '' : ' none');
		$magicTokenImg = ($this->_userTokens['magic'] >= 1 ? ' magic' : ' none');
		$ultraTokenImg = ($this->_userTokens['ultra'] >= 1 ? ' ultra' : ' none');
		
		echo '<br /><br />';
		echo '<h4 class="text-center">Select a token:</h4>';
		echo '<div class="enchantment-selecttoken">';
			echo '<div class="enchantment-tokencontainer">';
				echo '<div class="enchantment-tokenc">';
					echo '<a href="'.$regularTokenLink.'" class="enchantment-token'.$regularTokenImg.'" data-toggle="tooltip" data-placement="top" title="Regular Token"></a>';
					echo '<br /><span class="enchantment-token-qty">x '.$this->_userTokens['regular'].'</span>';
				echo '</div>';
				echo '<div class="enchantment-tokenc">';
					echo '<a href="'.$magicTokenLink.'" class="enchantment-token'.$magicTokenImg.'" data-toggle="tooltip" data-placement="top" title="Magic Token"></a>';
					echo '<br /><span class="enchantment-token-qty">x '.$this->_userTokens['magic'].'</span>';
				echo '</div>';
				echo '<div class="enchantment-tokenc">';
					echo '<a href="'.$ultraTokenLink.'" class="enchantment-token'.$ultraTokenImg.'" data-toggle="tooltip" data-placement="top" title="Ultra Token"></a>';
					echo '<br /><span class="enchantment-token-qty">x '.$this->_userTokens['ultra'].'</span>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
		echo '<br /><br />';
		echo '<div class="col-md-6 col-md-offset-3 text-center">';
			echo '<a href="'.module_url('usercp/tokens/', true).'" class="btn btn-primary">Get Tokens!</a>';
			
			echo '<br /><br />';
			echo '<p style="color:red;font-size:11px;">Your items must be amplified.<br />Your character must be offline.</p>';
		echo '</div>';
	}
	
	# STEP 2
	public function screen_characterSelection() {
		$sdb = Handler::loadDB('siel');
		$ldb = Handler::loadDB('lumiel');
		
		$sielCharacters = $sdb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($_SESSION['userid']));
		$lumielCharacters = $ldb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($_SESSION['userid']));
		
		if(!is_array($sielCharacters) && !is_array($lumielCharacters)) {
			throw new Exception('Looks like you don\'t have any characters yet.');
		}
		
		echo '<br /><br />';
		echo '<div class="col-md-6 col-md-offset-3 text-center">';
			echo '<h4>Token:</h4>';
				echo '<img src="'.template_img(true).'token_'.$this->_selectedToken.'.png" data-toggle="tooltip" data-placement="top" title="'.ucfirst($this->_selectedToken).' Token"/>';
			echo '<br /><br />';
			
			echo '<h4>Select a character:</h4>';
			if(is_array($sielCharacters)) {
				foreach($sielCharacters as $player) {
					echo '<a href="'.module_url('usercp/enchant/t/'.$_GET['t'].'/s/siel/c/' . $player['name'], true).'">'.$player['name'].'</a><br />';
				}
			}
			if(is_array($lumielCharacters)) {
				foreach($lumielCharacters as $player) {
					echo '<a href="'.module_url('usercp/enchant/t/'.$_GET['t'].'/s/lumiel/c/' . $player['name'], true).'">'.$player['name'].'</a><br />';
				}
			}
		echo '</div>';
	}
	
	# STEP 3
	public function screen_itemSelection() {
		
		if(!check($this->_selectedToken)) throw new Exception('Your request is not complete.');
		if(!check($this->_server)) throw new Exception('Your request is not complete [2].');
		if(!check($this->_character)) throw new Exception('Your request is not complete [2].');
		
		$playerData = $this->sdb->queryFetchSingle("SELECT * FROM `players` WHERE `account_id` = ? AND `name` = ?", array($_SESSION['userid'], $this->_character));
		if(!is_array($playerData)) throw new Exception('There was an error, please contact the admin [1].');
		
		# load inventory
		$this->_loadPlayerInventory($playerData['id']);
		
		if(!is_array($this->itemList)) throw new Exception('No items available for enchanting.');
		
		if(check($_POST['enchant_submit'])) {
			try {
				
				if($this->_enchantProcess($playerData['id'], $_POST['item'])) {
					message('<strong>Yay!</strong> your item was successfully enchanted!', 'success');
				} else {
					message('<strong>Aww!</strong> your item failed the enchantment!', 'warning');
				}
				
				# reload inventory
				$this->_loadPlayerInventory($playerData['id']);
				
			} catch(Exception $ex) {
				message($ex->getMessage(), 'error');
			}
			
		}
		
		echo '<br /><br />';
		echo '<div class="col-md-6 col-md-offset-3 text-center">';
			echo '<h4>Token:</h4>';
				echo '<img src="'.template_img(true).'token_'.$this->_selectedToken.'.png" data-toggle="tooltip" data-placement="top" title="'.ucfirst($this->_selectedToken).' Token"/>';
			echo '<br /><br />';
			
			echo '<h4>Character:</h4>';
				echo '<p>'.$this->_character.'</p>';
			echo '<br /><br />';
			
			echo '<h4>Select item to enchant:</h4>';
			echo '<form action="" method="post">';
				echo '<div class="form-group">';
					echo '<select class="form-control" name="item">';
						foreach($this->itemList as $selectItem) {
							echo '<option value="'.$selectItem[0].'">'.$selectItem[1].' +'.$selectItem[2].'</option>';
						}
					echo '</select>';
				echo '</div>';
				
				echo '<br />';
				echo '<button type="submit" class="btn btn-primary" name="enchant_submit" value="1">Enchant Item!</button>';
			echo '</form>';
		echo '</div>';
	}
	
	private function _loadPlayerInventory($playerId) {
		if(!check($playerId)) return;
		
		$this->itemList = array(); # clean list
		
		$playerInventory = $this->sdb->queryFetch("SELECT * FROM `inventory` WHERE `item_owner` = ? AND `is_amplified` = 1 AND `enchant` < 19", array($playerId));
		if(!is_array($playerInventory)) throw new Exception('You don\'t have any items in this character.');
		
		foreach($playerInventory as $item) {
			$itemName = getItemName($item['item_id']);
			if(!check($itemName)) continue;
			$this->itemList[] = array($item['item_unique_id'], $itemName, $item['enchant']);
		}
		
	}
	
	private function _tokenResult() {
		if(!check($this->_selectedToken)) return;
		
		switch($this->_selectedToken) {
			case 'regular':
				$successRate = 90;
				break;
			case 'magic':
				$successRate = 70;
				break;
			case 'ultra':
				$successRate = 100;
				break;
			default:
				return;
		}
		
		$randomValue = mt_rand(0, 100);
		
		if($randomValue > $successRate) return false;
		return true;
	}
	
	private function _enchantProcess($playerId, $itemUid) {
		if(!check($playerId)) throw new Exception('Missing information.');
		if(!check($itemUid)) throw new Exception('Missing information.');
		if(!check($this->_selectedToken)) throw new Exception('Missing information.');
		if(!check($this->_server)) throw new Exception('Missing information.');
		if(!check($this->_character)) throw new Exception('Missing information.');
		
		# load player data
		$playerData = $this->_playerData($playerId);
		if(!is_array($playerData)) throw new Exception('There was an error, please contact the administrator. [E16]');
		
		# check online status
		if($playerData['online'] == 1) throw new Exception('Please disconnect from your character.');
		
		# load item info
		$itemData = $this->_itemData($itemUid);
		if(!is_array($itemData)) throw new Exception('There was an error, please contact the administrator. [E12]');
		
		# check item owner
		if($itemData['item_owner'] != $playerId) throw new Exception('There was an error, please contact the administrator. [E13]');
		
		# is the item amplified?
		if($itemData['is_amplified'] != 1) throw new Exception('This item cannot be enchanted!');
		
		# check enchantment level
		if($this->_selectedToken == 'regular') {
			if($itemData['enchant'] >= 10) throw new Exception('You can\'t use a regular token to enchant this item.');
		} elseif($this->_selectedToken == 'magic') {
			if($itemData['enchant'] < 10) throw new Exception('You can\'t use a magic token to enchant this item.');
			if(($itemData['enchant']+3) > 19) throw new Exception('This item cannot be enchanted.');
		}
		
		# check tracker
		$checkTracker = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`enchant_tracker` WHERE `item_uid` = ?", array($itemUid));
		if(is_array($checkTracker)) throw new Exception('This item has been already enchanted with an ultra token, disconnect from your character to get it re-enchanted automatically!');
		
		# subtract token
		if(!$this->_subtractToken(1)) throw new Exception('Could not subtract token.');
		
		# token result
		$tokenResult = $this->_tokenResult();
		
		if($tokenResult) {
			# success
			switch($this->_selectedToken) {
				case 'regular':
					$newEnchantLevel = $itemData['enchant'] + 1;
					$enchantItem = $this->_enchantItem($playerId, $itemUid, $newEnchantLevel);
					if(!$enchantItem) throw new Exception('Could not enchant item. [E14]');
					
					logSystem::add('enchanted item [R]['.$itemUid.']['.$newEnchantLevel.']');
					break;
				case 'magic':
					$newEnchantLevel = $itemData['enchant'] + 3;
					$enchantItem = $this->_enchantItem($playerId, $itemUid, $newEnchantLevel);
					if(!$enchantItem) throw new Exception('Could not enchant item. [E15]');
					
					logSystem::add('enchanted item [M]['.$itemUid.']['.$newEnchantLevel.']');
					break;
				case 'ultra':
					$newEnchantLevel = 19;
					
					$addTracker = $this->db->query("INSERT INTO `aioncms`.`enchant_tracker` (`item_id`, `item_uid`, `player_name`) VALUES (?, ?, ?)", array($itemData['item_id'], $itemUid, $playerId));
					if(!$addTracker) throw new Exception('Could not enchant item. [E19]');
					
					$enchantItem = $this->_enchantItem($playerId, $itemUid, $newEnchantLevel);
					if(!$enchantItem) throw new Exception('Could not enchant item. [E17]');
					
					break;
				default:
					return;
			}
			
			return true;
		} else {
			# fail
			if($this->_selectedToken == 'magic') {
				# get item back to +10
				$enchantItem = $this->_enchantItem($playerId, $itemUid, 10);
				if(!$enchantItem) throw new Exception('Could not enchant item. [E11]');
				
				logSystem::add('enchanted item [M]['.$itemUid.'][FAILED]');
			} else {
				logSystem::add('enchanted item [R]['.$itemUid.'][FAILED]');
			}
			
			return;
		}
		
	}
	
	private function _subtractToken($quantity) {
		if(!check($this->_selectedToken)) return;
		if(!check($this->_username)) return;
		
		if($this->_userTokens[$this->_selectedToken] < $quantity) return;
		
		switch($this->_selectedToken) {
			case 'regular':
				$subtract = $this->db->query("UPDATE `enchantment_tokens` SET `regular_tokens` = `regular_tokens` - ? WHERE `name` = ?", array($quantity, $this->_username));
				break;
			case 'magic':
				$subtract = $this->db->query("UPDATE `enchantment_tokens` SET `magic_tokens` = `magic_tokens` - ? WHERE `name` = ?", array($quantity, $this->_username));
				break;
			case 'ultra':
				$subtract = $this->db->query("UPDATE `enchantment_tokens` SET `ultra_tokens` = `ultra_tokens` - ? WHERE `name` = ?", array($quantity, $this->_username));
				break;
			default:
				return;
		}
		
		if(!$subtract) return;
		return true;
		
	}
	
	private function _enchantItem($playerId, $itemUid, $enchantmentLevel) {
		if(!check($playerId)) return;
		if(!check($itemUid)) return;
		if(!check($enchantmentLevel)) return;
		
		$editItem = $this->sdb->query("UPDATE `inventory` SET `enchant` = ? WHERE `item_unique_id` = ? AND `item_owner` = ?", array($enchantmentLevel, $itemUid, $playerId));
		if(!$editItem) return;
		
		return true;
	}
	
	private function _itemData($itemUid) {
		if(!check($itemUid)) return;
		
		$itemData = $this->sdb->queryFetchSingle("SELECT * FROM `inventory` WHERE `item_unique_id` = ?", array($itemUid));
		if(!is_array($itemData)) return;
		
		return $itemData;
	}
	
	private function _playerData($playerId) {
		if(!check($playerId)) return;
		
		$playerData = $this->sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($playerId));
		if(!is_array($playerData)) return;
		
		return $playerData;
	}
	
}