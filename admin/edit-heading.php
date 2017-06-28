<?php
//edit heading

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";


require_once "../functions.php";

require_once "../db-config.php";

if (isset($_POST['id']))://if is update

$headingID = $_POST['id'];
$headingTable = ($_POST['type']=="name") ? "names": "subject_headings";
$columnName = $_POST['type'];
$query = "UPDATE $headingTable SET $columnName=?,uri=? WHERE id=?";
//print $query;
$statement = $mysqli->prepare($query);
$statement->bind_param("ssi",$_POST['heading-value'],$_POST['uri'],$headingID);
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

$query = "SELECT $columnName,uri FROM $headingTable WHERE id=? LIMIT 1";
//print $query;
$statement = $mysqli->prepare($query);
$statement->bind_param("i",$headingID);
$statement->execute();
$statement->store_result();
$statement->bind_result($heading, $uri);

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
        <input type="hidden" value="<?php print $_GET['id'];?>" name="id">
        <input type="hidden" value="<?php print $_GET['type'];?>" name="type">
        <input class="form-control btn-success btn" type="submit" value="Save">
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 
endif;//else

require "../footer.php";

//require "layout/scripts.php";

?>