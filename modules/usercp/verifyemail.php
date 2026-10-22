<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block accsecurity"></div>
<br /><br />

<h3>Verify Email Address</h3>
<p>Verifying your account's email address helps you keep your account more secure.</p>
<br />

<?php
try {
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	$accountSecurity = $Account->getExtraSecurityData();
	
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	if($accountSecurity['email_confirmed'] == 1) throw new Exception('Your email address is already verified.');
	
	if(check($_GET['send'])) {
		# send verification email
		try {
			
			$verificationCode = md5(md5('338nr72o3rcn8g32fx') . md5($_SESSION['username']));
			$verificationLink = __PAGE_URL__ . 'verification/email/key/' . $verificationCode . '/';
			
			$email = new Email();
			$email->setTemplate('VERIFY_EMAIL');
			$email->addVariable('{USERNAME}', $accountData['name']);
			$email->addVariable('{VERIFICATION_LINK}', $verificationLink);
			$email->addAddress($accountData['email']);
			$email->send();
			
		} catch(Exception $ex) {
			throw new Exception('We could not send you the verification email, please contact support.');
		}
		
		message('<strong>Almost done!</strong> We have sent a verification email to <strong>'.$accountData['email'].'</strong>. Once you click the link we sent you your email will be verified.', 'success');
		
		logSystem::add('requested email verification');
	}
	
	echo '<table class="my-account-table">';
		echo '<tr>';
			echo '<td>Email:</td>';
			echo '<td>'.$accountData['email'].'</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td><a href="'.module_url('usercp/verifyemail/send/1', true).'" class="btn btn-primary">Send Verification Email</a></td>';
		echo '</tr>';
	echo '</table>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>