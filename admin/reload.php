<!-- @Author : Max Poole

@Purpose : See MAiN section, near the bottom of the script 

@Credits : Jeffrey Tu (CSS & jQuery) -->

<?php 

if (file_exists("../files/file.csv")) unlink("../files/file.csv");

if (!isset($_GET['standalone'])) echo "<script type='text/javascript'>window.history.back()</script>";

?>