<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//image upload

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";


require_once "../functions.php";

require_once "../db-config.php";

$statement = $mysqli->prepare("SELECT id, name,uri,local_note FROM names ORDER BY name");
$statement->execute();
$statement->store_result();
$statement->bind_result($id,$name, $uri,$local_note);



?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-primary">Names list</h1>
        <table>
        <tr><th>Name</th><th>URI</th><th>Local note</th><th></th><th></th></tr>
        <?php
while ($statement->fetch()):
?>
<tr>
<td><?php print $name;?></td>
<td><?php print $uri;?></td>
<td><?php print $local_note;?></td>
<?php 
if (isSuper()):?>
<td><a href="edit-heading?id=<?php print $id;?>&type=name" class="btn btn-success btn-sm">Edit</a></td>
<td><a href="edit-heading?id=<?php print $id;?>&type=name&action=delete" class="btn btn-danger btn-sm">Delete</a></td>
<?php else:
  print "<td></td><td></td>";
endif;
endwhile; ?>
</table>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>