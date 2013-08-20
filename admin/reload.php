<?php 

if (file_exists("../files/file.csv")) unlink("../files/file.csv");

if (!isset($_GET['standalone'])) echo "<script type='text/javascript'>window.history.back()</script>";

?>