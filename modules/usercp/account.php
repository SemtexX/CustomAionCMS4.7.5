<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?><div class="page-header-block usercp"></div>
<br /><br />

<h3>My Account</h3>
<p>From here you will be able to manage your account's security. Remember to keep your information private, never share it with anyone.</p>

<?php
try {
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	$accountSecurity = $Account->getExtraSecurityData();
	
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	
	if($accountData['ip_force'] == 1)  {
		echo '<br />';
		message('Your account is currently banned, please check the ban system for more information.', 'error');
	}
	
	echo '<table class="my-account-table">';
		echo '<tr>';
			echo '<td>Account:</td>';
			echo '<td>'.$accountData['name'].' <a href="'.module_url('usercp/validate/', true).'">(validate)</a></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Password:</td>';
			echo '<td>&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226; <a href="'.module_url('usercp/password/', true).'">(change)</a></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Status:</td>';
			echo '<td>'.($accountData['activated'] == 1 ? 'Activated' : 'Pending Activation (email)').'</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Email:</td>';
			echo '<td>'.$accountData['email'].' <a href="'.module_url('usercp/email/', true).'">(request change)</a></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Registration Date:</td>';
			echo '<td>'.(check($accountData['created_at']) ? date("F jS, Y", strtotime($accountData['created_at'])) : '<i>Unknown</i>').'</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Account Type:</td>';
			echo '<td>'.$Account->accountTypeTxt($accountData['membership']).'</td>'; // 0= normal 1= premium 2= vip
		echo '</tr>';
		if($accountData['membership'] == 2) {
		echo '<tr>';
			echo '<td>VIP Expiration:</td>';
			echo '<td>'.date("F jS, Y", strtotime($accountData['expire'])).'</td>'; // vip expiration
		echo '</tr>';
		}
		echo '<tr>';
			echo '<td>Last IP:</td>';
			echo '<td>'.$accountData['last_ip'].'</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Credits:</td>';
			echo '<td>'.$accountData['toll'].' <a href="'.module_url('donate/', true).'">(Add Credits)</a></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>BattlePass:</td>';
			echo '<td><a href="'.module_url('usercp/battlepass/', true).'" class="btn btn-xs btn-primary">View BattlePass</a></td>';
		echo '</tr>';
	echo '</table>';
	
	$securityEmail = ($accountSecurity['email_confirmed'] == 1 ? '<span class="glyphicon glyphicon-ok" aria-hidden="true" style="color:green;"></span>' : '<a href="'.module_url('usercp/verifyemail/', true).'" class="btn btn-xs btn-primary">Verify Now</a>');
	$securityQuestions = (check($accountSecurity['question_1'], $accountSecurity['question_2'], $accountSecurity['answer_1'], $accountSecurity['answer_2']) ? '<span class="glyphicon glyphicon-ok" aria-hidden="true" style="color:green;"></span>' : '<a href="'.module_url('usercp/securityquestions/', true).'" class="btn btn-xs btn-primary">Configure</a>');
	$securityPin = (check($accountSecurity['security_pin']) ? '<span class="glyphicon glyphicon-ok" aria-hidden="true" style="color:green;"></span>' : '<a href="'.module_url('usercp/securitypin/', true).'" class="btn btn-xs btn-primary">Configure</a>');
	$securityLock = '<a href="'.module_url('usercp/accountlock/', true).'" class="btn btn-xs btn-primary">Configure</a>';
	
	echo '<table class="my-account-table">';
		echo '<tr>';
			echo '<td>Verify Email:</td>';
			echo '<td>'.$securityEmail.'</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Security Questions:</td>';
			echo '<td>'.$securityQuestions.'</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Security PIN:</td>';
			echo '<td>'.$securityPin.'</td>';
		echo '</tr>';
	echo '</table>';
	
	if(check($accountSecurity['security_pin']) || check($accountSecurity['question_1'], $accountSecurity['question_2'], $accountSecurity['answer_1'], $accountSecurity['answer_2'])) {
		echo '<table class="my-account-table">';
			echo '<tr>';
				echo '<td></td>';
				echo '<td><a href="'.module_url('usercp/reset/', true).'" class="btn btn-xs btn-danger">Reset Account Security</a></td>';
			echo '</tr>';
		echo '</table>';
	}
	
	echo '<div class="account-safety">';
		echo '<h4>Safety Tips:</h4>';
		echo '<ul>';
			echo '<li><span style="font-weight:bold;color: #0072ff;">Use a strong password.</span> Prevent people from guessing your password by making it at least 8 characters long, containing capital &amp; regular letters and numbers. Remember to never use the same password for multiple websites or services!</li>';
			echo '<li><span style="font-weight:bold;color: #0072ff;">Never give out your account id.</span> Our staff will never ask for your account id, we will only ask for your character name!</li>';
			echo '<li><span style="font-weight:bold;color: #0072ff;">Playing from an internet cafe?.</span> Always make sure the computer you are using has an antivirus/anti-malware installed and running. If it doesn\'t have one, we recommend using <a href="https://www.malwarebytes.org/" target="_blank">Malwarebytes</a>.</li>';
		echo '</ul>';
	echo '</div>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}

?>