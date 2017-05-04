<?php
//names list page

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";


require_once "../functions.php";

require_once "../db-config.php";

$statement = $mysqli->prepare("SELECT name,uri FROM names ORDER BY name");
$statement->execute();
$statement->store_result();
$statement->bind_result($name, $uri);



?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-primary">Subject headings list</h1>
        <?php
while ($statement->fetch()):
?>
<p><?php print $name;?></p>
<?php endwhile; ?>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>