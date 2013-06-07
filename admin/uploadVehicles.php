<!-- @Author : Max Poole

@Purpose : See MAiN section, near the bottom of the script 

@Credits : Jeffrey Tu (CSS & jQuery) -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ORWDB - Assign Vehicle Compatibilities</title>
<head>

<?php $p = "../"; require($p . "styling/header-script.php"); ?>

<script type="text/javascript">

function addVehicle(i, make, model, start, end, exclude) {

	var xmlhttp
	
	xmlhttp = new XMLHttpRequest()

	xmlhttp.onreadystatechange = function() { if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		
		$("#veh"+i).removeAttr("onclick"); $("#veh"+i).html("Done"); } }
	
	skip = $("#skip"+i).is(":checked")//; console.log(skip)
	
	ebaymodel = $("#ebaymodel"+i).val()//; console.log(ebaymodel)
	
	if (ebaymodel != null && ebaymodel != 'eBay Model') {
	
		if (skip == true) {
		
			xmlhttp.open("GET","addVehicle.php?make="+make+"&model="+model+"&start="+start+"&end="+end+"&exclude="+exclude+"&skip="+skip,true)
			
			xmlhttp.send() }
	
		else {
		
			ebaymake = ($("#ebaymake"+i).length) ? $("#ebaymake"+i).val() : make
			
			ebayextra = $("#ebayextra"+i).val(); console.log(ebayextra)
			
			ebaystart = Math.min.apply(Math, window.json_ebay[ebaymake][ebaymodel]['Years'])//; console.log(ebaystart)
			
			ebayend = Math.max.apply(Math, window.json_ebay[ebaymake][ebaymodel]['Years'])//; console.log(ebayend)
			
			if (window.json_ebay[ebaymake][ebaymodel]['Exclude'] == '') ebayexclude = '[""]'
		
			else ebayexclude = '[' + window.json_ebay[ebaymake][ebaymodel]['Exclude'] + ']'
			
			//console.log(ebayexclude)
		
			xmlhttp.open("GET","addVehicle.php?make="+make+"&model="+model+"&start="+start+"&end="+end+"&exclude="+exclude+"&ebaymake="+ebaymake+"&ebaymodel="+ebaymodel+"&ebayextra="+ebayextra+"&ebaystart="+ebaystart+"&ebayend="+ebayend+"&ebayexclude="+ebayexclude+"&skip="+skip,true) 
			
			xmlhttp.send() } }
	
	else alert('Please select an eBay model for '+make+' '+model)

return 1 }

function ebayYears(i, make) {

	make = $("#ebaymake"+i).val() || make

	ebaymodel = $("#ebaymodel"+i).val()
	
	if (ebaymodel == "eBay Model") { years = ''; exclude = '' }

	else {
	
		years = Math.min.apply(Math, window.json_ebay[make][ebaymodel]['Years']) + ' - ' + Math.max.apply(Math, window.json_ebay[make][ebaymodel]['Years'])

		exclude = window.json_ebay[make][ebaymodel]['Exclude'].join(', ') }
	
	$("#ebayyears"+i).html(years)
	
	$("#ebayexclude"+i).html(exclude)
	
return 1 }

function ebayModels(i) {

	ebaymake = $("#ebaymake"+i).val()
	
	if (ebaymake == "eBay Make") models = ''

	else { models = '<option selected>eBay Model</option>' 
	
		for (m in window.json_ebay[ebaymake]) models += "<option>" + m + "</option>" }
	
	$("#ebaymodel"+i).html(models)
	
return 1 }

</script>

</head>

<body>

<?php

{# iNCLUDES

require "../db/.db-info.php";

require "../db/.mysql.php";

require $p . "styling/header.html"; 

}

{# FUNCTiONS

function dbConnect($dbhost, $dbname) {

	$m = new MongoClient("mongodb://$dbhost");
	
	$db = $m->$dbname;
	
return $db; }

function getFile() { global $applist, $vehicles;
	
	$file = fopen($_FILES['userfile']['tmp_name'], "r"); $skiprow = fgetcsv($file); $i = 1;
	
	while ($row = fgetcsv($file)) { if (validate($i, $row[1], $row[2]) !== '') $err[] = validate($i, $row[1], $row[2]);

		for ($y = $row[1]; $y <= $row[2]; $y++) $vehicles[$row[3]][$row[4]]['Years'][] = (int) $y;
		
		$applist[$row[0]]['Applications'][] = array('Make' => $row[3], 'Model' => $row[4], 'Start year' => $row[1], 'End year' => $row[2], 'Extra' => $row[5]);
		
		unset($years); $i++; }
	
	if (isset($err)) { foreach ($err as $e) echo $e; exit(); }

return 1; }

function validate($i, $start, $end) {

	$date = date("Y") + 2; $err = '';
	
	if (!is_numeric($start) || !is_numeric($end)) $err .= "Year is not numeric in row $i<br />";
	
	if (strlen($start) !== 4 || strlen($end) !== 4) $err .= "Year is not four digits long in row $i<br />";
	
	if ($start < 1900 || $end < 1900) $err .= "Year is less than 1900 in row $i<br />";
	
	if ($start > $date || $end > $date) $err .= "Year is greater than $date in row $i<br />";
	
return $err; }

function getVehicles() { global $db;

	$vehicles = $db->vehicles;
	
	$res = $vehicles->find();

	foreach ($res as $r) {
	
		$validVehicles[$r['Make']][$r['Model']]['Start year'] = $r['Start year'];
		
		$validVehicles[$r['Make']][$r['Model']]['End year'] = $r['End year'];
		
		$validVehicles[$r['Make']][$r['Model']]['Exclude'] = $r['Exclude']; }
	
return (isset($validVehicles)) ? $validVehicles : array(); }

function geteBayVehicles() { global $mysqli_eBay; 

	$search = "SELECT DISTINCT Year, Make, Model FROM `vehicles` ORDER BY `Make`, `Model` ASC";
	
	$res = mysqli_query($mysqli_eBay, $search);
	
	$res->data_seek(0);
	
	while ($row = $res->fetch_assoc()) $ebayveh[$row['Make']][$row['Model']]['Years'][] = $row['Year'];

	foreach (array_keys($ebayveh) as $make) { foreach (array_keys($ebayveh[$make]) as $model) { $years = array_unique($ebayveh[$make][$model]['Years']); 

		sort($years); 

		foreach ($years as $y => $year) { while (array_key_exists(($y + 1), $years) && ($year + 1) < $years[($y + 1)]) $exclude[] = ++$year; }  
		
		if (!isset($exclude)) $exclude = array('');

		$ebayveh[$make][$model]['Exclude'] = $exclude;

		unset($years); unset($exclude); } }
	
return $ebayveh; }

function check($vehicles) { global $validVehicles, $allin, $ebayveh;

	$u = 0;

	echo "<div id='tabs-veh'><ul id='update-new'></ul>";
	
	foreach (array_keys($vehicles) as $make) { if (array_key_exists($make, $validVehicles)) {
		
		foreach (array_keys($vehicles[$make]) as $model) { if (array_key_exists($model, $validVehicles[$make])) { 
		
			$res = array_unique($vehicles[$make][$model]['Years']);
			
			sort($res);
			
			foreach ($res as $i => $r) { while (array_key_exists(($i + 1), $res) && ($r + 1) < $res[($i + 1)]) $exclude[] = ++$r; }  
			
			$startYear = $validVehicles[$make][$model]['Start year'];
			
			$endYear = $validVehicles[$make][$model]['End year'];
			
			if (min($res) < $validVehicles[$make][$model]['Start year']) $startYear = min($res); 
			
			if (max($res) > $validVehicles[$make][$model]['End year']) $endYear = max($res);
			
			if (!isset($exclude)) $exclude = array('');
			
			if ($startYear != $validVehicles[$make][$model]['Start year'] || $endYear != $validVehicles[$make][$model]['End year'] || $exclude != $validVehicles[$make][$model]['Exclude']) {
			
				$updateVehicles[$make][$model]['old']['start'] = $validVehicles[$make][$model]['Start year'];					
				$updateVehicles[$make][$model]['old']['end'] = $validVehicles[$make][$model]['End year'];					
				$updateVehicles[$make][$model]['old']['exclude'] = $validVehicles[$make][$model]['Exclude'];
				$updateVehicles[$make][$model]['new']['start'] = $startYear;
				$updateVehicles[$make][$model]['new']['end'] = $endYear;
				$updateVehicles[$make][$model]['new']['exclude'] = $exclude; }
			
			unset($res); unset($exclude); }
				
			else $addVehicles[$make][$model] = $vehicles[$make][$model]; } }
				
		else $addVehicles[$make] = $vehicles[$make]; }		 
	
	if (!isset($updateVehicles) && !isset($addVehicles)) $allin = true;
	
	if (isset($updateVehicles)) { 
	
		echo "<script type='text/javascript'>$('#update-new').append(\"<li><a href='#veh-tab1'>Update</a></li>\")</script>"; 
		
		echo "<span id='veh-tab1'><div id='accordion'>";

		foreach (array_keys($updateVehicles) as $uMake) { echo "<h3><strong>$uMake</strong></h3><div>"; 
		
			foreach (array_keys($updateVehicles[$uMake]) as $uModel) {
			
				$json_exclude = str_replace('"', '\"', json_encode($updateVehicles[$uMake][$uModel]['new']['exclude']));
				
				$uStart = $updateVehicles[$uMake][$uModel]['new']['start'];
				
				$uEnd = $updateVehicles[$uMake][$uModel]['new']['end'];
			
				echo "<span class='veh-count'><span class='spacer'>
											<button class='generalbutton' id='veh$u' onclick='addVehicle($u, \"$uMake\", \"$uModel\", $uStart, $uEnd, \"$json_exclude\"); return false'>
											Approve</button></span><span class='modelc spacer'>$uModel</span>"; $u++;
											
				echo "<span class='currentyear spacer'><strong>Years:</strong> " . $updateVehicles[$uMake][$uModel]['old']['start'] . '-' . $updateVehicles[$uMake][$uModel]['old']['end'];
				
				echo "<br><strong>Exclude:</strong> " . implodeV2($updateVehicles[$uMake][$uModel]['old']['exclude']) . '</span><span class="spacer2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				
				echo "<span class='newyears spacer'><strong>Years:</strong>" . $updateVehicles[$uMake][$uModel]['new']['start'] . '-' . $updateVehicles[$uMake][$uModel]['new']['end'];
				
				echo "<br><strong>Exclude:</strong> " . implodeV2($updateVehicles[$uMake][$uModel]['new']['exclude']);
				
				echo "</span></span><br>";	} echo "</div>"; }

		echo "</div></span>"; }
	
	if (isset($addVehicles)) { $n = ++$u;
	
		echo "<script type='text/javascript'>$('#update-new').append(\"<li><a href='#veh-tab2'>New</a></li>\")</script>"; 
		
		echo "<span id='veh-tab2'><div id='accordion2'>";

		ksort($addVehicles); 
		
		foreach (array_keys($addVehicles) as $addmake) { echo "<h3><strong>$addmake</strong></h3><div>"; echo '<div class="ebay-img"><img src="../styling/images/ebay_small_horiz.png" width="70" height="28"></div><br>';
			
			ksort($addVehicles[$addmake]);
		
			foreach (array_keys($addVehicles[$addmake]) as $addmodel) { 
			
				$res = array_unique($addVehicles[$addmake][$addmodel]['Years']);
				
				sort($res);
				
				foreach ($res as $i => $r) { while (array_key_exists(($i + 1), $res) && ($r + 1) < $res[($i + 1)]) $exclude[] = ++$r; }  
			
				if (!isset($exclude)) $exclude = array('');
				
				$json_exclude = str_replace('"', '\"', json_encode($exclude)); $start = min($res); $end = max($res);
				
				echo "<span class='veh-color-block'><span class='veh-count'><span class='spacer'><button class='generalbutton' id='veh$n' onclick='addVehicle($n, \"$addmake\", \"$addmodel\", $start, $end, \"$json_exclude\"); return false'>Insert</button></span><span class='modeln spacer'>$addmodel</span>";
				
				echo "<span class='newyears spacer'><strong>Years:</strong> " . $start . '-' . $end;
				
				echo "<br><strong>Exclude:</strong> " . implodeV2($exclude) . '</span></span>';
				
				if (array_key_exists($addmake, $ebayveh)) {
					
					if (array_key_exists($addmodel, $ebayveh[$addmake])) { //both exist
					
						echo "<span class='veh-count-ebay'><span class='spacer'><select id='ebaymodel{$n}'><option>{$addmodel}</option></select>";
					
						echo "<input type='text' id='ebayextra{$n}' size='20' placeholder='Extra'></span>";
					
						echo "<span class='newyears spacer'><strong>Years:</strong> " . min($ebayveh[$addmake][$addmodel]['Years']) . '-' . max($ebayveh[$addmake][$addmodel]['Years']);
					
						echo "<br><strong>Exclude:</strong> " . implodeV2($ebayveh[$addmake][$addmodel]['Exclude']) . "</span>"; 
						
						echo "<span class='modelc spacer'><input type='checkbox' id='skip{$n}'>Skip</span></span>"; }
					
					else { $ebaymodels = "<option selected>eBay Model</option>"; //model doesn't exist

						foreach (array_keys($ebayveh[$addmake]) as $ebaymodel) $ebaymodels .= "<option>" . $ebaymodel . "</option>"; 
					
						echo "<span class='veh-count-ebay'><span class='spacer'><select id='ebaymodel{$n}' onchange='ebayYears($n, \"$addmake\")'>{$ebaymodels}</select>";
					
						echo "<input type='text' id='ebayextra{$n}' size='20' placeholder='Extra'></span>";
					
						echo "<span class='newyears spacer'><strong>Years: </strong><span class='red' id='ebayyears{$n}'></span>";
					
						echo "<br><strong>Exclude: </strong><span class='red' id='ebayexclude{$n}'></span></span><span class='modelc spacer'><input type='checkbox' id='skip{$n}'>Skip</span></span>"; } } 
					
				else { $ebaymakes = "<option selected>eBay Makes</option>"; //make and model don't exist
				
					foreach (array_keys($ebayveh) as $ebaymake) $ebaymakes .= "<option>" . $ebaymake . "</option>";

					echo "<br><span class='modelc spacer'><select id='ebaymake{$n}' onchange='ebayModels($n)'>{$ebaymakes}</select>";
					
					echo "<br><span class='modelc spacer'><select id='ebaymodel{$n}' onchange='ebayYears($n)'></select>";
					
					echo "<br><input type='text' id='ebayextra{$n}' size='20' placeholder='Extra'><input type='checkbox' id='skip{$n}'>Skip</span>";
					
					echo "<span class='newyears spacer'><strong>Years: </strong><span class='red' id='ebayyears{$n}'></span>";
					
					echo "<br><strong>Exclude: </strong><span class='red' id='ebayexclude{$n}'></span></span>";	}
				
				echo "</span><br>";
				
				$n++; unset($res); unset($exclude);	} 
			
			echo "</div>"; }
			
		echo "</div></span></div>"; }
	
	else echo "</div>"; 

	if (!$allin) echo "<a id='continuebutton' class='continueclick2' href='#' onclick='location.reload()'>Continue</a>";
	
return 1; }

function implodeV2($arr) {

	sort($arr);

	while (count($arr) > 0) { 
	
		if (count($arr) > 1) {

			if ($arr[1] !== ($arr[0] + 1)) $res[] = array_shift($arr);
		
			else { $start = array_shift($arr); while (count($arr) > 1 && $arr[1] == ($arr[0] + 1)) array_shift($arr);
				
				$res[] = $start . '-' . array_shift($arr); } } 
		
		else $res[] = array_shift($arr); }
	
return implode(', ', $res); }

function insert() { global $db, $applist;

	$successMsg = 'All vehicles added to the database.';

	$products = $db->products;
	
	foreach (array_keys($applist) as $part) {
	
		$res = $products->find(["ISIS SKU" => $part]); 
		
		$result = iterator_to_array($res); 
		
		if (empty($result)) $missing[] = $part;

		else $products->update(["ISIS SKU" => $part], ['$set' => ["Application List" => $applist[$part]['Applications']]]); }
		
	if (isset($missing)) {
	
		var_dump($missing); 
		
		echo "<script type='text/javascript'>$('#tabs-veh').remove()</script>"; }
	
	else echo "<script type='text/javascript'>$('#tabs-veh').replaceWith('$successMsg')</script>";

return 1; }

}

{# VARiABLES

$form = <<<EOT
				<div class='body-margin2'>Assign vehicle compatibilities.
				<center>
				<form enctype="multipart/form-data" action="{$_SERVER['PHP_SELF']}" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="200000000"/>
				<button type='button' class='generalbutton' onclick="document.getElementById('submitbutton').click()" ><div id='filefield'>Choose File</div></button>
				<div class='uploadwrap'><input id='submitbutton' name="userfile" onchange="getfilename()" type="file" multiple/></div>
				<input id='process' class='generalbutton' onclick='processtext()' type="submit" value="Submit" />
				</form>
				</center>
EOT;

$db = dbConnect(DBHOST, DBNAME);

$validVehicles = getVehicles();

$ebayveh = geteBayVehicles();

$json_ebay = array();

$applist = array();

$vehicles = array();

$allin = false;

$margin = "<div id='body-margin'>";

}

{# MAiN

echo $margin;

if (empty($_FILES)) echo $form;

else {

	getFile(); //out $applist, $vehicles
	
	check($vehicles); //out $json_ebay
	
	echo "<script type='text/javascript'>json_ebay = " . json_encode($ebayveh) . "</script>";
	
	// if ($allin) insert(); //in $applist
	
	// echo 'AIR50259'; var_dump($pn['AIR50259']);
	
}

}

?>
</div>
</body>
</html>