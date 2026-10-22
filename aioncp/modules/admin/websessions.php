<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>

<script>
$(document).ready(function () {
    setInterval(function() {
        $.get("<?php echo __BASE_URL__; ?>api/live_2.php?key=789789789", function (result) {
            $('#latestData').html(result);
        });
    }, 1000);
});
</script>

<div class="row">
	<div class="col-md-12">
		<div class="block">
			<div class="block-content">
				<div id="latestData">Loading...</div>
			</div>
		</div>
	</div>
</div>