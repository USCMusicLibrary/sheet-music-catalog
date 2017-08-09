<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//add record page

//session_start();
if (!isset($_GET['id'])&& !isset($_POST['id'])){
    header("Location: login");
    die();
}

$id = $_GET['id'];
require "../header.php";


require_once "../functions.php";


require_once "adminFunctions.php";

if (!isset($_POST['p'])):
  $statement = $mysqli->prepare("SELECT id FROM users WHERE password_uid=? LIMIT 1");
  $statement->bind_param('i',$id);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($userID);
  if (!$statement->fetch()){
    header("Location: login");
    die();
  }?>
  <form action="reset" method="POST">
    <strong>New password: </strong><input type="password" class="form-control" name="p" id="p">
    <strong>Confirm password: </strong><input type="password" class="form-control" name="p2" id="p2">
    <input type="submit" class="form-control" value="Submit">
    <input type="hidden" value="<?php print $userID?>" name="id">
  </form>
<?php
else:
//var_dump($_POST);
  if (!isset($_POST['p']) || !isset($_POST['p2'])){
    //print "notset";
    header("Location: login");
    die();
  }
  if ($_POST['p'] !== $_POST['p2']){
    //print "notsame";
    header("Location: login");
    die();
  }
  $newPassword = password_hash($_POST['p'],PASSWORD_DEFAULT);
  $id = $_POST['id'];
  //var_dump($newPassword);

  $statement = $mysqli->prepare("UPDATE users SET password_hash=? WHERE id=? LIMIT 1");
  $statement->bind_param('ss',$newPassword,$id);
  $statement->execute();
  $statement->store_result();
?>
  <h1 class="text-success">Password updated successfully</h1>
<?php
endif;
?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">

      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>