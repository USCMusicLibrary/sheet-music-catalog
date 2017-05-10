<?php
//edit page
session_start();
require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";


$statement = $mysqli->prepare("SELECT id,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id FROM records WHERE id=? LIMIT 1");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($id, $title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer);
$statement->fetch();

$statement2 = $mysqli->prepare("INSERT INTO records (title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id,status) VALUES (?,?,?,?,?,?,?,?,?,'pending')");
$statement2->bind_param("sssssssss",$title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer);
$statement2->execute();
$statement2->store_result();

 //var_dump($displayArray);
?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-success">Record duplicated</h1>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>