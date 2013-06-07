<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ORWDB - Export Manufacturer</title>
<head>

<?php $p = ""; require($p . "styling/header-script.php"); ?>

<script>

var i = 1 //counter for additional notes

function selectChannel() {

	if ($("#brand option:selected").val() !== "Manufacturer") {

		var channel = "&nbsp;&rArr;&nbsp;<select name='channel' onChange='showFilters()'>"
		
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

		filters += "<hr><br><div class='optionsc'><select id='eBayShippingPreset' name='eBayShippingPreset'><option value='Shipping Preset' selected>Shipping Preset</option>"
		
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
		
		filters += "<input type='reset' class='leftbuttonclick' value='Clear Fields'><input id='process' onclick='processtext()' class='continueclick2' type='submit' value='Export'>"
		
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
	
	foreach ($res as $r) $manufacturers[$r['brand']] = $r;

return (isset($manufacturers)) ? $manufacturers : array(); }

function makeSelect($brands) {

	$select = '<div id="brandselect" style="float: left"><select id="brand" name="brand" onchange="selectChannel()"><option value="Manufacturer" selected>Select Manufacturer</option>';

	foreach (array_keys($brands) as $brand) $select .= "<option value='$brand'>$brand</option>";
	
	$select .= '</select></div>';
	
return $select; }

function getProducts($selBrand) { global $db, $brands;

	if (array_key_exists('full', $brands[$selBrand]['Missing'])) $exclude = $brands[$selBrand]['Missing']['full'];
	
	// echo count($brands[$selBrand]['partial']);
	
	if (array_key_exists('partial', $brands[$selBrand]['Missing'])) {
	
		foreach (array_keys($brands[$selBrand]['Missing']['partial']) as $pn) $exclude[] = $pn;
		
	}

	$products = $db->products;
	
	$res = $products->find(["Manufacturer" => $selBrand]);

	foreach ($res as $r) { if (!in_array($r['Manufacturer SKU'], $exclude)) $selProducts[] = $r; }
	
	return $selProducts;
	
}
	
function getCategories() {

	global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();

	foreach ($res as $r) {
		
		$list[$r['orw']]['amazon'] = (array_key_exists('amazon', $r)) ? $r['amazon'] : null;
		
		$list[$r['orw']]['ebay'] = (array_key_exists('ebay', $r)) ? $r['ebay'] : null;
		
		$list[$r['orw']]['ebaystore'] = (array_key_exists('ebaystore', $r)) ? $r['ebaystore'] : null;
		
		$list[$r['orw']]['product_type'] = (array_key_exists('product_type', $r)) ? $r['product_type'] : null;
		
		$list[$r['orw']]['filters'] = (array_key_exists('filters', $r)) ? $r['filters'] : null;
	
	}
	
	ksort($list);

	return $list;

}

function checkCategories($products, $channel) { global $globalCategories;

	// var_dump($globalCategories);

	foreach ($products as $p) { if (strpos($p['Category'], "|") !== false) {
	
		foreach (explode("|", $p['Category']) as $cat) $categories[] = $cat; }

		else $categories[] = $p['Category']; }

	if ($channel == 'eBay') {
	
		foreach ($categories as $category) { 
		
			if (is_null($globalCategories[$category]['ebay']) || $globalCategories[$category]['ebay'] == '' || is_null($globalCategories[$category]['ebaystore']) || $globalCategories[$category]['ebaystore'] == '') {
					
				$missing[] = $category; }	}
				
		if (isset($missing)) { echo "<h4>Categories missing " . $channel . " data:</h4>";
		
			foreach ($missing as $m) echo $m . "<br />"; 
			
			exit(); }
	
	}

return 1; }

{# --eBay Vehicles

function geteBayVehicles() { global $db;

	$vehicles = $db->vehicles;
	
	$res = $vehicles->find();
	
	foreach ($res as $r) $ebayveh[$r['Make']][$r['Model']] = $r['eBay'];

return $ebayveh; }

function eBayVehicles($item, $cat) { global $eBayVehicles;

	$eBayVehicles[$item['ISIS SKU']]['Part Number'] = $item['ISIS SKU'];
	
	$eBayVehicles[$item['ISIS SKU']]['Category'] = $cat;

	foreach ($item['Application List'] as $vehicle) {
	
		foreach (range($vehicle["Start year"], $vehicle["End year"]) as $year) {
		
			$eBayVehicles[$item['ISIS SKU']]['Years'][] = $year;
			
			$eBayVehicles[$item['ISIS SKU']]['Makes'][] = $vehicle['Make'];
			
			$eBayVehicles[$item['ISIS SKU']]['Models'][] = $vehicle['Model'];
		
		}
	}

return 1; }

function getCompatibility($ymm) { global $ebayveh;

	foreach ($ymm as $sku => $pn) {

		foreach ($pn['Years'] as $i => $year) { $veh = $ebayveh[$pn['Makes'][$i]][$pn['Models'][$i]];
		
			if (count($veh) > 1 && !in_array($year, $veh['Exclude']) && $year >= $veh['Start year'] && $year <= $veh['End year']) {
				
				$results[$sku][] = prep($year, $veh['Make'], $veh['Model'], $veh['Extra']); } }
			
		if (isset($results[$sku])) array_unshift($results[$sku], head($pn)); }
	
return $results; }

function prep($year, $make, $model, $notes = '') { global $columns;
	
	$compatibility[$columns[0]] = '';
	
	$compatibility[$columns[1]] = 2;
	
	$compatibility[$columns[2]] = '';
	
	$compatibility[$columns[3]] = ($notes == '') ? "Year=" . $year . "|Make=" . $make . "|Model=" . $model : "Year=" . $year . "|Make=" . $make . "|Model=" . $model . $notes;
	
	$compatibility[$columns[4]] = "Replace";
	
return $compatibility; }

function head($pn) { global $columns;

	$head[$columns[0]] = $pn['Part Number'];
	
	$head[$columns[1]] = 1;
	
	$head[$columns[2]] = $pn['Category'];
	
	$head[$columns[3]] = '';
	
	$head[$columns[4]] = '';
	
return $head; }

}
	
}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$brands = getBrands();

$selectBrand = makeSelect($brands);

$channel = "<div id='channel'></div>";

$globalCategories = getCategories();

$dir = "//orw-file-server/shared/Marketing/Photo Product/";

$ebayveh = geteBayVehicles();

$columns = array("Part Number", "IMPORT LEVEL", "CATEGORY NUMBER 1", "Compatibility", "IMPORT ACTION");

}

{# MAiN

echo "<div id='body-margin'>";

if (empty($_POST)) {

	echo '<form action="' . $_SERVER['PHP_SELF'] . '" onsubmit="return checkForm()" onreset="$(\'#channel\').html(\'\'); $(\'#filtersexport\').html(\'\')" method="POST">';

	echo $selectBrand . $channel;

	echo "<div id='filtersexport'></div></form>"; }

else {
	
	//var_dump($_POST); //var_dump($globalCategories);
	
	$selectedBrand = $_POST['brand'];
	
	$selectedChannel = $_POST['channel'];
	
	$selectedProducts = getProducts($selectedBrand); //var_dump($selectedProducts);
	
	checkCategories($selectedProducts, $selectedChannel);
	
	if ($selectedChannel == 'eBay') {
	
		$eBayDescription = '';
		
		$notes = $_POST['Additional_Notes_1']; $n = 1;
	
		while (array_key_exists("Additional_Notes_" . ++$n, $_POST)) $notes .= '<br />' . $_POST['Additional_Notes_' . $n];
	
		foreach ($selectedProducts as $i => $item) { $weight = explode(".", $item['Weight']);
		
			$eBayOutput[$i]['Part Number'] = $item['ISIS SKU'];
			$eBayOutput[$i]['Manufacturer Part Number'] = $item['Manufacturer SKU'];
			$eBayOutput[$i]['Manufacturer'] = $item['Manufacturer'];
			$eBayOutput[$i]['FolderName'] = $item['Manufacturer'];
			$eBayOutput[$i]['Picture 1'] = ($item['Image 1'] == '') ? '' : $dir . $brands[$selectedBrand]['linecode'] . "/orwdb/images/" . $item['Image 1'];
			$eBayOutput[$i]['Picture 2'] = ($item['Image 2'] == '') ? '' : $dir . $brands[$selectedBrand]['linecode'] . "/orwdb/images/" . $item['Image 2'];
			$eBayOutput[$i]['Picture 3'] = ($item['Image 3'] == '') ? '' : $dir . $brands[$selectedBrand]['linecode'] . "/orwdb/images/" . $item['Image 3'];
			$eBayOutput[$i]['Picture 4'] = ($item['Image 4'] == '') ? '' : $dir . $brands[$selectedBrand]['linecode'] . "/orwdb/images/" . $item['Image 4'];
			$eBayOutput[$i]['Picture 5'] = ($item['Image 5'] == '') ? '' : $dir . $brands[$selectedBrand]['linecode'] . "/orwdb/images/" . $item['Image 5'];
			$eBayOutput[$i]['Picture 6'] = ($item['Image 6'] == '') ? '' : $dir . $brands[$selectedBrand]['linecode'] . "/orwdb/images/" . $item['Image 6'];
			$eBayOutput[$i]['Package Depth'] = $item['Height'];
			$eBayOutput[$i]['Package Length'] = $item['Length'];
			$eBayOutput[$i]['Package Width'] = $item['Width'];
			$eBayOutput[$i]['Fixed Price'] = $item['Price 5'];
			$eBayOutput[$i]['Weight Major'] = $weight[0];
			$eBayOutput[$i]['Weight Minor'] = (string) round((($weight[1] / 100) * 16));
			$eBayOutput[$i]['Legal Disclaimer'] = (array_key_exists('Legal Disclaimer', $item['Filters'])) ? $item['Filters']['Legal Disclaimer'] : '';
			$eBayOutput[$i]['UPC\EAN\ISBN\VIN'] = $item['UPC'];
			$eBayOutput[$i]['Sorted Name'] = 'Jeff';
			$eBayOutput[$i]['Shipping Preset Name'] = $_POST['eBayShippingPreset'];
			$eBayOutput[$i]['Shipping Destination Name'] = $_POST['eBayShippingDestination'];
			$eBayOutput[$i]['Ad Template Name'] = 'ORW 2012';
			$eBayOutput[$i]['Auto Accept Best Offer'] = 'FALSE';
			$eBayOutput[$i]['Auto Decline Best Offer'] = 'TRUE';
			$eBayOutput[$i]['Best Offer Accept Percent'] = '100';
			$eBayOutput[$i]['Best Offer Accept Use Percentage'] = 'FALSE';
			$eBayOutput[$i]['Best Offer Accept Value'] = '0';
			$eBayOutput[$i]['Best Offer Decline Percent'] = '90';
			$eBayOutput[$i]['Best Offer Decline Use Percentage'] = 'TRUE';
			$eBayOutput[$i]['Best Offer Decline Value'] = '0';
			$eBayOutput[$i]['Best Offer'] = $_POST['eBayBestOffer'];
			$eBayOutput[$i]['Duration'] = '365';
			$eBayOutput[$i]['Email Account Name'] = 'francisco@orwmail.com';
			$eBayOutput[$i]['Listing Format'] = '8';
			$eBayOutput[$i]['Lot Size'] = '1';
			$eBayOutput[$i]['Payments Preset Name'] = $_POST['eBayPaymentsPreset'];
			$eBayOutput[$i]['Picture Host Template Name'] = 'eBay Picture Services';
			$eBayOutput[$i]['Seller Account Name'] = 'offroadwarehouse';
			$eBayOutput[$i]['Subtitle'] = '';
			$eBayOutput[$i]['Track Inventory'] = 'TRUE';
			$eBayOutput[$i]['Additional Notes'] = $notes;
			$eBayOutput[$i]['Total on Hand'] = $item['Quantity Available'];			
			$eBayOutput[$i]['Quantity To List'] = $item['Quantity Available'];
			
			$eBayTitle = $item['Manufacturer'] . ' ' . $item['Part Name'];
			
			if (strpos("|", $item['Category']) !== false) { $multiCategories = explode("|", $item['Category']);

				$eBayOutput[$i]['Category Number 1'] = $globalCategories[$multiCategories[0]]['ebay'];
				$eBayOutput[$i]['Category Number 2'] = $globalCategories[$multiCategories[1]]['ebay'];
				$eBayOutput[$i]['Store Category 1'] = $globalCategories[$multiCategories[0]]['ebaystore'];
				$eBayOutput[$i]['Store Category 2'] = $globalCategories[$multiCategories[1]]['ebaystore']; 
				
				foreach ($globalCategories[$multiCategories[0]]['filters'] as $titlefilter) {
			
					if ((strlen($item['Filters'][$titlefilter]) + strlen($eBayTitle) + strlen($item['Manufacturer SKU'])) < 79) {
				
						$eBayTitle .= ' ' . $item['Filters'][$titlefilter]; }

					else continue; } }
			
			else {
			
				$eBayOutput[$i]['Category Number 1'] = $globalCategories[$item['Category']]['ebay'];
				$eBayOutput[$i]['Category Number 2'] = '';
				$eBayOutput[$i]['Store Category 1'] = $globalCategories[$item['Category']]['ebaystore'];
				$eBayOutput[$i]['Store Category 2'] = ''; 
				
				foreach ($globalCategories[$item['Category']]['filters'] as $titlefilter) {
			
					if ((strlen($item['Filters'][$titlefilter]) + strlen($eBayTitle) + strlen($item['Manufacturer SKU'])) < 79) {
				
						$eBayTitle .= ' ' . $item['Filters'][$titlefilter]; }

					else continue; } }
			
			$eBayOutput[$i]['Title'] = $eBayTitle . ' ' . $item['Manufacturer SKU'];
			
			/*<p class="body_head_text">$description1</p><p class="body_head_text">$description2</p><p class="body_head_text">$description3</p><ul><li>$bulletpoint1</li><li>$bulletpoint2</li><li>$bulletpoint3</li><li>$bulletpoint4</li><li>$bulletpoint5</li></ul>*/
			
			//eBay Description
			//
			for ($d = 1; $d < 4; $d++) { if ($item['Description ' . $d] != "") {
			
				$eBayDescription .= '<p class="body_head_text">' . $item['Description ' . $d] . '</p>'; } }
			
			$eBayDescription .= '<ul>';	
			
			for ($b = 1; $b < 6; $b++) { if ($item['Bullet Point ' . $b] != "") {
			
				$eBayDescription .= '<li>' . $item['Bullet Point ' . $b] . '</li>'; } }
				
			$eBayDescription .= '</ul>';
			
			$eBayDescription .= '<p>Specifications</p>';
			
			foreach ($item['Filters'] as $filter => $value) $eBayDescription .= '<strong>' . ltrim($filter, 'T_') . ':</strong> ' . $value . '<br />';
			
			if (array_key_exists('Application List', $item)) { eBayVehicles($item, $eBayOutput[$i]['Category Number 1']);
			
				$eBayDescription .= "<p>Application List</p>";
			
				foreach ($item['Application List'] as $car) {
			
					$eBayDescription .= $car['Start year'] . '-' . $car['End year'] . ' ' . $car['Make'] . ' ' . $car['Model'] . ' ' . $car['Extra'] . '<br />'; } }
			
			$eBayOutput[$i]['Description'] = $eBayDescription; }//echo $eBayDescription;
		
		$ebayblackthorne = fopen("files/".$selectedBrand." - eBay Blackthorne.csv", "w") or exit("eBay Blackthorne file is open.");
		
		fputcsv($ebayblackthorne, array_keys($eBayOutput[0]));
		
		foreach ($eBayOutput as $part) fputcsv($ebayblackthorne, $part);
		
		fclose($ebayblackthorne);
		
		echo '<a href="files/' . $selectedBrand . ' - eBay Blackthorne.csv">' . $selectedBrand . ' - eBay Blackthorne</a><br />';
		
		if (!empty($eBayVehicles)) { //var_dump($eBayVehicles) // var_dump($ebayveh); // var_dump($compatibility);
		
			$compatibility = getCompatibility($eBayVehicles);

			$ebaycompat = fopen("files/".$selectedBrand." - eBay Compatibility.csv", "w") or exit("eBay Compatibility file is open.");
			
			fputcsv($ebaycompat, $columns);
			
			foreach ($compatibility as $part) { foreach ($part as $i => $line) { if ($i < 100) fputcsv($ebaycompat, $line); } }
			
			fclose($ebaycompat);
			
			echo '<a href="files/' . $selectedBrand . ' - eBay Compatibility.csv">' . $selectedBrand . ' - eBay Compatibility</a>'; }	}
 
}

}

{# EXiT

}

?>

</div>
</body>
</html>