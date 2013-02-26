<html>
<title>ORW DB</title>
<head>
<script>

function addOne(type, i, item) {
var xmlhttp;

item = item.replace(/&/g,'%26')

xmlhttp=new XMLHttpRequest();

xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("add"+i).innerHTML=xmlhttp.responseText;
    }
  }

if (type == "category") {	
	xmlhttp.open("GET","admin/addOne.php?type=category&category="+item,true);
}

else if (type == "filter") {	
	xmlhttp.open("GET","admin/addOne.php?type=filter&filter="+item,true);
}

else if (type == "brand") {	
	xmlhttp.open("GET","admin/addOne.php?type=brand&brand="+item,true);
}
	
xmlhttp.send();
}

function addFilterValues(i, a, arr0, arr1) {
var xmlhttp;

a = a.replace(/&/g,'%26')
arr0 = arr0.replace(/&/g,'%26')
arr1 = arr1.replace(/&/g,'%26')

xmlhttp=new XMLHttpRequest();

xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("add"+i).innerHTML=xmlhttp.responseText;
    }
  }
	
xmlhttp.open("GET","admin/addFilterValues.php?filter="+a+"&category="+arr0+"&value="+arr1,true);

xmlhttp.send();
}

function listCategories(categories) {

var categories = '<h3>See <a href=\'javascript:popup("listCategories.php")\'>categories</a> already in the database.&nbsp;&nbsp;&nbsp;<input type="button" value="Reload" onclick="reload()"></h3><hr>'

document.getElementById("top").innerHTML = categories

}

function reload() {
var reload;

reload = new XMLHttpRequest();

reload.onreadystatechange=function()
{
  if (reload.readyState==4 && reload.status==200)
  {
    window.location.href="upload.php";
  }
}

reload.open("GET", "admin/reload.php?standalone=false", true);

reload.send();
}

function popup(url) {

var popup = window.open(url, 'Categories', 'height=700,width=640');

if (window.focus) {popup.focus()}

}

</script>
</head>
<body>

<?php

{# iNCLUDES

require "db/.db-info.php";

require "db/.mysql.php";

}

{# FUNCTiONS

{# Getters

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
	return $db;

}

function getFile() {

	global $file;

	if (!file_exists("files/file.csv")) copy($_FILES['userfile']['tmp_name'], "files/file.csv");
	
	$file = fopen("files/file.csv", "r");

}

function getFilters() {

	global $db;
	
	$filters = $db->filters;
	
	$res = $filters->find();
	
	foreach ($res as $rows) { 
		
		if (array_key_exists('values', $rows)) {
		
			foreach ($rows['values'] as $r) {
			
				$dbfilters[$rows['name']][$r['category']] = $r['values'];
			
			}

		}
		
		else $dbfilters[$rows['name']] = array();
		
	}

	return isset($dbfilters) ? $dbfilters : array();

}

function getBrands() {

	global $db;
	
	$brands = $db->brands;
	
	$res = $brands->find();

	foreach ($res as $r) $db_brands[] = $r['brand'];

	return $db_brands;

}

function getCategories() {

	global $db;
	
	$categories = $db->categories;
	
	$res = $categories->find();

	foreach ($res as $r) $db_categories[] = $r['orw'];

	return isset($db_categories) ? $db_categories : array();

}

function getHeaders() {

	global $db;
	
	$headers = $db->headers;
	
	$res = $headers->findOne();

	return $res['headers'];

}

function getISIS($line) {

	global $mysqli;

	$search = "SELECT * FROM `table 1` WHERE `LineCode` = '" . $line . "'";

	$res = mysqli_query($mysqli, $search);

	$res->data_seek(0);
	
	while ($row = $res->fetch_assoc()) $ISIS[$row['Part Number']][] = $row;
	
	return $ISIS;

}

function processRows($file) {

	global $headers, $rows;
	
	$c = 2;

	while ($row = fgetcsv($file)) {

		foreach ($row as $i => $datum) {
		
			if (array_key_exists($i, $headers)) $data[$headers[$i]] = validateValues($headers[$i], $datum, $c);
			
			elseif (array_key_exists($i, $headers['filters'])) $filter[$headers['filters'][$i]] = validateFilters($data['Category'], $headers['filters'][$i], $datum, $c);
			
			else exit("<small>Unrecognized columns in row {$c}.</small>");
		
		}
		
		$rows['rows'][$c] = $data;
		
		$rows['filters'][$c] = $filter;
		
		$c++;

	}
	
	fclose($file);

}

}

{# Validators

function inISIS($sku, $c) {

	global $ISIS, $current_linecode;

	$line = substr($sku, 0, 3);

	$pn = substr($sku, 3);

	if ($c == 2) $ISIS[$line] = getISIS($line);
	
	elseif ($c > 2 && $line != $current_linecode && !array_key_exists($line, $ISIS)) $ISIS[$line] = getISIS($line);
	
	$current_linecode = $line;
	
	return (array_key_exists($pn, $ISIS[$line])) ? true : false;
	
}

function validateHeaders($array) {

	global $headers, $validHeaders, $validFilters;
	
	$headers = $validHeaders;
	
	foreach ($array as $a => $arr) {
	
		if (array_key_exists($a, $validHeaders)) { if ($arr !== $validHeaders[$a]) $err[$validHeaders[$a]] = $arr; }
	
		elseif (!array_key_exists($arr, $validFilters)) $new[] = $arr; 
		
		else $headers['filters'][$a] = $arr;
	
	} 
	
	if (!empty($err)) printErrors($err);
	
	if (!empty($new)) addFilters($new);

}

function validateValues($name, $value, $c) {
	
	global $validBrands, $validCategories, $ISIS;
	
	global $errors, $dir, $current_linecode;
	
	global $UPCs, $SKUs, $char;
	
	$data = 'invalid';
	
	if (strpos($name, 'Manufacturer') !== false) { 
		
		if (in_array($value, $validBrands)) $data = $value; 
		
		else $errors[$name][$value][] = $c;
		
	}
	
	if ((strpos($name, 'UPC') !== false) && !empty($value)) { 
		
		if (is_numeric($value) && (strlen($value) == 12)) {
		
			if (!array_key_exists($value, $UPCs)) {
			
				$data = $value;
		
				$UPCs[$value][] = $c;
		
			}
			
			else {
			
				$UPCs[$value][] = $c;
			
				$errors[$name][$value] = $UPCs[$value];
		
			}
		
		}
		
		else $errors[$name][$value][] = $c;
		
	}
	
	if (strpos($name, 'ISIS SKU') !== false) { 
	
		$data = $value;
		
		if (inISIS($value, $c)) {
		
			if (!array_key_exists($value, $SKUs)) {
			
				$SKUs[$value][] = $c;
		
			}
			
			else {
			
				$SKUs[$value][] = $c;
				
				$errors[$name][$value] = $SKUs[$value];
			
			}
		
		}
		
		else $errors[$name][$value][] = $c;
		
	}	

	if (strpos($name, 'Part Name') !== false) { 
		
		$data = $value; // no validation
		
	}
	
	if (strpos($name, 'Category') !== false) { 
		
		$data = $value; // no validation
		
		// $categories = explode('|', $value);
		
		// foreach ($categories as $cat) {
		
			// if (in_array($cat, $validCategories)) $data = $value; 
			
			// else $errors[$name][$cat][] = $c;
		
		// }
		
	}
	
	if (strpos($name, 'Bullet Point') !== false) { 
		
		if (strlen($value) <= 200) $data = $value; 
		
		else $errors[$name][] = $c;
		
	}

	if (strpos($name, 'Description') !== false) { 
	
		$char += strlen($value);
		
		$data = $value;
		
		$n = explode(' ', $name);
		
		if ($n[1] == '3') { 
		
			if ($char >= 1500) { $errors['Description'][] = $c; $char = 0; }
			
			else $char = 0;
			
		}
		
	}

	if (strpos($name, 'Image') !== false) { 
		
		$data = $value;
		/*if (file_exists($dir . $current_linecode . '/images/' . $value)) $data = $value; 
			
		else $errors[$c][$name] = $value;*/
		
	}

	if (strpos($name, 'Instructions') !== false) { 
		
		$data = $value;
		/*if (file_exists($dir . $current_linecode . '/instructions/' . $value)) $data = $value; 
			
		else $errors[$c][$name] = $value;*/
		
	}
	
	return $data;

}

function validateFilters($category, $name, $value, $c) {

	global $validFilters, $errors;
	
	$data = 'invalid';
	
	$categories = explode('|', $category);
	
	foreach ($categories as $cat) {
	
		if ($value == '') $data = $value;
		
		elseif (array_key_exists($cat, $validFilters[$name]) && in_array($value, $validFilters[$name][$cat])) $data = $value;
		
		else $errors['Filters'][$name][$value][$cat][] = $c;
	
	}
	
	return $data;

}

}

{# Admin

function addFilters($array) {

	global $validFilters;

	$i = 0;
	
	$filters = $validFilters;
	
	ksort($filters);
	
	$select = "<div style='text-align: center;'><select id='filters'><option>Filters in the database</option>";
	
	$select_T = "<select id='filters_T'><option>Title Filters</option>";
	
	foreach ($filters as $name => $categories) {
	
		if (strpos($name, "T_") === false) $select .= "<option>$name</option>";
		
		else $select_T .= "<option>$name</option>";
	
	}
	
	echo $select . "</select>";
	
	echo $select_T . "</select></div>";
	
	foreach ($array as $arr) {
		
		$response = "<div style=\"line-height: 1.5em;\"><span id=\"add$i\"><a href=\"#\" onclick=\"addOne('filter',$i,'$arr'); return false;\">Add</a></span>";
		
		$response .= " - $arr</div>";
	
		$i++;
	
		echo $response;
	
	}
	
	echo '<br /><a href="upload.php">Continue</a>';
	
	exit();

}

function addCategories($file) {

	global $validCategories;

	$add = false;
	
	$skip = fgetcsv($file);
	
	$e = 2;
	
	while ($row = fgetcsv($file)) {
	
		if (empty($row[4])) $empty[] = $e;
	
		else {
		
			if (strpos($row[4], "|") !== false) {
			
				$cats = explode('|', $row[4]);
				
				foreach ($cats as $single_cat) $categories[] = $single_cat;
			
			}
			
			else $categories[] = $row[4];
	
		}
	
		$e++;
	
	}
	
	rewind($file);
	
	if (!empty($empty)) { $rows = implode(',', $empty); exit("Rows $rows are empty or missing category values<br/><input type='button' value='Reload' onclick='reload()'>"); }
	
	$i = 0;
	
	echo '<div id="top"></div>';
	
	sort($categories);
	
	foreach (array_unique($categories) as $cat) {
	
		if (!in_array($cat, $validCategories)) {
		
			$add = true;
			
			$response = "<div style=\"line-height: 1.5em;\"><span id=\"add$i\"><a href=\"#\" onclick=\"addOne('category',$i,'$cat'); return false;\">Add</a></span>";
		
			$response .= " - $cat</div>";
		
			$i++;
		
			echo $response;
		
		}
	
	}
	
	if ($add) {
		
		echo "<script language='javascript'>listCategories()</script>";
		
		echo '<hr><a href="upload.php">Continue</a>';

		exit();

	}
	
}

function insert($data) {
	
	global $db;
	
	$products = $db->products;
	
	foreach ($data['rows'] as $i => $row) {
		
		$document = $row;
		
		$document['Manufacturer SKU'] = substr($row["ISIS SKU"], 3);
		
		if (array_key_exists($i, $data['filters'])) $document['Filters'] = $data['filters'][$i];
		
		$checkSKU = $products->find(["ISIS SKU" => $row["ISIS SKU"]]);
		
		$SKU = iterator_to_array($checkSKU);
		
		if (empty($SKU)) $products->insert($document);
		
		else $products->update(["ISIS SKU" => $row["ISIS SKU"]], $document);
	
	}
	
	// var_dump($rows);

}

function insertFiltersByCategory() {

	global $db;
	
	$categories = $db->categories;
	
	$filters = $db->filters;
	
	$res = $filters->find();
	
	foreach ($res as $rows) { 
		
		if (array_key_exists('values', $rows)) {
		
			foreach ($rows['values'] as $r) {
			
				$categories->update(['orw' => $r['category']], ['$addToSet' => ['filters' => $rows['name']]]);

			}
			
		}

	}

}

}

{# Output

function printErrors($array) { global $validFilters;

	$bullets = ['Bullet Point 1', 'Bullet Point 2', 'Bullet Point 3', 'Bullet Point 4', 'Bullet Point 5'];

  foreach ($array as $type => $error) {
	
		if ($type == 'Manufacturer') {

			echo "<h3>MANUFACTURER</h3>";
		
			$i = 0;
		
			foreach ($error as $name => $rows) {
			
				$range = implode(', ', $rows);
				
				$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\">more</a></sup>" : $range;
			
				$response = "<div style=\"line-height: 1.5em;\"><span id=\"add$i\"><a href=\"#\" onclick=\"addOne('brand',$i,'$name'); return false;\">Add</a></span>";
		
				$response .= " - Manufacturer [$name] does not exist in the database. Row(s) $show</div>";
			
				$i++;
			
				echo $response;
			
			}
		
		}
		
		if ($type == 'UPC') {

			echo "<h3>UPC</h3>";
		
			foreach ($error as $name => &$rows) {
			
				// unset($rows['first']);
			
				$response = "UPC [$name] is ";
				
				$response .= (count($rows) > 1) ? "repeated in rows " . implode(', ', $rows) : 'invalid in row ' . $rows[0];

				$response .= "<br />";				
				
				echo $response;
			
			}
		}
		
		if ($type == 'ISIS SKU') {
		
			echo "<h3>ISIS SKU</h3>";
		
			foreach ($error as $name => $rows) {
			
				$response = "ISIS SKU <font face='monospace'>[$name]</font> ";
				
				$response .= (count($rows) > 1) ? "is repeated in rows " . implode(', ', $rows) : "does not exist in ISIS. Row " . $rows[0];  
				
				$response .= '<br />';
			
				echo $response;
			
			}
		}
		
		if (in_array($type, $bullets)) {
		
			echo "<h3>BULLET POINTS</h3>";
		
			$response = "$type is too long in row(s) " . implode(', ', $error) . "<br />";
			
			echo $response;
		
		}
		
		if ($type == 'Description') {
		
			echo "<h3>DESCRIPTION</h3>";
			
			$range = implode(', ', $error);
			
			$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\">more</a></sup>" : $range;
		
			$response = "$type is too long in row(s) $show<br />";
			
			echo $response;
		
		}
		
		if ($type == 'Filters') {
			
			echo "<h3>FILTERS</h3>";
			
			$i = 1;
		
			foreach ($error as $filter => $value) {

				$response = '';
				
				foreach ($value as $val => $info) {
				
					foreach ($info as $cat => $rows) {
					
						$vals = "empty"; $t_vals = $vals;
						
						$range = implode(', ', $rows);
			
						$show = (strlen($range) > 20) ? substr($range, 0, 20) . "... <sup><a href=\"javascript:alert('$range')\" title='$range'>more</a></sup>" : $range;
						
						if (isset($validFilters[$filter][$cat])) {

							asort($validFilters[$filter][$cat]);
							
							$vals = "[$filter] in $cat\\n\\n" . implode("\\n", $validFilters[$filter][$cat]);
							
							$vals = str_replace("'", "\'", $vals);
							
							$t_vals = implode("\n", $validFilters[$filter][$cat]);						
						
						}
						
						$js_val = str_replace("'", "\'", $val);
						
						$response .= "<div><span id=\"add$i\">&nbsp;<a href=\"#\" onclick=\"addFilterValues($i,'$filter','$cat','$js_val'); return false;\">Add</a></span>";
	
						$response .= " - <font face='monospace'>[$filter";
						
						$response .= "<sup><a href=\"javascript:alert('$vals')\" style=\"text-decoration: none;\" title =\"$t_vals\">&lowast;</a></sup>";
						
						$response .= "]</font> value <font face='monospace'>[$val]</font> is missing<br />";
						
						$response .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face='monospace' style=\"background-color: #e5e0ec;\">$cat</font> in row(s) $show<br /><br /></div>";
						
						$i++;
				
					}
					
				}
				
				echo $response;
			
			}
		
		}
	
	}
	
	echo '<br /><h3><input type="button" value="Reload" onclick="reload()">&nbsp;&nbsp;&nbsp;';
	echo (count($array) == 1) ? '<a href="?skip_isis">Continue</a></h3>' : '<a href="">Continue</a></h3>';
	
	exit();

}

}

}

{# VARiABLES

{# Const

$form = <<<EOT
				<br />
				<center><h2>Choose file to upload</h2>
				<br />
				<form enctype="multipart/form-data" action="{$_SERVER['PHP_SELF']}" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="200000000" />
				<input name="userfile" type="file" />
				<input type="submit" value="Submit" />
				</form>
				</center>
EOT;

$dir = "//orw-file-server/shared/Marketing/Photo Product/";

}

{# Init

$ISIS = array();

$errors = array();

$UPCs = array();

$SKUs = array();

$char = 0;

$current_linecode = '';

$file = null;

$headers = null;

$rows = null;

}

{# Var

$db = dbConnect(DBHOST, DBNAME);

$validHeaders = getHeaders();

$validBrands = getBrands();	

$validCategories = getCategories();

$validFilters = getFilters();

}

}

{# MAiN

if (empty($_FILES) && !file_exists("files/file.csv")) echo $form;

else {

	getFile(); addCategories($file); validateHeaders(fgetcsv($file)); processRows($file); 
	
	if (empty($errors) || isset($_GET['skip_isis'])) { 
	
		insert($rows);
		
		insertFiltersByCategory();
		
		echo 'Success! <a href="index.php">Go Home</a>';
		
	}
	
	else printErrors($errors);
	
	

}

}

{# EXiT

if (file_exists("files/file.csv")) unlink("files/file.csv");

}

?>

</body>
</html>