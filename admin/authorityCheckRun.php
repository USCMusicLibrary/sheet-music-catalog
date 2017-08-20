<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//authority check run
session_start();
require_once "../functions.php";

if (!isLoggedIn()){
    header("Location: login");
    die();
}

require "../header.php";
require "admin-navigation.php";

if (!isLoggedIn() || !isSuper() ){
    print "<h1 class=\"text-danger\">Unauthorized</h1>";
    die();
  }

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
    <?php
$execString = getcwd()."/runAuthorityCheck.sh";

print $execString; 

//die();
shell_exec($execString);

$output1 = file_get_contents("output.txt");
$output2 = file_get_contents("output2.txt");
print '<pre>'.$output1."\n".$output2.'</pre>';
    ?>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>