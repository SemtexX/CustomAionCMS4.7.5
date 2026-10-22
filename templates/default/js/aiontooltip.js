$(document).ready(function () {
	$('.aion-tooltip-text').each(function () {
		var url = "http://gamezaion.com/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var quality = str[2].match("quality-(.*)\"  ")[1];
			obj.innerHTML = "<a class=\"tooltip  quality-" + quality + "\" href=\"http://aiondatabase.net/us/item/" + itemId + "\"><b>" + name + "</b></a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
	$('.shop-item-text').each(function () {
		var url = "http://gamezaion.com/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var quality = str[2].match("quality-(.*)\"  ")[1];
			//obj.innerHTML = "<a class=\"tooltip  quality-" + quality + "\" href=\"http://aiondatabase.net/us/item/" + itemId + "\"><b>" + name + "</b></a>";
			obj.innerHTML = "<a href=\"http://aiondatabase.net/us/item/" + itemId + "\" class=\"shop-quality-" + quality + "\">" + name + "</a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
	$('.aion-tooltip-icon').each(function () {
		var url = "http://gamezaion.com/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var icon = str[1].match("items\/(.*).png")[1];
			var quality = str[2].match("quality-(.*)\"  ")[1];
			obj.innerHTML = "<a href=\"http://aiondatabase.net/us/item/" + itemId + "\"><img src=\"http://aiondatabase.net/items/" + icon + ".png\" /></a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
	$('.aion-tooltip-icon-small').each(function () {
		var url = "http://gamezaion.com/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var icon = str[1].match("items\/(.*).png")[1];
			var quality = str[2].match("quality-(.*)\"  ")[1];
			obj.innerHTML = "<a href=\"http://aiondatabase.net/us/item/" + itemId + "\"><img src=\"http://aiondatabase.net/items/" + icon + ".png\" width=\"24\" height=\"24\" /></a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
	$('.aion-tooltip-icon-large').each(function () {
		var url = "http://gamezaion.com/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var name = str[2].match("<b>(.*)</b>")[1];
			var itemId = parseInt(str);
			var icon = str[1].match("items\/(.*).png")[1];
			var quality = str[2].match("quality-(.*)\"  ")[1];
			obj.innerHTML = "<a href=\"http://aiondatabase.net/us/item/" + itemId + "\"><img src=\"http://aiondatabase.net/items/" + icon + ".png\" width=\"40\" height=\"40\" /></a>";
			$(obj).qtip(qtip_options(itemId));
		})
	})
	$('.shop-item-icon').each(function () {
		var url = "http://gamezaion.com/pquery.php?id=" + $(this).attr("id");
		var obj = this;
		$.getJSON(url, function (data) {
			var str = data.aaData[0];
			var icon = str[1].match("items\/(.*).png")[1];
			obj.innerHTML = "<img src=\"http://aiondatabase.net/items/" + icon + ".png\" width=\"50\" height=\"50\" />";
		})
	})
})
			
function qtip_options(itemId) {
	return {
		    content: {
		        text: function () {
		            $(this).qtip('option', 'content.text', 'Loading...');
		            var result = $.ajax({
		                url: "http://gamezaion.com/ptip.php?id=" + itemId
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