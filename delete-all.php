<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
require "header.php";

require_once 'functions.php';

if (!$_SESSION['logged-in']){
    header("Location: admin/login");
    die();
}

if (!isLoggedIn() || !isSuper() ){
  print "<h1 class=\"text-danger\">Unauthorized</h1>";
  die();
}

?>
<div class="container-fluid">
<pre>
<?php 
delete_all();
?>
</pre>
</div>
<?php 

?>

<?php 

require "footer.php";

?>