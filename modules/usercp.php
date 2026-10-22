<?php
// Am Anfang jeder Seite
include('countdown_timer.php');
?>

<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block usercp"></div>
<br /><br />

<h3>MENU</h3>

<?php
echo '<div class="usercp-menu-container">';
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/acc_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/account/', true).'">My Account</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/acc_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/battlepass/', true).'">Battlepass</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/char_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/characters/', true).'">My Characters</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/tokens_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('tickets/list/', true).'">My Support Tickets</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/items_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('shop/', true).'">Webshop</a></div>';
	echo '</div>';
	
	//echo '<div class="item">';
		//echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/wsitems_ico.png" /></div>';
		//echo '<div class="itemlink"><a href="'.module_url('shop/', true).'">Weekly Special Shop</a></div>';
	//echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/premium_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/upgrade/', true).'">Get Premium | VIP Membership</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/changepass_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/password/', true).'">Change Password</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/bansystem_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/bansystem/', true).'">Ban System</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/forumevents_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('forumevents/', true).'">Forum Events</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/xfer_ot_icon.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/timexchange/', true).'">Online Time Exchange</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/lottery_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('lottery/', true).'">Lottery</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/redeem_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/redeem/', true).'">Redeem Code</a></div>';
	echo '</div>';
	
	echo '<div class="item">';
		echo '<div class="itemicon"><img src="'.template_img(true).'usercp_icons/referrals_ico.png" /></div>';
		echo '<div class="itemlink"><a href="'.module_url('usercp/referrals/', true).'">Referral System</a></div>';
	echo '</div>';
echo '</div>';