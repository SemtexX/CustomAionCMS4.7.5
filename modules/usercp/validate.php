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

<h3>Validate Account</h3>
<p>Use this only if a staff member requested you to do so.</p>
<br />
<br />
<br />
<br />

<?php

if(check($_POST['acc_submit'], $_POST['acc_pwd'])) {
	try {
		$Account = new Account();
		$Account->setId($_SESSION['userid']);
		$accountData = $Account->getAccountData();
		
		if(!is_array($accountData)) throw new Exception('Bad request.');
		$encryptPwd = base64_encode(sha1($_POST['acc_pwd'], true));
		if($accountData['password'] != $encryptPwd) throw new Exception('Your password is not correct.');
		
		echo '<div class="col-md-6 col-md-offset-3 text-center">';
			echo '<h4>Validation Code</h4><br />';
			echo '<p style="color:red;">'.md5($_SESSION['username'] . md5('validateUser3000')).'</p>';
		echo '</div>';
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'warning');
	}
} else {
?>
<div class="col-md-6 col-md-offset-3 text-center">
	<h4>Enter your account password</h4><br />
	<form method="post" action="">
		<div class="form-group">
			<input class="form-control" type="password" name="acc_pwd" /><br />
			<button type="submit" name="acc_submit" value="ok" class="btn btn-success">Create Validation Code</button>
		</div>
	</form>
</div>
<?php } ?>