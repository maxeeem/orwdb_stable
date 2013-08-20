<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ORWDB - Off Road Warehouse Database</title>
<head>

<?php $p = ""; require($p . "styling/header-script.php"); ?>

</head>

<body>

<?php require("styling/header.html"); ?>

<div id="body-margin">

<div id="index">
<h2>Welcome to ORW Database</h2>

<p><i>ORWDB is a collection of scripts that allows you to build and maintain a reusable information bank.<br />
That information can be output to any channel like eBay, Amazon or an eCommerce website with just a few clicks.</i></p>
</div>

<p><small>In addition to help topics below, every page features a help icon <img src="styling/images/help-icon-sm.png"> in the upper right corner.<br />
You can use it to get information about a particular part of the system.</small></p>

<div id="accordion">

<h3>Upload File &emsp;<small>File&rarr;Upload</small></h3>
<div id="upload">
<em>Add new products to the data bank using a prepared spreadsheet.</em><br />
<p>The upload process is broken up into a number of steps to simplify the task of adding new products to the ORW Database.
<ol><li><p>Choose a file to upload. It needs to be prepared in the ORWDB format and saved as a CSV file with UTF-8 encoding.<br />
<small>The system automatically saves your state so you can interrupt your import at any point and resume it later by simply selecting File&rarr;Upload again.<br />
However, if you make changes to the source file, you will need to use the <img src="styling/images/reload-icon-sm.png"> icon in the upper right corner to force the system to start over.</small></p>
<li><p>After you submit the file, the system checks to see if all of the categories in that file alredy exist in the ORW Database.<br />
If some of those categories are missing, it will ask you to verify the information and add new categories to the database.</p>
<li><p>Next, the system checks that all the filters in the uploaded file already exist in the database.<br />
If some of those filters are missing, it will ask you to verify the information and add new filters to the database.</p>
<li><p>After all the categories and filters have been verified and added to the database, the spreadsheet is processed and an error report is generated.<br />
Errors are divided into a number of types, each in a separate tab on the page. Please review and fix all the issues before proceeding to the next step.</p>
<li><p>Final step before new products are added to the ORW Database. Part numbers that do not exist in ISIS are displayed for your reference.<br />
<small>This information can later be accessed through File&rarr;Review</small></p></ol>
</div>

<h3>Review Manufacturer &emsp;<small>File&rarr;Review</small></h3>
<div id="reviewBrand">
<em>Get a quick overview of any Brand currently in the ORWDB.</em><br />
<p>Shows part numbers that do not exist in ISIS as well as a breakdown of all part numbers that have some information missing like Price, Weight or Dimensions.</p>
<p>Information is also available for download as a spreadsheet.</p>
</div>

<h3>Update Manufacturer &emsp;<small>File&rarr;Update</small></h3>
<div id="updateBrand">
<strong>*** WORK IN PROGRESS ***</strong><br />
<em>Use this script to update products for manufacturers already in the ORW Database.</em><br />
<p>At the present time, this script takes a single-column CSV file as input and produces a report, displaying New and potentially Discontinued part numbers.</p>
<p>The CSV file should contain an updated parts list for a given manufacturer, including the ISIS linecode, arranged in a single column with Manufacturer name<br />
in the first cell. Manufacturer name must <strong>exactly</strong> match the one already in the ORWDB.</p>
</div>

<h3>Export Manufacturer &emsp;<small>File&rarr;Export</small></h3>
<div id="export">
<em>Export products for use with Website, eBay or Amazon.</em><br />
<p>After you have selected the Manufacturer and Channel, you are presented with a range of channel-specific options.</p>
<p>Review the options carefully and once you click Export, a number of files will be generated, depending on the selected channel.</p>
</div>

<h3>Assign Categories &emsp;<small>Assign&rarr;Categories</small></h3>
<div id="assignCategories">
<em>Provides an interface to map ORWDB categories to corresponding Amazon and eBay categories.</em><br />
<ul><li>Select a category from the menu on the right, then enter corresponding category numbers (<strong>not</strong> category names) for Amazon and eBay
<li>You do not have to fill out all of the fields if products in that category are not going to be sold through a particular channel
<li>Category information is verified and used during export (<small>File&rarr;Export</small>) and can be modified at any time</ul></p>
</div>

</div>

</div>
</body>
</html>