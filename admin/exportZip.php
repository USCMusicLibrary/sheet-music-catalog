<?php
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