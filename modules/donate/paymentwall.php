<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block donate"></div>
<br /><br />

<?php
// Paymentwall Widget
Paymentwall_Config::getInstance()->set(array(
    'api_type' => Paymentwall_Config::API_VC,
    'public_key' => config('pw_project_key'),
    'private_key' => config('pw_private_key')
));

$widget = new Paymentwall_Widget(
    $_SESSION['username'], 
    config('pw_widget'),
    array(),
    array(
        'email' => $accountInfo['email'], 
        'timestamp' => time(),
        'ps' => 'all'
    )
);

echo $widget->getHtmlCode(array('width' => config('pw_widget_width'), 'height' => config('pw_widget_height')));
?>