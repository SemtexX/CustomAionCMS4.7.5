<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

# Access
define('access', 'cron');

# Path
$file_name = basename(__FILE__);
$sys_path = str_replace('\\','/',dirname(dirname(__FILE__))).'/';

# Load system
try {

	if(!@include_once($sys_path . 'includes/system.php')) {
		throw new Exception('Could not load engine.');
	}
	
	// PayPal Sandbox
	$enable_sandbox = false;

	// PayPal Seller Email
	$seller_email = 'syrextv1337@gmail.com';

	$ipn = new PaypalIPN();
	if ($enable_sandbox) {
		$ipn->useSandbox();
	}
	$verified = $ipn->verifyIPN();

	// IPN
	$paypal_ipn_status = "VERIFICATION FAILED";
	if ($verified) {
		try {
			
			// Check receiver email
			if(strtolower($_POST["receiver_email"]) != strtolower($seller_email)) throw new Exception('RECEIVER EMAIL MISMATCH');
			
			// TODO: log system
			$paypal_ipn_status = "Completed Successfully";
			
			// Process payment
			try {
				
				// CALCULATE CREDITS
				$totalCredits = floor($_POST['payment_gross']*100);
				
				// SEND CREDITS
				$Account = new Account();
				$Account->setId($_POST['custom']);
				$sendCredits = $Account->addCredits($totalCredits);
				if(!$sendCredits) throw new Exception('paypal: error adding credits');
				
				// ADD DATABASE LOG
				$addPayPalLog = $db->query("INSERT INTO `aioncms`.`paypal` (txn_id, payment_date, payment_gross, payer_email, custom) VALUES (?, ?, ?, ?, ?)", array($_POST['txn_id'], $_POST['payment_date'], $_POST['payment_gross'], $_POST['payer_email'], $_POST['custom']));
				
				// ADD LOG
				logSystem::add('PayPal donation complete userid: '.$_POST['custom'].' credits: ' . $totalCredits);
				
			} catch(Exception $ex) {
				$paypal_ipn_status = $ex->getMessage();
			}
			
		} catch(Exception $ex) {
			// TODO: log system
			$paypal_ipn_status = $ex->getMessage();
		}
	} elseif ($enable_sandbox) {
		if ($_POST["test_ipn"] != 1) {
			$paypal_ipn_status = "RECEIVED FROM LIVE WHILE SANDBOXED";
			// TODO: log system
		}
	} elseif ($_POST["test_ipn"] == 1) {
		$paypal_ipn_status = "RECEIVED FROM SANDBOX WHILE LIVE";
		// TODO: log system
	}

	// Reply with an empty 200 response to indicate to paypal the IPN was received correctly
	header("HTTP/1.1 200 OK");
	
} catch(Exception $ex) {
	
	logSystem::add('PayPal error: ' . $ex->getMessage());
	die($ex->getMessage());
	
}