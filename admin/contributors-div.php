<?php
//var_dump($_POST['data']);
$contributors = $_POST['data'];

if (!$contributors) {
  print "<div></div>";
  exit();
}?>
<div class="col-xs-10" id="contributors-list">
<?php

foreach ($contributors as $contributor):?>
  <div class="col-xs-8 col-xs-offset-2">
    <span><?php print $contributor[0];?>: <b><?php print $contributor[1];?></b></span><button class="btn btn-default btn-sm">x</button>
  </div>
<?php endforeach;?>
  
</div>