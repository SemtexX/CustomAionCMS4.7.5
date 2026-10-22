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

<h3>Security Questions</h3>
<p>Configuring the security questions will help you confirm ownership of your account.</p>
<br />

<?php
try {
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	$accountSecurity = $Account->getExtraSecurityData();
	
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	if(check($accountSecurity['question_1'], $accountSecurity['question_2'], $accountSecurity['answer_1'], $accountSecurity['answer_2'])) throw new Exception('You have already set your account\'s security questions.');
	
	$securityQuestions = config('security_questions', true);
	
	if(check($_POST['sq_submit'])) {
		try {
			# filters
			if(!check($_POST['question_1'])) throw new Exception('Please fill all the required fields.');
			if(!check($_POST['answer_1'])) throw new Exception('Please fill all the required fields.');
			if(!check($_POST['question_2'])) throw new Exception('Please fill all the required fields.');
			if(!check($_POST['answer_2'])) throw new Exception('Please fill all the required fields.');
			
			if(!in_array($_POST['question_1'], $securityQuestions)) throw new Exception('Your request could not be completed.');
			if(!in_array($_POST['question_2'], $securityQuestions)) throw new Exception('Your request could not be completed.');
			
			if($_POST['question_1'] == $_POST['question_2']) throw new Exception('You can\'t use the same question twice.');
			
			if(!Validator::Length($_POST['answer_1'], 50, 3)) throw new Exception('Your answers may have 3 to 50 characters in length.');
			if(!Validator::Length($_POST['answer_2'], 50, 3)) throw new Exception('Your answers may have 3 to 50 characters in length.');
			
			if(!Validator::Chars($_POST['answer_1'], array("a-z","A-Z","0-9"," "))) throw new Exception('Answers may only contain letters, numbers and spaces.');
			if(!Validator::Chars($_POST['answer_2'], array("a-z","A-Z","0-9"," "))) throw new Exception('Answers may only contain letters, numbers and spaces.');
			
			# save questions
			$saveQuestions = $Account->setSecurityQuestions($_POST['question_1'], $_POST['answer_1'], $_POST['question_2'], $_POST['answer_2']);
			if(!$saveQuestions) throw new Exception("Your request could not be completed. If this problem persists contact the administrator. [E-A004]");
			
			logSystem::add('set security questions');
			
			redirect('usercp/account/');
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
			logSystem::add('set security questions error', 5);
		}
	}
	
	echo '<form action="'.module_url('usercp/securityquestions/', true).'" method="post">';
	echo '<table class="my-account-table">';
		echo '<tr>';
			echo '<td>1st Question &amp Answer:</td>';
			echo '<td>';
				echo '<select name="question_1" class="form-control">';
				shuffle($securityQuestions);
				foreach($securityQuestions as $question) {
					echo '<option value="'.$question.'">'.$question.'</option>';
				}
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td><input type="text" name="answer_1" class="form-control" placeholder="answer..."/></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td colspan="2"><br /></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td>2nd Question &amp Answer:</td>';
			echo '<td>';
				echo '<select name="question_2" class="form-control">';
				shuffle($securityQuestions);
				foreach($securityQuestions as $question) {
					echo '<option value="'.$question.'">'.$question.'</option>';
				}
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td><input type="text" name="answer_2" class="form-control" placeholder="answer..."/></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td></td>';
			echo '<td><button type="submit" name="sq_submit" value="ok" class="btn btn-primary">Save Security Questions</button></td>';
		echo '</tr>';
	echo '</table>';
	echo '</form>';
	
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>