$(document).ready(function () {
	$('.aion-tooltip-text').each(function () {
		var url = "https://www.globalxcentra.net/aioncp/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var quality = str[2].match("quality-(.*)\"  ")[1];
			obj.innerHTML = "<a class=\"tooltip  quality-" + quality + "\" href=\"https://aioncodex.com/us/items/" + itemId + "\">" + name + "</a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
	$('.aion-tooltip-icon').each(function () {
		var url = "https://www.globalxcentra.net/aioncp/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var icon = str[1].match("items\/(.*).png")[1];
			var quality = str[2].match("quality-(.*)\"  ")[1];
			obj.innerHTML = "<a href=\"https://aioncodex.com/us/items/" + itemId + "\"><img src=\"https://aioncodex.com/us/item/" + icon + ".png\" /></a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
	$('.aion-tooltip-icon-small').each(function () {
		var url = "https://www.globalxcentra.net/aioncp/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var icon = str[1].match("items\/(.*).png")[1];
			var quality = str[2].match("quality-(.*)\"  ")[1];
			obj.innerHTML = "<a href=\"https://aioncodex.com/us/items/" + itemId + "\"><img src=\"https://aioncodex.com/us/item/" + icon + ".png\" width=\"24\" height=\"24\" /></a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
})
			
function qtip_options(itemId) {
	return {
		    content: {
		        text: function () {
		            $(this).qtip('option', 'content.text', 'Loading...');
		            var result = $.ajax({
		                url: "https://www.globalxcentra.net/aioncp/ptip.php?id=" + itemId
		            });
		            return result;
		        }
		    },
		    position: {
		        target: 'mouse',
		        adjust: { x: 5, y: 5 }
		    },
			style: {
				classes: 'qtip-rounded'
			}
	};
}