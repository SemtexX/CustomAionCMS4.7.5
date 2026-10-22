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
	
echo '<div style="background:#fef2da;border:2px solid #f79433;padding: 10px;">';
	echo '<div style="background:#fff9ec;text-align:center;padding:10px;">';
		echo '<img src="'.template_img(true).'paypal_logo.png"/>';
	echo '</div>';
	echo '<div style="padding:10px;text-align:center;">';
		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
			echo '<input type="hidden" name="cmd" value="_xclick">';
			echo '<input type="hidden" name="business" value="syrextv1337@gmail.com">';
			echo '<input type="hidden" name="item_name" value="GlobalXcentra Credits">';
			echo '<input type="hidden" name="currency_code" value="EUR">';
			echo '€ <input type="text" name="amount" id="amount" maxlength="3" style="width:40px;"> EUR = <span id="result">0</span> Credits<br /><br />';
			echo '<input type="hidden" name="notify_url" value="'.__BASE_URL__.'api/paypal.php">';
			echo '<input type="hidden" name="return" value="'.__BASE_URL__.'">';
			echo '<input type="hidden" name="cancel_return" value="'.__BASE_URL__.'">';
			echo '<input type="hidden" name="no_shipping" value="1">';
			echo '<input type="hidden" name="shipping" value="0.00">';
			echo '<input type="hidden" name="no_note" value="1">';
			echo '<input type="hidden" name="tax" value="0.00">';
			echo '<input type="hidden" name="custom" value="'.$_SESSION['userid'].'">';
			echo '<input type="image" name="submit" src="'.template_img(true).'paypal-submit.jpg" alt="Buy now with PayPal" style="cursor:pointer;">';
		echo '</form>';
	echo '</div>';
echo '</div>';

?>

<script type="text/javascript">
document.getElementById('amount').onkeyup = function(ev) {
  var num = 0;
  var c = 0;
  var event = window.event || ev;
  var code = (event.keyCode) ? event.keyCode : event.charCode;
  for(num=0;num<this.value.length;num++) {
	c = this.value.charCodeAt(num);
	if(c<48 || c>57) {
	  document.getElementById('result').innerHTML = '0';
	  document.getElementById('amount').value = '';
	  return false;
	}
  }
  num = parseInt(this.value);
  if(isNaN(num)) {
	document.getElementById('result').innerHTML = '0';
	document.getElementById('amount').value = '';
  } else {
	var result = (100*num).toString();
	document.getElementById('result').innerHTML = result;
  }
}
</script>