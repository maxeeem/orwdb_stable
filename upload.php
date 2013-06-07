<!-- @Author : Max Poole

@Purpose : See MAiN section, near the bottom of the script 

@Credits : Jeffrey Tu (CSS & jQuery) -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ORWDB - Upload File</title>
<head>

<?php $p = ""; require("styling/header-script.php"); ?>

<script type="text/javascript">

function makeTabs(tabs) {

	nav = document.getElementById('tabs-list')

	nav.innerHTML = '<ul>' + tabs + '</ul>'
	
return 1 }

function addOne(type, i, item, line) {
	
	var xmlhttp
	
	item = item.replace(/&/g,'%26')

	xmlhttp = new XMLHttpRequest()

	xmlhttp.onreadystatechange = function() {
  
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		
			var done = "<a class='addbutton' style='color:#fff'>"+xmlhttp.responseText+"</a>"
			
			document.getElementById("add"+i).innerHTML = done
    
		}
  
	}

	if (type == "category") xmlhttp.open("GET","admin/addOne.php?type=category&category="+item,true)

	else if (type == "filter") xmlhttp.open("GET","admin/addOne.php?type=filter&filter="+item,true)

	else if (type == "brand") xmlhttp.open("GET","admin/addOne.php?type=brand&brand="+item+"&line="+line,true)
	
	xmlhttp.send()

return 1 }

function addFilterValues(i, a, arr0, arr1) {

	var xmlhttp

	a = a.replace(/&/g,'%26')
	
	arr0 = arr0.replace(/&/g,'%26')
	
	arr1 = arr1.replace(/&/g,'%26')

	xmlhttp = new XMLHttpRequest()

	xmlhttp.onreadystatechange = function() {
	
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		
			var done = "<a class='addbutton' style='color:#fff'>"+xmlhttp.responseText+"</a>"
			
			document.getElementById("add"+i).innerHTML = done
    
		}
  
	}
	
	xmlhttp.open("GET","admin/addFilterValues.php?filter="+a+"&category="+arr0+"&value="+arr1,true)

	xmlhttp.send()

return 1 }

function listCategories() {

	var xmlhttp

	xmlhttp = new XMLHttpRequest()

	xmlhttp.onreadystatechange = function() {
	
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
    
			document.getElementById("dialog").innerHTML = xmlhttp.responseText
			
			$("#catbrowser").menu()
    
		}
  
	}
	
	xmlhttp.open("GET","listCategories.php",true)

	xmlhttp.send()

return 1 }

function highlightStep(step) {

	$("#"+step).removeClass("steps-boxes").addClass("steps-boxes-highlighted")

return 1 }

function reload() {
	
	var xmlhttp

	xmlhttp = new XMLHttpRequest()

	xmlhttp.onreadystatechange = function() {
	
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) window.location.href = "upload.php"

	}

	xmlhttp.open("GET", "admin/reload.php?standalone=false", true)

	xmlhttp.send()

return 1 }

</script>

</head>

<body>

<?php

{# iNCLUDES

require "db/.db-info.php";

require "db/.mysql.php";

require "styling/header.html"; 

require "styling/steps.html";

}

{# FUNCTiONS

{# --Getters

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getFile() { global $file;

	if (!file_exists("files/file.csv")) copy($_FILES['userfile']['tmp_name'], "files/file.csv");
	
	$file = fopen("files/file.csv", "r");

return 1; }

function getFilters() { global $db;
	
	$filters = $db->filters;
	
	$res = $filters->find();
	
	foreach ($res as $rows) { 
		
		if (array_key_exists('values', $rows)) {
		
			foreach ($rows['values'] as $r) $dbfilters[$rows['name']][$r['category']] = $r['values']; }
		
		else $dbfilters[$rows['name']] = array();	}

return isset($dbfilters) ? $dbfilters : array(); }

function getBrands() { global $db;
	
	$brands = $db->brands;
	
	$res = $brands->find();

	foreach ($res as $r) $db_brands[] = $r['brand'];

return isset($db_brands) ? $db_brands : array(); }

function getCategories() { global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();

	foreach ($res as $r) $db_categories[] = $r['orw'];

return isset($db_categories) ? $db_categories : array(); }

function getHeaders() { global $db;
	
	$headers = $db->headers;
	
	$res = $headers->findOne();

return $res['headers']; }

function getISIS($line) { global $mysqli;

	$search = "SELECT * FROM `table 1` WHERE `LineCode` = '" . $line . "'";

	$res = mysqli_query($mysqli, $search);

	$res->data_seek(0);
	
	while ($row = $res->fetch_assoc()) $ISIS[$row['Part Number']][] = $row;
	
return $ISIS; }

function processRows($file) { global $headers, $rows;
	
	$c = 2;

	while ($row = fgetcsv($file)) {

		foreach ($row as $i => $datum) {
		
			if (array_key_exists($i, $headers)) $data[$headers[$i]] = validateValues($headers[$i], $datum, $c);
			
			elseif (array_key_exists($i, $headers['filters'])) {
			
				if ($datum != '')	$filter[$headers['filters'][$i]] = validateFilters($data['Category'], $headers['filters'][$i], $datum, $c); }
			
			else exit("<small>Unrecognized columns in row {$c}.</small>"); }
		
		$rows['rows'][$c] = $data;
		
		$rows['filters'][$c] = $filter;
		
		$c++; }
	
	fclose($file); 
	
return 1; }

}

{# --Validators

function inISIS($sku, $c) { global $ISIS, $current_linecode;

	$line = substr($sku, 0, 3);

	$pn = substr($sku, 3);

	if ($c == 2) $ISIS[$line] = getISIS($line);
	
	elseif ($c > 2 && $line != $current_linecode && !array_key_exists($line, $ISIS)) $ISIS[$line] = getISIS($line);
	
	$current_linecode = $line;
	
return (array_key_exists($pn, $ISIS[$line])) ? true : false; }

function validateHeaders($array) { global $headers, $validHeaders, $validFilters;
	
	$headers = $validHeaders;
	
	foreach ($array as $a => $arr) {
	
		if (array_key_exists($a, $validHeaders)) { if ($arr !== $validHeaders[$a]) $err[$validHeaders[$a]] = $arr; }
	
		elseif (!array_key_exists($arr, $validFilters)) $new[] = $arr; 
		
		else $headers['filters'][$a] = $arr; } 
	
	if (!empty($err)) printErrors($err);
	
	if (!empty($new)) addFilters($new);

return 1; }

function validateValues($name, $value, $c) {
	
	global $validBrands, $validCategories, $ISIS;
	
	global $errors, $dir, $current_linecode, $brand_linecode;
	
	global $UPCs, $SKUs, $char, $IMGs, $PDFs;
	
	$data = 'invalid';
	
	if (strpos($name, 'Manufacturer') !== false) { 
		
		if (in_array($value, $validBrands)) $data = $value; 
		
		else { $brand_linecode[$value] = $current_linecode;	$errors[$name][$value][] = $c; } }
	
	if ((strpos($name, 'UPC') !== false)) { if (empty($value)) $data = ''; else { 
		
		if (is_numeric($value) && (strlen($value) == 12)) {
		
			if (!array_key_exists($value, $UPCs)) { $data = $value;	$UPCs[$value][] = $c;	}
			
			else { $UPCs[$value][] = $c; $errors[$name][$value] = $UPCs[$value]; } }
		
		else $errors[$name][$value][] = $c;	} }
	
	if (strpos($name, 'ISIS SKU') !== false) { $data = $value;
		
		if (inISIS($value, $c)) {
		
			if (!array_key_exists($value, $SKUs)) $SKUs[$value][] = $c;
			
			else { $SKUs[$value][] = $c; $errors[$name][$value] = $SKUs[$value]; } }
		
		else $errors[$name][$value][] = $c;	}	

	if (strpos($name, 'Part Name') !== false) $data = $value; // no validation
	
	if (strpos($name, 'Category') !== false) $data = $value; // validation done in addCategories
	
	if (strpos($name, 'Bullet Point') !== false) { 
		
		if (strlen($value) <= 200) $data = $value; 
		
		else $errors[$name][] = $c;	}

	if (strpos($name, 'Description') !== false) { $data = $value;
	
		$char += strlen($value);
		
		$n = explode(' ', $name);
		
		if ($n[1] == '3') { 
		
			if ($char >= 1500) { $errors['Description'][] = $c; $char = 0; }
			
			else $char = 0;	} }

	if (strpos($name, 'Image') !== false) { $data = $value;
		
		if ($value != '') {	$IMGs[$current_linecode][] = $value;
		
			/*if (!file_exists($dir . $current_linecode . '/orwdb/images/' . $value)) $errors[$name][$value][] = $c; //comment out the skip images check*/ } }

	if (strpos($name, 'Instructions') !== false) { $data = $value;
		
		if ($value != '') { $PDFs[$current_linecode][] = $value;
		
			if (!file_exists($dir . $current_linecode . '/orwdb/instructions/' . $value)) $errors[$name][$value][] = $c; } }
	
return $data; }

function validateFilters($category, $name, $value, $c) { global $validFilters, $errors;
	
	$data = 'invalid';
	
	$categories = explode('|', $category);
	
	foreach ($categories as $cat) {
		
		if (array_key_exists($cat, $validFilters[$name]) && in_array($value, $validFilters[$name][$cat])) $data = $value;
		
		else $errors['Filters'][$name][$value][$cat][] = $c; }
	
return $data; }

}

{# --Admin

function addFilters($array) {	global $validFilters;
	
	$filters = $validFilters; ksort($filters);
	
	echo "<script language='javascript'>highlightStep('step3')</script>";
	
	$select = "<p><select id='filters_N'><option>Filters in the database</option>";
	
	$select_T = "<select id='filters_T'><option>Title Filters</option>";
	
	foreach ($filters as $name => $categories) {
	
		if (strpos($name, "T_") === false) $select .= "<option>$name</option>";
		
		else $select_T .= "<option>$name</option>"; }
	
	echo $select . "</select>";
	
	echo $select_T . "</select></p>";
	
	$i = 0;
	
	foreach ($array as $arr) {
		
		$response = "<hr><div class='highlighblock' style=\"line-height: 1.5em;\"><span id=\"add$i\"><a class='addbutton' href=\"#\" onclick=\"addOne('filter',$i,'$arr'); return false;\">Add</a></span>";
		
		$response .= " $arr</div>";
	
		echo $response;	$i++; }
	
	echo '<a id="continueclick" class="continueclick" href="upload.php" onclick="processtext()">Continue</a>';
	
exit(); }

function addCategories($file) { global $validCategories;

	$add = false; $skip = fgetcsv($file); $e = 2;
	
	while ($row = fgetcsv($file)) { if (empty($row[4])) $empty[] = $e; else { 
		
		if (strpos($row[4], "|") !== false) { $cats = explode('|', $row[4]);
		
			foreach ($cats as $one_cat) $categories[] = $one_cat; }
		
		else $categories[] = $row[4]; }
	
		$e++; }
	
	rewind($file);
	
	if (!empty($empty)) { $rows = implode(',', $empty); 
		
		exit("Rows $rows are empty or missing category values<br/><input type='button' value='Reload' onclick='reload()'>"); }
	
	sort($categories);
	
	$i = 0;
	
	foreach (array_unique($categories) as $cat) { if (!in_array($cat, $validCategories)) { $add = true;
		
		$response = "<div class='highlightblock'><span id=\"add$i\"><a class='addbutton' href=\"#\" onclick=\"addOne('category',$i,'$cat'); return false;\">Add</a></span>";
		
		$response .= " $cat</div>";
		
		echo $response; $i++; } }
	
	if ($add) {
		
		echo "<script language='javascript'>listCategories()</script>";
		
		echo "<script language='javascript'>highlightStep('step2')</script>";
		
		echo '<a id="continueclick" class="continueclick" href="upload.php" onclick="processtext()">Continue</a>';
		
		exit(); }

return 1; }

function insert($data) { global $db;
	
	$products = $db->products;
	
	foreach ($data['rows'] as $i => $row) {
		
		$document = $row;
		
		$document['Manufacturer SKU'] = substr($row["ISIS SKU"], 3);
		
		if (array_key_exists($i, $data['filters'])) $document['Filters'] = $data['filters'][$i];
		
		$checkSKU = $products->find(["ISIS SKU" => $row["ISIS SKU"]]);
		
		$SKU = iterator_to_array($checkSKU);
		
		if (empty($SKU)) $products->insert($document);
		
		else $products->update(["ISIS SKU" => $row["ISIS SKU"]], $document); }

return 1; }

function insertFiltersByCategory() { global $db;
	
	$categories = $db->categories;
	
	$filters = $db->filters;
	
	$res = $filters->find();
	
	foreach ($res as $rows) { if (array_key_exists('values', $rows)) {
		
		foreach ($rows['values'] as $r) $categories->update(['orw' => $r['category']], ['$addToSet' => ['filters' => $rows['name']]]); } }

return 1; }

function cleanup($array, $type) { global $dir;
	
	if (!empty($array)) { $linecodes = array_keys($array);
		
		foreach ($linecodes as $line) {	$img_dir = $dir . $line . '/orwdb/' . $type . '/';
			
			if (!file_exists($dir . $line . '/orwdb/extra')) mkdir($dir . $line . '/orwdb/extra/');
			
			if (!file_exists($dir . $line . '/orwdb/extra/' . $type)) mkdir($dir . $line . '/orwdb/extra/' . $type);
			
			foreach (glob($img_dir . "*") as $file) { $f = basename($file);
				
				if (!in_array($f, $array[$line])) {	copy($file, $dir . $line . '/orwdb/extra/' . $type . '/' . $f); unlink($file); } } } }
	
return 1; }

}

{# --Output

function printErrors($array) { global $validFilters, $dir, $current_linecode, $brand_linecode;

	$bullets = ['Bullet Point 1', 'Bullet Point 2', 'Bullet Point 3', 'Bullet Point 4', 'Bullet Point 5'];
	
	$images = ['Image 1', 'Image 2', 'Image 3', 'Image 4', 'Image 5', 'Image 6', 'Image 7', 'Image 8'];

	echo '<div id="tabs"><span id="tabs-list"></span>';
	
	$tabs = null;

  foreach ($array as $type => $error) {
	
		if ($type == 'Manufacturer') { $tabs .= "<li><a href='#manufacturer'>MANUFACTURER</a></li>"; $i = 0; foreach ($error as $name => $rows) {
			
			$line = $brand_linecode[$name];
		
			$range = implode(', ', $rows);
			
			$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\">more</a></sup>" : $range;
		
			$response = "<div class='highlightblock' style=\"line-height: 1.5em;\"><span id=\"add$i\"><a class='addbutton' href=\"#\" onclick=\"addOne('brand',$i,'$name','$line'); return false;\">Add</a></span>";
	
			$response .= " Manufacturer [$name] does not exist in the database. Row(s) $show</div>";
		
			echo "<div id='manufacturer'>" . $response . "</div>"; $i++; } }
		
		if ($type == 'UPC') { $tabs .= "<li><a href='#upc'>UPC</a></li>"; foreach ($error as $name => $rows) {
			
			$response = "UPC [$name] is ";
			
			$response .= (count($rows) > 1) ? "repeated in rows " . implode(', ', $rows) : 'invalid in row ' . $rows[0];

			$response .= "<br />";				
			
			echo "<div id='upc'>" . $response . "</div>"; } }
		
		if ($type == 'ISIS SKU') { $tabs .= "<li><a href='#isis-sku'>ISIS SKU</a></li>"; $response = null; foreach ($error as $name => $rows) {
			
			$response .= "<div class='upload-isis-sku-line'><div class='upload-isis-sku-name'>[$name]</div>";
			
			$response .= (count($rows) > 1) ? "<div class='upload-isis-sku-dne'>is repeated in rows</div>" . implode(', ', $rows) : "<div class='upload-isis-sku-dne'>does not exist in ISIS. Row " . $rows[0];  
			
			$response .= '</div></div><br>'; }
			
			echo "<div id='isis-sku'>" . $response . "</div>"; }
		
		if (in_array($type, $bullets)) {
		
			$tabs .= "<li><a href='#bullets'>BULLET POINTS</a></li>";
		
			$response = "$type is too long in row(s) " . implode(', ', $error) . "<br />";
			
			echo "<div id='bullets'>" . $response . "</div>"; }
		
		if ($type == 'Description') {
		
			$tabs .= "<li><a href='#description'>DESCRIPTION</a></li>";
			
			$range = implode(', ', $error);
			
			$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\">more</a></sup>" : $range;
		
			$response = "$type is too long in row(s) $show<br />";
			
			echo "<div id='description'>" . $response . "</div>"; }

		if (in_array($type, $images)) {
		
			$tabs .= "<li><a href='#images'>IMAGES</a></li>";
			
			$response = 'Directory: ' . $dir . $current_linecode . '/orwdb/images/<br />'; //@todo change to account for multiple linecodes per sheet
			
			foreach ($error as $image => $rows) {
			
				$range = implode(', ', $rows);
				
				$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\">more</a></sup>" : $range;
				
				$response .= "<table><tr><td width='200' height='25'>$image</td><td>Row(s) $show</td></tr></table>"; }
			
			echo "<div id='images'>" . $response . "</div>"; }

		if ($type == "Instructions") { $tabs .= "<li><a href='#instructions'>INSTRUCTIONS</a></li>"; $response = ''; foreach ($error as $instructions => $rows) {
			
			$range = implode(', ', $rows);
			
			$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\">more</a></sup>" : $range;
			
			$response .= "$instructions&nbsp;&nbsp;&nbsp;&nbsp; Row(s) $show<br />"; }
			
			echo "<div id='instructions'>" . $response . "</div>"; } //@todo make it work more like images, with an address to the folder
		
		if ($type == 'Filters') { $tabs .= "<li><a href='#filters-u'>FILTERS</a></li>";	$i = 1;	$response = '';	foreach ($error as $filter => $value) {
			
			foreach ($value as $val => $info) {	foreach ($info as $cat => $rows) {
				
				$vals = "empty"; $t_vals = $vals;
				
				$range = implode(', ', $rows);
	
				$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\" title='$range'>more</a></sup>" : $range;
				
				if (isset($validFilters[$filter][$cat])) {

					asort($validFilters[$filter][$cat]);
					
					$vals = "[$filter] in $cat\\n\\n" . implode("\\n", $validFilters[$filter][$cat]);
					
					$vals = str_replace("'", "\'", $vals);
					
					$t_vals = implode("\n", $validFilters[$filter][$cat]); }
				
				$js_val = str_replace("'", "\'", $val);
				
				$response .= "<div class='highlightblock'><div  style='display:inline-block; vertical-align: top;' id=\"add$i\">&nbsp;<a class='addbutton' href=\"#\" onclick=\"addFilterValues($i,'$filter','$cat','$js_val'); return false;\">Add</a></div>";

				$response .= " <div style='display:inline-block; margin-left: 10px'><font face='monospace'>[$filter";
				
				$response .= "<sup><a href=\"javascript:alert('$vals')\" style=\"text-decoration: none;\" title =\"$t_vals\">&lowast;</a></sup>";
				
				$response .= "]</font> value <font face='monospace'>[$val]</font> is missing<br>";
				
				$response .= "<div><font face='monospace' style=\"background-color: #e5e0ec;\">$cat</font> in row(s) $show<hr></div></div></div>";
				
				$i++; } }	}
		
			echo "<div id='filters-u'>" . $response . "</div>";	} }
	
	echo "<script type='text/javascript'>makeTabs(\"$tabs\"); $(\"#tabs\").tabs()</script>";
	
	$step = "<script language='javascript'>highlightStep('";
	
	if (count($array) == 1 && key($array) == "ISIS SKU") echo $step . "step5')</script><a class='continueclick' id='continueclick' onclick='processtext()' href='?skip_isis'>Insert Into Database</a>";
	
	else echo $step . "step4')</script><a id='continueclick' class='continueclick' href='upload.php' onclick='processtext()'>Continue</a>";

exit(); }

}

}

{# VARiABLES

{# --Const

$form = <<<EOT
				<div class='body-margin2'><br><br>
				<center>
				<form enctype="multipart/form-data" action="{$_SERVER['PHP_SELF']}" method="POST">
				<button type='button' class='generalbutton' onclick="document.getElementById('submitbutton').click()" ><div id='filefield'>Choose File</div></button>
				<div class='uploadwrap'><input id='submitbutton' name="userfile" onchange="getfilename()" type="file"/></div>
				<input id='process' class='generalbutton' onclick='processtext()' type="submit" value="Submit" />
				</form>
				</center>
				</div>
EOT;

$dir = "//orw-file-server/shared/Marketing/Photo Product/";

$margin = "<div id='body-margin'>";

}

{# --Init

$ISIS = array();

$errors = array();

$UPCs = array();

$SKUs = array();

$IMGs = array();

$PDFs = array();

$char = 0;

$current_linecode = '';

$brand_linecode = array();

$file = null;

$headers = null;

$rows = null;

}

{# --Var

$db = dbConnect(DBHOST, DBNAME);

$validHeaders = getHeaders();

$validBrands = getBrands();	

$validCategories = getCategories();

$validFilters = getFilters();

}

}

{# MAiN

echo $margin;

if (empty($_FILES) && !file_exists("files/file.csv")) { echo $form;	echo "<script language='javascript'>highlightStep('step1')</script>"; }

else { getFile(); addCategories($file); validateHeaders(fgetcsv($file)); processRows($file); 

	if (empty($errors) || isset($_GET['skip_isis'])) { 
	
		insert($rows); insertFiltersByCategory();
		
		cleanup($IMGs, "images"); cleanup($PDFs, "instructions");
		
		echo 'Database insertion successful.';
		
		echo "<p>Perform <a href='admin/IsisSync.php'>ISIS Syncronization</a>. It may take a while so please be patient.</p>"; }
	
	else printErrors($errors); }

}

{# EXiT

if (file_exists("files/file.csv")) unlink("files/file.csv");

}

?>

</div>
</body>
</html>