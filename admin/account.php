<?php
//account page
session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <h1>Account info goes here </h1>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>