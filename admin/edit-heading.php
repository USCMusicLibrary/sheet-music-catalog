<?php
//edit heading

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";


require_once "../functions.php";

require_once "../db-config.php";


$headingID = $_POST['id'];
$headingTable = ($_POST['type']) ? "names": "subject_headings";
$columnName = $_POST['type'];

$statement = $mysqli->prepare("SELECT $columnName,uri FROM $headingTable WHERE id=? ? LIMIT 1");
$statement->bind_param("i",$headingID);
$statement->execute();
$statement->store_result();
$statement->bind_result($heading, $uri);

//todo: add error checking
$statement->fetch();



?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-primary">Names list</h1>
        <table>
        <tr><th>Heading</th><th>URI</th><th></th></tr>
<tr>
<td><span class="text-primary"><?php print $heading;?></span>
            <input class="clickedit" type="text" /></td>
<td><span class="text-primary"><?php print $uri;?></span>
            <input class="clickedit" type="text" /></td>
</tr>
</table>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>