<?php
//edit page
session_start();
require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";

$recordArray = json_decode(file_get_contents($ROOTURL."data/shoppingCart.json"),true);

//var_dump($recordArray);
$exportArray = array();
foreach ($recordArray as $rec) $exportArray[] = $rec;

export_for_CDM($exportArray);
?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h1 class="text-success">Records exported</h1>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>