<datalist id="headings-list">
<?php
//while ($statement->fetch()):
?>
<option value="some subject heading">some subject heading</option>
<?php //endwhile; ?>
</datalist>

<?php 

//exit();
/*require_once "../db-config.php";

$statement = $mysqli->prepare("SELECT name,uri FROM subject_headings ORDER BY name");
$statement->execute();
$statement->store_result();
$statement->bind_result($name, $uri);
?>
<datalist id="names-list">
<?php
while ($statement->fetch()):
?>
<option value="<?php print $name;?>"><?php print $name;?></option>
<?php endwhile; ?>
</datalist>*/