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
<img src="<?php template_img(); ?>free-kassa.png"/><br />

How to make a donation<br><br>
1. Select the desired donation amount in usd.<br>
2. Click on the button to go to the Free-Kassa site.<br>
3. Select your preferred transfer method.<br>
4. Follow the instructions of the selected payment system.<br><br>

Thanks for the support!<br>

<br><br>

<?php

$donationOptions = config('fk_donation_options');

if(check($_POST['kassa-buy'])) {
	try {
		
		if(!check($_SESSION['userid'])) throw new Exception('Your account information could not be loaded.');
		if(!check($_POST['option'])) throw new Exception('The donation option is not valid.');
		if(!array_key_exists($_POST['option'], $donationOptions)) throw new Exception('The donation option is not valid.');
		
		$merchant_id = config('fk_merchant_id');
		$secret_word = config('fk_secret');
		$order_id = $_SESSION['userid'];
		$order_amount = $_POST['option'];
		$signature = md5($merchant_id.":".$order_amount.":".$secret_word.":".$order_id);

		$kassaUrl = 'http://www.free-kassa.ru/merchant/cash.php?';
		$kassaUrl .= 'm=' . $merchant_id;
		$kassaUrl .= '&oa=' . $order_amount;
		$kassaUrl .= '&o=' . $order_id;
		$kassaUrl .= '&s=' . $signature;
		$kassaUrl .= '&lang=en';
		$kassaUrl .= '&pay=Donate';
		
		redirect($kassaUrl);
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<form method="post">';
	echo '<select name="option">';
	foreach($donationOptions as $amount => $credits) {
		echo '<option value="'.$amount.'">$'.number_format($amount, 2).' USD - '.number_format($credits).' Credits</option>';
	}
	echo '</select> ';
	echo '<input class="btn btn-primary" type="submit" name="kassa-buy" value="Get Credits">';
echo '</form>';
?>