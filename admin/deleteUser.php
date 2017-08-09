<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//delete page
session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";


if (!isSuper()) die();
$statement = $mysqli->prepare("DELETE FROM users WHERE id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <h1 class="text-danger">User <?php print $_GET['id']; ?> deleted</h1>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>