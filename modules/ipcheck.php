<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';
echo '<br />';

$userIp =  Handler::userIP();

echo '<h3>Send the following code to support:</h3>';
if($userIp == '0.0.0.0') {
	echo $userIp;
} else {
	debug(md5($userIp . 'gnDev'));
}