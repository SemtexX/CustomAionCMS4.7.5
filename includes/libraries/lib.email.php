<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class Email {
	
	private $_active = false;
	private $_smtp = false;
	
	private $_from;
	private $_name;
	private $_templates = array();
	private $_templatesPath = __PATH_EMAILS__;
	
	private $_smtpHost;
	private $_smtpPort;
	private $_smtpUser;
	private $_smtpPass;
	
	private $_template;
	private $_message;
	private $_to = array();
	private $_subject;
	private $_variables = array();
	private $_values = array();
	
	function __construct() {
		# set configurations
		$this->_active = config('email_active');
		$this->_from = config('email_send_from');
		$this->_name = config('email_send_name');
		
		$this->_smtp = config('smtp_active');
		$this->_smtpHost = config('smtp_host');
		$this->_smtpPort = config('smtp_port');
		$this->_smtpUser = config('smtp_user');
		$this->_smtpPass = config('smtp_pass');
		
		# load database
		$this->db = Handler::loadDB();
		
		# load templates
		$dbTemplates = $this->db->queryFetch("SELECT * FROM `aioncms`.`website_emails` WHERE `language` = ?", array(config('language')));
		if(!is_array($dbTemplates)) throw new Exception('Could not load emails.');
		
		# load templates list
		$templates = array();
		foreach($dbTemplates as $template) {
			$templates[$template['filename']] = $template['subject'];
		}
		
		# save templates
		$this->_templates = $templates;
		
		# phpmailer instance
		$this->mail = new PHPMailer();
		
	}
	
	public function test() {
		debug($this->_templates);
	}
	
	public function setMessage($message) {
		$this->_message = $message;
	}
	
	public function setTemplate($template) {
		if(!array_key_exists($template, $this->_templates)) throw new Exception("Could not load email template.");
		$this->_template = $template;
		$this->_subject = $this->_templates[$template];
	}
	
	public function addVariable($variable, $value) {
		$this->_variables[] = $variable;
		$this->_values[] = $value;
	}
	
	public function addAddress($email) {
		if(!Validator::Email($email)) throw new Exception("Email address invalid, cannot send email.");
		$this->_to[] = $email;
	}
	
	private function _loadTemplate() {
		if(!$this->_template) throw new Exception("You did not set a template.");
		if(!file_exists($this->_templatesPath . $this->_template . '.txt')) throw new Exception("Could not load email template.");
		return file_get_contents($this->_templatesPath . $this->_template . '.txt');
	}
	
	private function _prepareTemplate() {
		return str_replace($this->_variables, $this->_values, $this->_loadTemplate());
	}
	
	public function send() {
		if(!$this->_active) throw new Exception('Email system is not active.');
		
		if(!$this->_message) {
			if(!$this->_template) throw new Exception("You did not set a template.");
		}
		
		if(!is_array($this->_to)) throw new Exception("You did not add any address.");
		
		if($this->_smtp) {
			$this->mail->IsSMTP();
			$this->mail->SMTPAuth = true;
			$this->mail->Host = $this->_smtpHost;
			$this->mail->Port = $this->_smtpPort;
			$this->mail->Username = $this->_smtpUser;
			$this->mail->Password = $this->_smtpPass;
		}
		
		$this->mail->SetFrom($this->_from, $this->_name);
		
		foreach($this->_to as $address) {
			$this->mail->AddAddress($address);
		}
		
		$this->mail->Subject = $this->_subject;
		
		if(!$this->_message) {
			$this->mail->MsgHTML($this->_prepareTemplate());
		} else {
			$this->mail->MsgHTML($this->_message);
		}
		
		if($this->mail->Send()) return true;
		return false;
	}
	
}