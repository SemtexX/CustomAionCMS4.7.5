<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class ReferralSystem extends Account {
	
	protected $_kinahPerHour = 3600000;
	protected $_requiredOnlineHours;
	protected $_creditsReward;
	
	function __construct() {
		
		$this->db = Handler::loadDB();
		
		$this->_active = config('referral_active');
		$this->_requiredOnlineHours = config('referral_required_onlinetime_hours');
		$this->_creditsReward = config('referral_credits_reward');
	}
	
	public function setReferralId($userid) {
		if(!Validator::UnsignedNumber($userid)) return;
		$this->setId($userid);
	}
	
	public function saveReferral() {
		if(!check($this->_username)) return;
		if(!is_array($this->_accountData)) return;
		if($this->_isDuplicate()) return;
		
		$accountId = $this->_getAccountId();
		if(!check($accountId)) return;
		
		$result = $this->db->query("INSERT INTO `aioncms`.`referrals` (`username`, `userid`, `referral`, `join_date`) VALUES (?, ?, ?, now())", array($this->_username, $accountId, $this->_accountData['name']));
		if(!$result) return;
		return true;
	}
	
	public function checkProgress() {
		if(!$this->_active) return;
		$referralList = $this->_getPendingReferrals();
		if(!is_array($referralList)) return;
		
		foreach($referralList as $ref) {
			$totalOnlineTime = $this->_getTotalOnlineTime($ref['userid']);
			$onlineTimeToHours = sec_to_hms($totalOnlineTime);
			if(!is_array($onlineTimeToHours)) continue;
			if($onlineTimeToHours[0] >= $this->_requiredOnlineHours) {
				$Account = new Account();
				$Account->setUsername($ref['referral']);
				$Account->loadAccountDataFromUsername();
				
				// reward referrer
				if($Account->addCredits($this->_creditsReward)) {
					
					// update referral status
					$this->db->query("UPDATE `aioncms`.`referrals` SET status = ?, reward_date = now() WHERE `id` = ?", array(1, $ref['id']));
					
					debug('referral rewarded!!');
				}
			}
		}
		
		
	}
	
	public function getAccountReferrals() {
		if(!check($this->_username)) return;
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`referrals` WHERE `referral` = ?", array($this->_username));
		if(!is_array($result)) return;
		return $result;
	}
	
	public function getLogs() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`referrals` ORDER BY `id` DESC");
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _getAccountId() {
		if(!check($this->_username)) return;
		$result = $this->db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($this->_username));
		if(!is_array($result)) return;
		return $result['id'];
	}
	
	private function _isDuplicate() {
		if(!check($this->_username)) return;
		if(!is_array($this->_accountData)) return;
		$result = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`referrals` WHERE `username` = ? AND `referral` = ?", array($this->_username, $this->_accountData['name']));
		if(!is_array($result)) return;
		return true;
	}
	
	private function _getPendingReferrals() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`referrals` WHERE `status` = ?", array(0));
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _getTotalOnlineTime($userid) {
		if(!check($userid)) return;
		
		$totalOnlineTime = 0;
		
		// get online time from players_onlinetime table
		$playersOnlineTime = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`players_onlinetime` WHERE `account_id` = ?", array($userid));
		if(is_array($playersOnlineTime)) {
			$totalOnlineTime += $playersOnlineTime['total_onlinetime'];
		}
		
		// get online time from players_exchange_logs
		$playersExchangeLogs = $this->db->queryFetchSingle("SELECT `account_id`, SUM(`exchange_amount`) AS totalKinahExchanged FROM `aioncms`.`players_exchange_logs` WHERE `account_id` = ?", array($userid));
		if(is_array($playersExchangeLogs)) {
			$totalKinahExchanged = $playersExchangeLogs['totalKinahExchanged'];
			$totalOnlineHoursExchanged = floor($totalKinahExchanged/$this->_kinahPerHour);
			$totalOnlineSecondsExchanged = floor($totalOnlineHoursExchanged*3600);
			$totalOnlineTime += $totalOnlineSecondsExchanged;
		}
		
		return $totalOnlineTime;
	}

}