<?php
//edit page
session_start();
require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";


$statement = $mysqli->prepare("UPDATE records SET status='approved' WHERE id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-12">
        <h1 class="text-danger">Record <?php print $_GET['id']; ?> approved</h1>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>