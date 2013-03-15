<html>
<title>Upload File</title>
<head>

<?php $p = ""; require($p . "styling/header-script.php"); ?>

<script>

var i = 1 //counter for additional notes

function selectChannel() {

	if ($("#brand option:selected").val() !== "Manufacturer") {

		var channel = "&nbsp;&rArr;&nbsp;<select onChange='showFilters()'>"
		
		channel += "<option value='Channel' selected>Select Channel</option>"
		
		channel += "<option value='Amazon'>Amazon</option>"

		channel += "<option value='eBay'>eBay</option>"

		channel += "<option value='Website'>Website</option></select>" }
	
	else { var channel = ""; $("#filtersexport").html(channel) }
	
	$("#channel").html(channel)
	
return 1 }

function showFilters() {

	var channel = $("#channel option:selected").val()

	// console.log(channel)

	var filters = ""
	
	if (channel == "Channel") $("#filtersexport").html(filters)
	
	if (channel == "eBay") {

		filters += "<hr><div class='optionsc'><select id='eBayShippingPreset' name='eBayShippingPreset'><option value='Shipping Preset' selected>Shipping Preset</option>"
		
		filters += "<option value='Calculated'>Calculated</option>"
		
		filters += "<option value='Free'>Free</option>"
		
		filters += "<option value='Freight'>Freight</option></select></div>"
		
		filters += "<div class='optionsc'><select id='eBayShippingDestination' name='eBayShippingDestination'><option value='Shipping Destination' selected>Shipping Destination</option>"
		
		filters += "<option value='US Only'>US Only</option>"
		
		filters += "<option value='Worldwide'>Worldwide</option></select></div>"
		
		filters += "<div class='optionsc'><select id='eBayBestOffer' name='eBayBestOffer'><option value='Best Offer' selected>Best Offer</option>"
		
		filters += "<option value='True'>TRUE</option>"
		
		filters += "<option value='False'>FALSE</option></select></div>"

		filters += "<div class='optionsc'><select id='eBayPaymentsPreset' name='eBayPaymentsPreset'><option value='Payments Preset' selected>Payments Preset</option>"
		
		filters += "<option value='Paypal - 30 Day Return'>Paypal - 30 Day Return</option>"
		
		filters += "<option value='Paypal - No Immediate Pay'>Paypal - No Immediate Pay</option></select></div></div>"
		
		filters += "<br /><br /><div id='notes'><div id='note"+i+"'>"
		
		filters += "<a href='' onclick='addNote(); return false;'><span class='addbutton'>+</span></a>&nbsp;"
		
		filters += "<input type='text' size='80' name='Additional Notes "+i+"' placeholder=' Additional Note (eg. extra handling time)'></input>"
		
		filters += "</div></div><br />"
		
		filters += "<input type='reset' class='leftbuttonclick' value='Clear Fields'><input class='continueclick' type='submit' value='Export'>"
		
		$("#filtersexport").html(filters) }

return 1 }

function checkForm() {

	var msg = []
	
	if ($("#channel option:selected").val() == "eBay") {

		if ($("#eBayShippingPreset option:selected").val() == "Shipping Preset") msg.push("Shipping Preset")
	
		if ($("#eBayShippingDestination option:selected").val() == "Shipping Destination") msg.push("Shipping Destination")

		if ($("#eBayBestOffer option:selected").val() == "Best Offer") msg.push("Best Offer")

		if ($("#eBayPaymentsPreset option:selected").val() == "Payments Preset") msg.push("Payments Preset") }
	
	if (msg.length > 0) {	alert(msg.join(", ") + " not selected"); return false	}
	
return true }

function addNote() {

	i++

	$("#notes").append("<div id='note"+i+"'><a href='' onclick='addNote(); return false;'><span class='addbutton'>+</span></a>&nbsp;<input type='text' size='80' name='Additional Notes "+i+"' placeholder=' Additional Note'></input>&nbsp;<a href='' onclick='removeNote("+i+"); return false;'>x</a></div>") 
	
return 1 }

function removeNote(i) { 

	$("#note"+i).remove()

return 1 }

</script>

</head>

<body>

<?php

{# iNCLUDES

require "db/.db-info.php";

require "styling/header.html";

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getBrands() { global $db;
	
	$brands = $db->brands;
	
	$res = $brands->find();
	
	foreach ($res as $r) $manufacturers[] = $r['brand'];

return (isset($manufacturers)) ? $manufacturers : array(); }

function makeSelect($brands) {

	$select = '<div id="brandselect" style="float: left"><select id="brand" name="brand" onchange="selectChannel()"><option value="Manufacturer" selected>Select Manufacturer</option>';

	foreach ($brands as $brand) $select .= "<option value='$brand'>$brand</option>";
	
	$select .= '</select></div>';
	
return $select; }
	
}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$brands = getBrands();

$selectBrand = makeSelect($brands);

$channel = "<div id='channel' name='channel' ></div>";
	
}

{# MAiN

echo "<div id='body-margin'>";

if (empty($_POST)) {

	echo '<form action="' . $_SERVER['PHP_SELF'] . '" onsubmit="return checkForm()" method="POST">';

	echo $selectBrand . $channel;

	echo "<div id='filtersexport'></div></form>"; }

else {
	
	var_dump($_POST); 
	
}

}

?>

</div>
</body>
</html>