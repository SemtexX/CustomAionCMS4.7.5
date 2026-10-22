<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<?php if(isLoggedIn()) redirect('usercp/'); ?>
<div class="page-header-block accrecovery"></div>
<br /><br />

<h3>Account Access Recovery</h3>
<p>Please select one of the options below.</p>

<br />

<div class="row">
	<div class="col-xs-6 text-center">
		<a href="<?php module_url('recovery/password/'); ?>" class="btn btn-primary btn-lg btn-block">I forgot my password</a>
	</div>
	<div class="col-xs-6 text-center">
		<a href="<?php module_url('recovery/username/'); ?>" class="btn btn-primary btn-lg btn-block">I forgot my username</a>
	</div>
</div>