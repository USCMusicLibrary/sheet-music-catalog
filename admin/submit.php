<?php
//index for admin part
require "../header.php";


require "../functions.php";

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
      <pre>
      <?php //print_r ($_POST);?>
      </pre>
      <div>
       <h4>Editable labels (below)</h4>

<label class="pull-left">Editable labels test</label>
<input class="clickedit" type="text" />
<div class="clearfix"></div>
<label class="pull-left">Some other thing</label>
<input class="clickedit" type="text" />
<div class="clearfix"></div>


      </div>
      <?php foreach ($_POST as $key => $val):?>
        <p>
          <strong><?php print $key;?>: </strong>
          <?php 
            if (is_array($val)){
                foreach ($val as $value){
                    if (trim($value)=="") continue;
                    print '<br>'.$value;
                }
            }
            else print $val;
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