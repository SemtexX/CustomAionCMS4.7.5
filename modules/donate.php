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

<div align="center">
	<p>Credits can be spent in our <a href="<?php module_url('shop/'); ?>">Webshop</a> or for <a href="<?php module_url('usercp/upgrade/'); ?>">Premium | VIP membership</a>.</p><br />
	<p>1 € gives you 100 Credits (Toll)</p><br />
	<p>You can donate with <span style="color:#307fff;font-weight:bold;">PayPal</span></p><br /><br />
</div>

<h4>Upon making a donation you automatically agree to all terms below.</h4>
<ul>
	<li style="font-size:14px;">You agree, that Credits are voluntary transfers of virtual assets from private individuals.</li>
	<li style="font-size:14px;">Donations are materials given by private persons without receiving anything in exchange.</li>
	<li style="font-size:14px;">You agree, that by making a donation you are not buying any product or service.</li>
	<li style="font-size:14px;">To show our appreciation we reward donators with Credits in return for their support.</li>
	<li style="font-size:14px;">The donations are used to keep the server up and running, as well as improving our network.</li>
	<li style="font-size:14px;">You agree, that all transactions made are final and will not be refunded in any way.</li>
	<li style="font-size:14px;">You agree, that virtual assets such as player items will not be restored if lost or stolen.</li>
</ul>

<div style="width:100%;border-bottom: 1px dashed #ccc;margin:50px 0px;"></div>


<div align="center" style="font-size:14px;">
	<img src="<?php template_img(); ?>paypal_logo.png"/><br />
	
	<br />
	<a href="<?php module_url('donate/paypal/'); ?>" class="btn btn-primary">Get Credits</a>
</div>

<?php if(config('sr_active') == true) { ?>
<div style="width:100%;border-bottom: 1px dashed #ccc;margin:50px 0px;"></div>
<div align="center" style="font-size:14px;">
	<img src="<?php template_img(); ?>super_rewards.png"/><br />
	<br />
	<a href="<?php module_url('donate/superrewards/'); ?>" class="btn btn-primary">Get Credits</a>
</div>
<?php } ?>

<?php if(config('pw_active') == true) { ?>
<div style="width:100%;border-bottom: 1px dashed #ccc;margin:50px 0px;"></div>
<div align="center" style="font-size:14px;">
	<img src="<?php template_img(); ?>paymentwall.png"/><br />
	<br />
	<a href="<?php module_url('donate/paymentwall/'); ?>" class="btn btn-primary">Get Credits</a>
</div>
<?php } ?>

<?php if(config('fk_active') == true) { ?>
<div style="width:100%;border-bottom: 1px dashed #ccc;margin:50px 0px;"></div>
<div align="center" style="font-size:14px;">
	<img src="<?php template_img(); ?>free-kassa.png"/><br />
	
	<br>
	<p>Donate with Free-Kassa which includes many Donation Methods such as</p>
	<p>Paypal, Credit Cards, Mobile and more</p>
	<p>You should receive your credits shortly after donating.</p>
	
	<br>
	<a href="<?php module_url('donate/freekassa/'); ?>" class="btn btn-primary">Get Credits</a>
</div>
<?php } ?>

<br /><br /><br /><br />