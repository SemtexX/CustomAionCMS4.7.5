<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div style="margin: 20px;padding: 20px;text-align: center;">
	<?php if(isset($_POST['email']) && isset($_POST['submit'])) { ?>
		<h1>You have been successfully unsubscribed!</h1>
	<?php } else { ?>
		<form action="" method="post">
			<h3>Enter your email</h3>
			<input type="text" name="email" />
			<button type="submit" name="submit" value="ok">UNSUBSCRIBE</button>
		</form>
	<?php } ?>
</div>