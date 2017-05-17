<?php
//subject headings list page

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";


require_once "../functions.php";

require_once "../db-config.php";

$statement = $mysqli->prepare("SELECT subject_heading,uri FROM subject_headings ORDER BY subject_heading");
$statement->execute();
$statement->store_result();
$statement->bind_result($name, $uri);



?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-primary">Subject headings list</h1>
        <table>
        <tr><th>Heading</th><th>URI</th><th></th></tr>
        <?php
while ($statement->fetch()):
?>
<tr>
<td><?php print $name;?></td>
<td><?php print $uri;?></td>
<?php 
if (isSuper()):?>
<td><a href="#" class="btn btn-danger">Edit</a></td>
<?php else:
  print "<td></td>";
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