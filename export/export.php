<html>
<title>Web Channels</title>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script type="text/javascript">

function selectChannel() {
	
	var channel = "<option value='Channel' selected>Channel</option>"
	
	channel += "<option value='Amazon'>Amazon</option>"

	channel += "<option value='eBay'>eBay</option>"

	channel += "<option value='Website'>Website</option>"

	$("#channel").html(channel)

}

var i = 1 //counter for additional notes

function showFilters() {

	var channel = $("#channel option:selected").val()

	// console.log(channel)

	var filters = ""
	
	if (channel == "eBay") {

		filters += "<select id='eBayShippingPreset' name='eBayShippingPreset'><option value='Shipping Preset' selected>Shipping Preset</option>"
		
		filters += "<option value='Calculated'>Calculated</option>"
		
		filters += "<option value='Free'>Free</option>"
		
		filters += "<option value='Freight'>Freight</option></select>"
		
		filters += "<select id='eBayShippingDestination' name='eBayShippingDestination'><option value='Shipping Destination' selected>Shipping Destination</option>"
		
		filters += "<option value='US Only'>US Only</option>"
		
		filters += "<option value='Worldwide'>Worldwide</option></select>"
		
		filters += "<select id='eBayBestOffer' name='eBayBestOffer'><option value='Best Offer' selected>Best Offer</option>"
		
		filters += "<option value='True'>TRUE</option>"
		
		filters += "<option value='False'>FALSE</option></select>"

		filters += "<select id='eBayPaymentsPreset' name='eBayPaymentsPreset'><option value='Payments Preset' selected>Payments Preset</option>"
		
		filters += "<option value='Paypal - 30 Day Return'>Paypal - 30 Day Return</option>"
		
		filters += "<option value='Paypal - No Immediate Pay'>Paypal - No Immediate Pay</option></select>"
		
		filters += "<br /><br /><div id='notes'><div id='note"+i+"'>"
		
		filters += "<a href='' onclick='addNote(); return false;'>+</a>&nbsp;"
		
		filters += "<input type='text' size='80' name='Additional Notes "+i+"' placeholder='Additional Notes'></input>"
		
		filters += "</div></div><br />"
		
		filters += "<input type='reset' onclick='window.location.reload()'><input type='submit' value='Export'>"
		
		$("#filters").replaceWith(filters)

	}

}

function checkForm() {

	var msg = []
	
	if ($("#channel option:selected").val() == "eBay") {

		if ($("#eBayShippingPreset option:selected").val() == "Shipping Preset") msg.push("Shipping Preset")
	
		if ($("#eBayShippingDestination option:selected").val() == "Shipping Destination") msg.push("Shipping Destination")

		if ($("#eBayBestOffer option:selected").val() == "Best Offer") msg.push("Best Offer")

		if ($("#eBayPaymentsPreset option:selected").val() == "Payments Preset") msg.push("Payments Preset")
	
	}
	
	if (msg.length > 0) {
	
		alert(msg.join(", ") + " not selected")
	
		return false
	
	}
	
	return true

}

function addNote() {

	i++

	$("#notes").append("<div id='note"+i+"'><a href='' onclick='addNote(); return false;'>+</a>&nbsp;<input type='text' size='80' name='Additional Notes "+i+"' placeholder='Additional Notes'></input>&nbsp;<a href='' onclick='removeNote("+i+"); return false;'>X</a></div>")

}

function removeNote(i) { $("#note"+i).remove() }

</script>
</head>
<body>

<?php

{# iNCLUDES

require "../db/.db-info.php";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
	return $db;

}

function getBrands() {

	global $db;
	
	$brands = $db->brands;
	
	$res = $brands->find();
	
	foreach ($res as $r) $manufacturers[] = $r['brand'];

	return (isset($manufacturers)) ? $manufacturers : array();
	
}

function makeSelect($brands) {

	$select = '<select id="brand" name="brand" onchange="selectChannel()"><option value="Manufacturer" selected>Manufacturer</option>';

	foreach ($brands as $brand) $select .= "<option value='$brand'>$brand</option>";
	
	$select .= '</select>';
	
	return $select;

}
	
}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$brands = getBrands();

$selectBrand = makeSelect($brands);

$channel = "<select id='channel' name='channel' onChange='showFilters()'></select>";
	
}

{# MAiN

if (empty($_POST)) {

	echo '<form action="' . $_SERVER['PHP_SELF'] . '" onsubmit="return checkForm()" method="POST">';

	echo $selectBrand . "&nbsp;&rArr;&nbsp;" . $channel;

	echo "<br /><br /><div id='filters'></div></form>";

}

else {
	
	var_dump($_POST);

}

}

?>

</body>
</html>