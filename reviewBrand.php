<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ORWDB - Review Manufacturer</title>
<head>

<?php $p = ""; require($p . "styling/header-script.php"); ?>

<script>

function csv(brand) {

	var xmlhttp

	xmlhttp = new XMLHttpRequest()

	xmlhttp.onreadystatechange = function() {
  
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		
			if (xmlhttp.responseText == "Done") window.location.href = "files/missingISIS.csv"
			
			else $("#csv").html("&emsp;Something went wrong") }
  
	}

	xmlhttp.open("GET","saveCSV.php?brand="+brand,true)
	
	xmlhttp.send()

return 1 }

function showMissing() {

	$("#review").html("")

	var brand = $("#brand").val()
	
	var map = Array('SKU', 'Price 5', 'Weight', 'Length', 'Width', 'Height')
	
	if (brand != "Manufacturer") {
	
		var csv = "&emsp;<a href='#' onclick='csv(\"" + brand + "\"); return false'>Download CSV</a>"
		
		$("#csv").html(csv)
	
		if ("Missing" in window.manufacturers[brand]) {
		
			if ("full" in window.manufacturers[brand]['Missing']) {
			
				var full = '<h4>Not in ISIS</h4>'
				
				for (var i in window.manufacturers[brand]['Missing']['full']) full += window.manufacturers[brand]['Missing']['full'][i] + "<br />"
				
				$("#review").append(full) }
				
			if ("partial" in window.manufacturers[brand]['Missing']) {
			
				var partial = "<h4>Missing some info</h4>"
				
				partial += "<table><tr>"
				
				for (var x in map) partial += "<th>" + map[x] + "</th>"

				partial += "</tr>"
			
				for (var pn in window.manufacturers[brand]['Missing']['partial']) { partial += "<tr>"
				
					for (var k in window.manufacturers[brand]['Missing']['partial'][pn]) {
					
						//if (window.manufacturers[brand]['Missing']['partial'][pn][k] == "X") {
					
							partial += "<td>" + window.manufacturers[brand]['Missing']['partial'][pn][k] + "</td>" } partial += "</tr>" } //}

				$("#review").append(partial + "</table>") } }
		
		else $("#review").html("All "+brand+" parts are ready for export.") }
		
	else $("#csv").html("")

return 1 }

</script>

</head>

<body>

<?php

{# iNCLUDES

require "db/.db-info.php";

require "styling/header.html";

echo $helptext; // from header.html

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getBrands() { global $db;
	
	$brands = $db->brands;
	
	$res = $brands->find();
	
	foreach ($res as $r) $manufacturers[$r['brand']] = $r;
	
	if (isset($manufacturers)) {
	
		echo "<script>manufacturers=" . json_encode($manufacturers) . "</script>"; }
		
	else $manufacturers = array();

return $manufacturers; }

function makeSelect($brands) {

	$select = '<div id="brandselect" style="float: left"><select id="brand" name="brand" onchange="showMissing()"><option value="Manufacturer" selected>Select Manufacturer</option>';

	foreach (array_keys($brands) as $brand) $select .= "<option value='$brand'>$brand</option>";
	
	$select .= '</select></div>';
	
return $select; }
	
}

{# VARiABLES

$margin = "<div id='body-margin'>";

$db = dbConnect(DBHOST, DBNAME);

$brands = getBrands();

$selectBrand = makeSelect($brands);

$review = "<br /><div id='review'></div>";

$csv = "<div id='csv'></div>";

}

{# MAiN

echo $margin;

echo $selectBrand . $csv . $review;

}

{# EXiT

}

?>

</div>
</body>
</html>