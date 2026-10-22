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
<div class="page-header-block login"></div>
<br /><br />

<h3>Account Login</h3>

<br />

<?php
# Login Process
if(check($_POST['login_submit'])) {
	try {
		$accountLogin = new Login();
		$accountLogin->setUsername(strtolower($_POST['login_username']));
		$accountLogin->setpassword($_POST['login_password']);
		$accountLogin->accountLogin();
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
		logSystem::add('failed login attempt ('.strtolower($_POST['login_username']).')');
		//$_SESSION['failed_logins'] = (check($_SESSION['failed_logins']) ? $_SESSION['failed_logins']+1 : 0);
	}
}
?>

<form action="<?php module_url('login/'); ?>" method="post">
<table class="login-form">
	<tr>
		<td>Username:</td>
		<td><input type="text" name="login_username" maxlength="25" autofocus/></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" name="login_password" /></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<div style="width: 300px;font-size: 12px;">
				<a href="<?php module_url('recovery/'); ?>">Forgot your username / password ?</a><br /><br />
				
			</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><button type="submit" name="login_submit" value="ok">Login</button></td>
	</tr>
</table>
</form>