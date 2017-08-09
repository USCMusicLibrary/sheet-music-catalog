<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
/**
 */
//TODO: add some error checking

$title = "Users";
session_start();

if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../db-config.php";
require_once "../functions.php";
require "../header.php";


require "admin-navigation.php";

if (!isLoggedIn() || !isSuper() ){
  print "<h1 class=\"text-danger\">Unauthorized</h1>";
  die();
}
?>

<div class="container">
  <div class="row page-header">

  </div>
  <div class="row">
<!-- debug -->
  <div>
    
  </div>
      <div class="col-xs-6 center-block">
        <table class="table table-striped table-hover">
        <tr><th>User</th><th>Email</th><th>Role</th><th>Change status</th><th>Delete user</th></tr>
        <?php
          global $mysqli;
          $statement = $mysqli->prepare("SELECT id,username,email,user_role FROM users");
          $statement->execute();
          $statement->store_result();
          $statement->bind_result($userid,$user,$email,$role);

          while($statement->fetch()):?>
            <tr><td><?php print $user;?></td><td><?php print $email;?></td><td><?php print $role;?></td>
            <td><a href="modifyUser?id=<?php print $userid;?>&to=<?php  print ($role=='super')?'contributor':'super';?>" class="btn btn-primary">Change status</a></td>
            <td><a href="deleteUser?id=<?php print $userid;?>" class="btn btn-danger">Delete user</a></td>
            </tr>
          <?php endwhile;
        ?>
      </div>
  </div>
</div>

<?php require "../footer.php"; ?>
