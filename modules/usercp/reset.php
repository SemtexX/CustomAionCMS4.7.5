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

<h3>Reset Account Security</h3>
<p>By resetting your account's security information you will be able to choose new security questions and answers and choose a new PIN number.</p>
<br />

<?php
try {
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	$accountSecurity = $Account->getExtraSecurityData();
	
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	if(!is_array($accountSecurity)) throw new Exception('You have not set your account\'s security information.');
	
	if(check($_GET['verify'])) {
		
		if(!in_array($_GET['verify'], array('sq', 'spin'))) throw new Exception('Sorry! your request cannot be completed, try again later.');
		
		if($_GET['verify'] == 'sq') {
			
			# verify security questions
			echo '<div class="col-md-8 col-md-offset-2">';
				if(check($_POST['pwd_ans1'], $_POST['pwd_ans2'])) {
					try {
						if($_POST['pwd_ans1'] != $accountSecurity['answer_1']) throw new Exception('The answers entered are not correct.');
						if($_POST['pwd_ans2'] != $accountSecurity['answer_2']) throw new Exception('The answers entered are not correct.');
						
						# reset security
						if(!$Account->resetAccountSecurity()) throw new Exception('There was an error, please contact support. [2]');
						
						logSystem::add('account security reset');
						redirect('usercp/account/');
						
						$disableSqForm = true;
					} catch(Exception $ex) {
						message($ex->getMessage(), 'error');
					}
				}
				if($disableSqForm != true) {
					echo '<p>Please answer your account\'s security questions:</p><br />';
					echo '<form action="" method="post">';
						echo '<div class="form-group">';
							echo '<label for="q_1">'.$accountSecurity['question_1'].'</label>';
							echo '<input type="text" name="pwd_ans1" class="form-control" id="q_1" autofocus/>';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label for="q_2">'.$accountSecurity['question_2'].'</label>';
							echo '<input type="text" name="pwd_ans2" class="form-control" id="q_2"/>';
						echo '</div>';
						echo '<button type="submit" class="btn btn-primary" name="pwd_submit" value="ok">Verify</button>';
					echo '</form>';
				}
			echo '</div>';
		}
		
		if($_GET['verify'] == 'spin') {
			
			# verify security questions
			echo '<div class="col-md-8 col-md-offset-2">';
				if(check($_POST['pwd_pin'])) {
					try {
						if($_POST['pwd_pin'] != $accountSecurity['security_pin']) throw new Exception('The security PIN entered is not valid.');
						
						# reset security
						if(!$Account->resetAccountSecurity()) throw new Exception('There was an error, please contact support.');
						
						logSystem::add('account security reset');
						redirect('usercp/account/');
						
						$disablePinForm = true;
					} catch(Exception $ex) {
						message($ex->getMessage(), 'error');
					}
				}
				if($disablePinForm != true) {
					echo '<p>Please enter your account\'s 4-digit security PIN:</p><br />';
					echo '<form action="" method="post">';
						echo '<div class="form-group">';
							echo '<label for="pin">Security PIN</label>';
							echo '<input type="text" name="pwd_pin" class="form-control" maxlength="4" id="pin" autofocus/>';
						echo '</div>';
						echo '<button type="submit" class="btn btn-primary" name="pwd_submit" value="ok">Verify</button>';
					echo '</form>';
				}
			echo '</div>';
		}
		
		
	} else {
		
		echo '<div class="col-md-6 col-md-offset-3 text-center">';
			echo '<h3>Choose a verification method</h3><br /><br />';
			if(check($accountSecurity['question_1'], $accountSecurity['question_2'], $accountSecurity['answer_1'], $accountSecurity['answer_2'])) echo '<a href="'.module_url('usercp/reset/verify/sq/', true).'" class="btn btn-primary btn-lg btn-block">Security Questions</a>';
			if(check($accountSecurity['security_pin'])) echo '<a href="'.module_url('usercp/reset/verify/spin/', true).'" class="btn btn-primary btn-lg btn-block">Security PIN</a>';
		echo '</div>';
		
	}
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>