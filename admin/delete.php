<?php
//delete page
session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";


require_once "../functions.php";

//TODO: check user permissions before deleting record
deleteRecord($_GET['id']);

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <h1 class="text-danger">Record <?php print $_GET['id']; ?> deleted</h1>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>