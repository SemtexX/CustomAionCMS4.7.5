<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class Tickets {
	
	protected $_id;
	protected $_subject;
	protected $_username;
	protected $_message;
	
	protected $_subjectMinLen = 1;
	protected $_subjectMaxLen = 100;
	protected $_messageMinLen = 1;
	protected $_messageMaxLen = 1000;
	protected $_messageOrder = 'ASC';
	
	protected $_viewTicketPath = 'tickets/view/id/';
	
	function __construct() {
		
		
		// database object
		$this->db = Handler::loadDB();
		
		
	}
	
	public function setId($id) {
		if(!Validator::UnsignedNumber($id)) throw new Exception('The provided ticket id is not valid.');
		$this->_id = $id;
	}
	
	public function setSubject($subject) {
		if(!Validator::Length($subject, $this->_subjectMaxLen, $this->_subjectMinLen)) throw new Exception('The ticket subject exceeds the length limits.');
		$this->_subject = $subject;
	}
	
	public function setUsername($username) {
		//if(!Validator::AccountUsername($username)) throw new Exception('Invalid username.');
		$this->_username = $username;
	}
	
	public function setMessage($message) {
		if(!Validator::Length($message, $this->_messageMaxLen, $this->_messageMinLen)) throw new Exception('The ticket message exceeds the length limits.');
		$this->_message = $message;
	}
	
	public function getTicketData() {
		if(!check($this->_id)) return;
		$result = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`tickets` WHERE `id` = ?", array($this->_id));
		if(!is_array($result)) return;
		return $result;
	}
	
	public function getTicketMessages() {
		if(!check($this->_id)) return;
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`ticket_messages` WHERE `ticket_id` = ? ORDER BY `id` ".$this->_messageOrder."", array($this->_id));
		if(!is_array($result)) return;
		return $result;
	}
	
	public function submitTicket() {
		if(!check($this->_subject)) throw new Exception('Error submitting ticket, missing information.');
		if(!check($this->_message)) throw new Exception('Error submitting ticket, missing information.');
		if(!check($this->_username)) throw new Exception('Error submitting ticket, missing information.');
		
		// check if banned from using ticket system
		if($this->_isBannedFromTicketSystem()) throw new Exception('You have been banned from using the ticket support system.');
		
		$submitData = array(
			'subject' => $this->_subject,
			'username' => $this->_username
		);
		$submitTicket = $this->db->query("INSERT INTO `aioncms`.`tickets` (`subject`, `username`, `create_date`, `last_reply_by`, `last_reply_date`) VALUES (:subject, :username, now(), :username, now())", $submitData);
		if(!$submitTicket) throw new Exception('There was an error submitting your ticket, try again later.');
		
		$ticketId = $this->db->db->lastInsertId();
		if(!check($ticketId)) throw new Exception('There was an error retrieving the ticket id.');
		$this->setId($ticketId);
		
		$submitMessage = $this->submitMessage();
	}
	
	public function submitMessage() {
		if(!check($this->_id)) throw new Exception('Error submitting ticket, missing information.');
		if(!check($this->_message)) throw new Exception('Error submitting ticket, missing information.');
		if(!check($this->_username)) throw new Exception('Error submitting ticket, missing information.');
		
		// check if banned from using ticket system
		if($this->_isBannedFromTicketSystem()) throw new Exception('You have been banned from using the ticket support system.');
		
		$submitData = array(
			'id' => $this->_id,
			'message' => $this->_message,
			'username' => $this->_username
		);
		$submitMessage = $this->db->query("INSERT INTO `aioncms`.`ticket_messages` (`ticket_id`, `message`, `username`, `create_date`) VALUES (:id, :message, :username, now())", $submitData);
		if(!$submitMessage) throw new Exception('There was an error submitting your message, try again later.');
		
		$updateTicket = $this->db->query("UPDATE `aioncms`.`tickets` SET `last_reply_by` = ?, `last_reply_date` = now() WHERE `id` = ?", array($this->_username, $this->_id));
		if(!$updateTicket) throw new Exception('There was an error updating the ticket.');
	}
	
	public function closeTicket() {
		if(!check($this->_id)) return;
		$result = $this->db->query("UPDATE `aioncms`.`tickets` SET `closed` = 1 WHERE `id` = ?", array($this->_id));
		if(!$result) return;
		return $result;
	}
	
	public function openTicket() {
		if(!check($this->_id)) return;
		$result = $this->db->query("UPDATE `aioncms`.`tickets` SET `closed` = 0 WHERE `id` = ?", array($this->_id));
		if(!$result) return;
		return $result;
	}
	
	public function getAccountTickets() {
		if(!check($this->_username)) return;
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`tickets` WHERE `username` = ? ORDER BY `last_reply_date` DESC, `id` DESC", array($this->_username));
		if(!is_array($result)) return;
		return $result;
	}
	
	public function redirectToTicket() {
		if(!check($this->_id)) return;
		redirect($this->_viewTicketPath . $this->_id);
	}
	
	public function getOpenTickets() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`tickets` WHERE `closed` = ? ORDER BY `last_reply_date` DESC, `id` DESC", array(0));
		if(!is_array($result)) return;
		return $result;
	}
	
	public function getClosedTickets() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`tickets` WHERE `closed` = ? ORDER BY `last_reply_date` DESC, `id` DESC", array(1));
		if(!is_array($result)) return;
		return $result;
	}
	
	public function getAwaitingResponseTickets() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`tickets` WHERE `username` == `last_reply_by` AND `closed` = ? ORDER BY `last_reply_date` DESC, `id` DESC", array(0));
		if(!is_array($result)) return;
		return $result;
	}
	
	public function banFromTicketSystem() {
		if(!check($this->_username)) throw new Exception('Missing username value.');
		if($this->_isBannedFromTicketSystem()) throw new Exception('This account is already banned from using the ticket system.');
		$result = $this->db->query("INSERT INTO `aioncms`.`ticket_banned_accounts` (`username`) VALUES (?)", array($this->_username));
		if(!$result) throw new Exception('There was an error banning the account from using the ticket system.');
	}
	
	public function unbanFromTicketSystem() {
		if(!check($this->_username)) throw new Exception('Missing username value.');
		if(!$this->_isBannedFromTicketSystem()) throw new Exception('This account is not banned from using the ticket system.');
		$result = $this->db->query("DELETE FROM `aioncms`.`ticket_banned_accounts` WHERE `username` = ?", array($this->_username));
		if(!$result) throw new Exception('There was an error unbanning the account from using the ticket system.');
	}
	
	public function getBannedAccountsList() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`ticket_banned_accounts`");
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _isBannedFromTicketSystem() {
		if(!check($this->_username)) return;
		$result = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`ticket_banned_accounts` WHERE `username` = ?", array($this->_username));
		if(!is_array($result)) return;
		return true;
	}
	
}