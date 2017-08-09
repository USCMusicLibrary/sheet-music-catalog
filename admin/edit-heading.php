<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//edit heading

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";


require_once "../functions.php";

require_once "../db-config.php";

if (isset($_GET['action']) && $_GET['action']=="delete"){
  //check that no record is currently using heading
  $headingID = $_GET['id'];
  $headingTable = ($_GET['type']=="name") ? "contributors": "has_subject";
  $columnName = ($_GET['type']=="name") ? "contributor_id": "subject_id";
  $query = "SELECT $columnName FROM $headingTable WHERE $columnName=?";
  $statement = $mysqli->prepare($query);
  $statement->bind_param("i",$headingID);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($heading_id);
  if ($statement->fetch()){//there are still records associated with this heading
    ?>
    <div class="container-fluid">
      <div class="row">
          <div class="col-xs-8 col-xs-offset-2">
            <h1 class="text-danger">Unable to delete heading. Please make sure that no records are currently using this heading. -- Heading id:<?php print $headingID;?></h1>
          </div>
      </div>
    </div> <!-- container-fluid -->
    <?php  
  }
  else{//no records associated, safe to delete
    $headingTable = ($_GET['type']=="name") ? "names": "subject_headings";
    $query = "DELETE FROM $headingTable WHERE id=?";
    $statement = $mysqli->prepare($query);
    $statement->bind_param("i",$headingID);
    $statement->execute();
    $statement->store_result();
  }
}
else if (isset($_POST['id']))://if is update
  $headingID = $_POST['id'];
  $headingTable = ($_POST['type']=="name") ? "names": "subject_headings";
  $columnName = $_POST['type'];
  $query = "UPDATE $headingTable SET $columnName=?,uri=?,local_note=? WHERE id=?";
  //print $query;
  $statement = $mysqli->prepare($query);
  $statement->bind_param("sssi",$_POST['heading-value'],$_POST['uri'],$_POST['localNote'],$headingID);
  $statement->execute();
  $statement->store_result();

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-primary">Heading updated</h1>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php
else:

$headingID = $_GET['id'];
$headingTable = ($_GET['type']=="name") ? "names": "subject_headings";
$columnName = $_GET['type'];

$query = "SELECT $columnName,local_note,uri FROM $headingTable WHERE id=? LIMIT 1";
//print $query;
$statement = $mysqli->prepare($query);
$statement->bind_param("i",$headingID);
$statement->execute();
$statement->store_result();
$statement->bind_result($heading,$localNote, $uri);

//todo: add error checking
$statement->fetch();



?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-primary"></h1>
        <form action="edit-heading" method="POST">
        <label for="heading-value">Heading: </label><input type="text" class="form-control" name="heading-value" id="heading-value" value="<?php print $heading;?>"><br>
        <label for="uri">URI: </label><input type="text" class="form-control" name="uri" id="uri" value="<?php print $uri;?>"><br>
        <label for="localNote">Local note: </label><input type="text" class="form-control" name="localNote" id="localNote" value="<?php print $localNote;?>"><br>
        <input type="hidden" value="<?php print $_GET['id'];?>" name="id">
        <input type="hidden" value="<?php print $_GET['type'];?>" name="type">
        <input type="hidden" value="edit" name="action">
        <input class="form-control btn-success btn" type="submit" value="Save">
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 
endif;//else

require "../footer.php";

//require "layout/scripts.php";

?>