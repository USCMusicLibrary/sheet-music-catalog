<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
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
      <a class="btn btn-lg btn-default" href="add">Add records</a>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>