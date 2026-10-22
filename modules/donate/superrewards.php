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
echo '<iframe src="https://wall.superrewards.com/super/offers?h='.config('sr_app_hash').'&uid='.$_SESSION['username'].'&uncached=1" frameborder="0" width="590" height="2400" scrolling="no"></iframe>';
?>