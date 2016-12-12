<?php
//index for admin part
require "../header.php";


require "../functions.php";

$path = $ROOTURL.'data/db2.json';
$dataJson = file_get_contents($path);
//if ($dataJson===FALSE) echo"error";
$data = json_decode($dataJson,true);
//print json_last_error();
//var_dump($data);

$item = $data[$_GET['id']];
?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <?php foreach ($item as $key => $val):?>
        <p>
          <strong><?php print $solrFieldNames[$key]['field_title'];?>: </strong>
          <?php 
            if (is_array($val)){
                foreach ($val as $value){
                    if (trim($value)=="") continue;
                    ?>'<br><span class="text-primary"><?php print $value;?></span>
            <input class="clickedit" type="text" />
                <?php
                }
            }
            else{?>
            <span class="text-primary"><?php print $val;?></span>
            <input class="clickedit" type="text" />
            <?php }
          ?>
        </p>
      <?php endforeach;?>
      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>