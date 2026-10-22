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
	
	Paymentwall_Base::setApiType(Paymentwall_Base::API_VC);
	Paymentwall_Base::setAppKey(config('pw_project_key'));
	Paymentwall_Base::setSecretKey(config('pw_private_key'));

	$pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);

	if($pingback->validate()) {
		
		$Paymentwall = new Paymentwall();
		
		if ($pingback->isDeliverable()) {
			// deliver the virtual currency
			$Paymentwall->processPayment($_GET);
			
		} else if ($pingback->isCancelable()) {
			// withdraw the virtual currency
			$Paymentwall->processCancelation($_GET);
			
		} else if ($pingback->isUnderReview()) {
			// set "pending" status to order
			$Paymentwall->processPending($_GET);
			
		}
		
		echo 'OK';
		
	} else {
		
		// TODO: log system
		echo $pingback->getErrorSummary();
		
	}
	

} catch(Exception $ex) {
	echo $ex->getMessage();
}