<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block changepwd"></div>
<br /><br />

<h3>Change Password</h3>

<br />

<?php

if(!check($_GET['key'])) redirect();
if(!Validator::UnsignedNumber($_GET['key'])) redirect();
if(time() > ($_GET['key']+3600)) redirect(); // 60 minutes already passed, key is not valid anymore

# Change Password Process
if(check($_POST['pwd_username'])) {
	
	try {
		
		$Account = new Account();
		$Account->setUsername($_POST['pwd_username']);
		$Account->loadAccountDataFromUsername();
		$accountData = $Account->getAccountData();
		$extraSecurityData = $Account->getExtraSecurityData();
		
		if(!is_array($accountData)) throw new Exception('There was an error changing your password, please contact support. [1]');
		
		# check if has new password saved
		if(!check($accountData['hash'])) throw new Exception('There was an error changing your password, please contact support. [2]');
		if(!check($accountData['confirmed'])) throw new Exception('There was an error changing your password, please contact support. [3]');
		
		# compare
		if($_GET['key'] != $accountData['hash']) throw new Exception('There was an error changing your password, please contact support. [4]');
		
		# Extra Security
		if(is_array($extraSecurityData)) {
			
			# HAS EXTRA SECURITY
			
			if($extraSecurityData['email_confirmed'] != 1) {
				# activate account
				$Account->activateAccount();
			}
			
			if(check($extraSecurityData['security_pin'])) {
				
				# verify using security pin
				if(check($_POST['pwd_pin'], $_POST['new_password'], $_POST['new_password2'])) {
					try {
						if($_POST['pwd_pin'] != $extraSecurityData['security_pin']) throw new Exception('The security PIN entered is not valid.');
						if($_POST['new_password'] != $_POST['new_password2']) throw new Exception('The new passwords did not match.');
						
						# set new password
						$Account->setPassword($_POST['new_password']);
						
						# PIN is correct, change password
						if(!$Account->changePassword()) throw new Exception('There was an error changing your password, please contact support. [5]');
						
						message('Your password has been successfully changed.', 'success');
						logSystem::add('password recovered (pin verification)');
						
						$disablePinForm = true;
					} catch(Exception $ex) {
						message($ex->getMessage(), 'error');
					}
				}
				if($disablePinForm != true) {
					echo '<form action="'.module_url('verification/forgotpassword/key/'.$_GET['key'].'/', true).'" method="post">';
					echo '<input type="hidden" name="pwd_username" value="'.$_POST['pwd_username'].'"/>';
					echo '<table class="login-form">';
						echo '<tr>';
							echo '<td>New Password:</td>';
							echo '<td><input type="password" name="new_password" autofocus/></td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Confirm New Password:</td>';
							echo '<td><input type="password" name="new_password2" /></td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td>Security PIN:</td>';
							echo '<td><input type="text" name="pwd_pin" maxlength="4" /></td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td></td>';
							echo '<td><button type="submit" name="pwd_submit" value="ok">Verify</button></td>';
						echo '</tr>';
					echo '</table>';
					echo '</form>';
				}
				
			} else {
				
				if(check($extraSecurityData['question_1'], $extraSecurityData['question_2'], $extraSecurityData['answer_1'], $extraSecurityData['answer_2'])) {
					# verify using security questions
					if(check($_POST['pwd_ans1'], $_POST['pwd_ans2'], $_POST['new_password'], $_POST['new_password2'])) {
						try {
							if($_POST['pwd_ans1'] != $extraSecurityData['answer_1']) throw new Exception('The answers entered are not correct.');
							if($_POST['pwd_ans2'] != $extraSecurityData['answer_2']) throw new Exception('The answers entered are not correct.');
							if($_POST['new_password'] != $_POST['new_password2']) throw new Exception('The new passwords did not match.');
							
							# set new password
							$Account->setPassword($_POST['new_password']);
							
							# answers are correct, change password
							if(!$Account->changePassword()) throw new Exception('There was an error changing your password, please contact support. [6]');
							
							message('Your password has been successfully changed.', 'success');
							logSystem::add('password recovered (questions verification)');
							
							$disableSqForm = true;
						} catch(Exception $ex) {
							message($ex->getMessage(), 'error');
						}
					}
					if($disableSqForm != true) {
						echo '<p>Please answer your account\'s security questions:</p>';
						echo '<form action="'.module_url('verification/forgotpassword/key/'.$_GET['key'].'/', true).'" method="post">';
						echo '<input type="hidden" name="pwd_username" value="'.$_POST['pwd_username'].'"/>';
						echo '<table class="login-form">';
							echo '<tr>';
								echo '<td>New Password:</td>';
								echo '<td><input type="password" name="new_password" autofocus/></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Confirm New Password:</td>';
								echo '<td><input type="password" name="new_password2" /></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td></td>';
								echo '<td>'.$extraSecurityData['question_1'].'<br /><input type="text" name="pwd_ans1" /></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td></td>';
								echo '<td>'.$extraSecurityData['question_2'].'<br /><input type="text" name="pwd_ans2" /></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td></td>';
								echo '<td><button type="submit" name="pwd_submit" value="ok">Verify</button></td>';
							echo '</tr>';
						echo '</table>';
						echo '</form>';
					}
				} else {
					# DOESNT HAVE EXTRA SECURITY
					
					# check new passwords
					if(check($_POST['new_password'], $_POST['new_password2'])) {
						try {
							
							if($_POST['new_password'] != $_POST['new_password2']) throw new Exception('The new passwords did not match.');
							
							# set new password
							$Account->setPassword($_POST['new_password']);
							
							# all good, change pass
							if(!$Account->changePassword()) throw new Exception('There was an error changing your password, please contact support. [8]');
							
							message('Your password has been successfully changed.', 'success');
							logSystem::add('password recovered (no verification)');
							
							$disablePwdForm = true;
						} catch(Exception $ex) {
							message($ex->getMessage(), 'error');
						}
					}
					
					# new password form
					if($disablePwdForm != true) {
						echo '<form action="'.module_url('verification/forgotpassword/key/'.$_GET['key'].'/', true).'" method="post">';
						echo '<input type="hidden" name="pwd_username" value="'.$_POST['pwd_username'].'"/>';
						echo '<table class="login-form">';
							echo '<tr>';
								echo '<td>New Password:</td>';
								echo '<td><input type="password" name="new_password" autofocus/></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td>Confirm New Password:</td>';
								echo '<td><input type="password" name="new_password2" /></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td></td>';
								echo '<td><button type="submit" name="new_password_submit" value="ok">Continue</button></td>';
							echo '</tr>';
						echo '</table>';
						echo '</form>';
					}
				}
				
			}
			
		} else {
			
			# some error
			message('Error 5', 'error');
		}
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
		logSystem::add('password recovery verification error', 5);
	}
	
} else {
	
	# enter username form
	echo '<form action="'.module_url('verification/forgotpassword/key/'.$_GET['key'].'/', true).'" method="post">';
	echo '<table class="login-form">';
		echo '<tr>';
			echo '<td>Username:</td>';
			echo '<td><input type="text" name="pwd_username" maxlength="25" autofocus/></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td>';
				echo '<div style="width: 300px;font-size: 12px;">';
					echo '<a href="'.module_url('recovery/username/', true).'">Forgot your username ?</a><br /><br />';
				echo '</div>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td><button type="submit" name="pwd_submit" value="ok">Continue</button></td>';
		echo '</tr>';
	echo '</table>';
	echo '</form>';
	
}
?>

