<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//edit page
session_start();
//require "../header.php";


require_once "../functions.php";

//require "admin-navigation.php";

if (isset($_GET['action']) && $_GET['action']=="export"):

$recordArray = json_decode(file_get_contents($ROOTURL."data/shoppingCart.json"),true);
$exportArray = array();
foreach ($recordArray as $rec) $exportArray[] = $rec;

$digitalcollection = $_GET['digital-collection'];
$digispec = $_GET['digitization-spec'];
$contributing_inst = $_GET['contributing-institution'];
$website = $_GET['website'];


export_for_CDM($exportArray,$digitalcollection,$digispec,$contributing_inst,$website);
?>

<?php 
endif;

//require "../footer.php";

//require "layout/scripts.php";

?>