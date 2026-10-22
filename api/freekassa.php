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
	
	// Check Signature
	$sign = md5(config('fk_merchant_id').':'.$_REQUEST['AMOUNT'].':'.config('fk_secret').':'.$_REQUEST['MERCHANT_ORDER_ID']);
	if($sign != $_REQUEST['SIGN']) {
		throw new Exception('Request signature not valid!');
	}
	
	// check amount
	$donationOptions = config('fk_donation_options');
	if(!array_key_exists($_REQUEST['AMOUNT'], $donationOptions)) throw new Exception('Invalid donation option.');
	
	// load account data
	$Account = new Account();
	$Account->setId($_REQUEST['MERCHANT_ORDER_ID']);
	$accountData = $Account->getAccountData();
	if(!is_array($accountData)) {
		throw new Exception('Invalid account data');
	}
	
	// add credits
	$addCredits = $Account->addCredits($donationOptions[$_REQUEST['AMOUNT']]);
	if(!$addCredits) throw new Exception('Error adding credits.');

} catch(Exception $ex) {
	echo $ex->getMessage();
}