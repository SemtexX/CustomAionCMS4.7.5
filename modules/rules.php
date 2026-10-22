<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block srules"></div>
<br /><br />

<?php
try {
	
	$cacheFileName = 'server_rules.html';
	$cacheLocation = __PATH_CACHE__;
	
	if(!file_exists($cacheLocation . $cacheFileName)) throw new Exception('There was an error displaying the page, please contact the administrator.');
	
	include($cacheLocation . $cacheFileName);
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>