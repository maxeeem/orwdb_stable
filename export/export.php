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
	
	foreach ($res as $r) $manufacturers[$r['brand']] = $r;
	
	return (isset($manufacturers)) ? $manufacturers : array();
	
}

function makeSelect($brands) {

	$select = '<select id="brand" name="brand" onchange="selectChannel()"><option value="Manufacturer" selected>Manufacturer</option>';

	foreach (array_keys($brands) as $brand) $select .= "<option value='$brand'>$brand</option>";
	
	$select .= '</select>';
	
	return $select;

}

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
	
	}
	
	ksort($list);

	return $list;

}

}

{# VARiABLES

$db = dbConnect(DBHOST, DBNAME);

$brands = getBrands();

$selectBrand = makeSelect($brands);

$channel = "<select id='channel' name='channel' onChange='showFilters()'></select>";

$globalCategories = getCategories();

$dir = "//orw-file-server/shared/Marketing/Photo Product/";
	
}

{# MAiN

if (empty($_POST)) {

	echo '<form action="' . $_SERVER['PHP_SELF'] . '" onsubmit="return checkForm()" method="POST">';

	echo $selectBrand . "&nbsp;&rArr;&nbsp;" . $channel;

	echo "<br /><br /><div id='filters'></div></form>";

}

else {
	
	var_dump($_POST);
	
	$selectedBrand = $_POST['brand'];
	
	$selectedChannel = $_POST['channel'];
	
	$selectedProducts = getProducts($selectedBrand);
	
	if ($selectedChannel == 'eBay') {
	
		foreach ($selectedProducts as $i => $item) {
		
			$eBayOutput[$i]['Part Number'] = $item['ISIS SKU'];
			
			$eBayOutput[$i]['Manufacturer Part Number'] = $item['Manufacturer SKU'];
			
			$eBayOutput[$i]['Manufacturer'] = $item['Manufacturer'];
			
			if (strpos("|", $item['Category']) !== false) {
			
				$multiCategories = explode("|", $item['Category']);

				$eBayOutput[$i]['Category Number 1'] = $globalCategories[$multiCategories[0]]['ebay'];
				
				$eBayOutput[$i]['Category Number 2'] = $globalCategories[$multiCategories[1]]['ebay'];
				
				$eBayOutput[$i]['Store Category 1'] = $globalCategories[$multiCategories[0]]['ebaystore'];
				
				$eBayOutput[$i]['Store Category 2'] = $globalCategories[$multiCategories[1]]['ebaystore'];
				
			}
			
			else {
			
				$eBayOutput[$i]['Category Number 1'] = $globalCategories[$item['Category']]['ebay'];
				
				$eBayOutput[$i]['Category Number 2'] = '';
			
				$eBayOutput[$i]['Store Category 1'] = $globalCategories[$item['Category']]['ebaystore'];
				
				$eBayOutput[$i]['Store Category 2'] = '';
			
			}
		
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
			
			
			
		
		}
	
		var_dump($selectedProducts); var_dump($eBayOutput);
	
	}

}

}

?>

</body>
</html>