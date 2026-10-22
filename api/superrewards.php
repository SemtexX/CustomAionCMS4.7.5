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

try {
	
	if(!@include_once($sys_path . 'includes/system.php')) {
		throw new Exception('Could not load engine.');
	}

	$SuperRewards = new SuperRewards($config['sr_secret_key']);

	// transaction id
	if(check($_REQUEST['id'])) {
		$SuperRewards->setTransactionId($_REQUEST['id']);
	}

	// user id
	if(check($_REQUEST['uid'])) {
		$SuperRewards->setUserId($_REQUEST['uid']);
	}

	// offer id
	if(check($_REQUEST['oid'])) {
		$SuperRewards->setOfferId($_REQUEST['oid']);
	}

	// total
	if(check($_REQUEST['new'])) {
		$SuperRewards->setTotal($_REQUEST['new']);
	}

	// hash signature
	if(check($_REQUEST['sig'])) {
		$SuperRewards->setSignatureHash($_REQUEST['sig']);
	}

	// Hash validate
	if(!$SuperRewards->validateHash()) {
		// signature doesn't match, respond with a failure
		$SuperRewards->saveErrorLog();
		die("0\n");
	}
	
	// Check for duplicate postback
	if($SuperRewards->isPostbackDuplicate()) {
		die("1\n");
	}
	
	// Process payment
	if(!$SuperRewards->processPayment()) {
		throw new Exception('Payment process failed.');
	}

	// Success
	echo "1\n";

} catch(Exception $ex) {
	echo $ex->getMessage();
}