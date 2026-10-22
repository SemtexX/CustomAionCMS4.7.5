<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

# Define Access
define('access', 'index');

try {

	if(!@include_once('includes/system.php')) {
		throw new Exception('Could not load engine.');
	}
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}