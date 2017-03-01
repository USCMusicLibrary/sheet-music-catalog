<?php 
require_once "../db-config.php";

$statement = $mysqli->prepare("SELECT subject_heading,uri FROM subject_headings ORDER BY subject_heading");
$statement->execute();
$statement->store_result();
$statement->bind_result($name, $uri);
?>
<datalist id="headings-list">
<?php
while ($statement->fetch()):
?>
<option value="<?php print $name;?>"><?php print $name;?></option>
<?php endwhile; ?>
</datalist>