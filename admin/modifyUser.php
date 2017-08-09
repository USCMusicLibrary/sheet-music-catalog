<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//mod user
session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";


if (!isSuper()) die();
$statement = $mysqli->prepare("UPDATE users SET user_role=? WHERE id=?");
$statement->bind_param("si",$_GET['to'],$_GET['id']);
$statement->execute();
$statement->store_result();

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <h1 class="text-danger">User <?php print $_GET['id']; ?> updated</h1>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>