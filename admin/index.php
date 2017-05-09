<?php
//index for admin part
session_start();
require_once "../functions.php";

if (!isLoggedIn()){
    header("Location: login");
    die();
}

require "../header.php";

require "admin-navigation.php";

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <a class="btn btn-lg btn-default" href="account">Account</a>
      <a class="btn btn-lg btn-default">Add records</a>
      <a class="btn btn-lg btn-default">Review records</a>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>