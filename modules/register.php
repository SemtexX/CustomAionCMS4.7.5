<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<?php if(isLoggedIn()) redirect('usercp/'); ?>
<div class="page-header-block register"></div>
<br /><br />

<h3>Create an Account</h3>

<br />

<?php
# Login Process
if(check($_POST['register_submit'])) {
	try {
		if(!check($_POST['register_username'])) throw new Exception('Please complete all the fields in the registration form.');
		if(!check($_POST['register_password'])) throw new Exception('Please complete all the fields in the registration form.');
		if(!check($_POST['register_password2'])) throw new Exception('Please complete all the fields in the registration form.');
		if(!check($_POST['register_email'])) throw new Exception('Please complete all the fields in the registration form.');
		if($_POST['register_password'] != $_POST['register_password2']) throw new Exception('Passwords did not match.');
		
		$Register = new Register();
		$Register->setUsername($_POST['register_username']);
		$Register->setPassword($_POST['register_password']);
		$Register->setEmail($_POST['register_email']);
		if(check($_POST['referral'])) {
			$Register->setReferralId($_POST['referral']);
		}
		
		$Register->createAccount();
		message('<strong>Awesome!</strong> your account has been created successfully!<br />', 'success');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}
?>

<form action="<?php module_url(); ?>register/" method="post">
<?php
if(check($_GET['ref'])) {
	echo '<input type="hidden" name="referral" value="'.$_GET['ref'].'" />';
}
?>
<table class="login-form">
	<tr>
		<td>Username:</td>
		<td><input type="text" name="register_username" maxlength="25" required/></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" name="register_password" required/></td>
	</tr>
	<tr>
		<td>Confirm Password:</td>
		<td><input type="password" name="register_password2" required/></td>
	</tr>
	<tr>
		<td>Email:</td>
		<td><input type="text" name="register_email" required/></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<div style="width: 300px;font-size: 12px;">
				A valid email address is required to activate your account. <br /><br />
				By creating an account you automatically agree to our <a style="color: #ff5400;" href="<?php module_url('rules/'); ?>" target="_blank">terms of service</a>.
			</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><button type="submit" name="register_submit" value="ok">Register</button></td>
	</tr>
</table>
</form>