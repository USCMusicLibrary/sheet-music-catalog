<?php
//account page
session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}

require "../header.php";


require_once "../functions.php";

require "admin-navigation.php";

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
<br>
      <?php
        $statement = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
        $statement->bind_param('s',$_SESSION['username']);
        $statement->execute();
        $metaResults = $statement->result_metadata();
    $fields = $metaResults->fetch_fields();
    $statementParams='';
     //build the bind_results statement dynamically so I can get the results in an array
     //because I'm lazy'
     foreach($fields as $field){
         if(empty($statementParams)){
             $statementParams.="\$column['".$field->name."']";
         }else{
             $statementParams.=", \$column['".$field->name."']";
         }
    }
    $statment="\$statement->bind_result($statementParams);";
    eval($statment);
    while($statement->fetch()){
      foreach ($column as $key=>$value):
        if ($key=='password_hash') continue;
        ?>
        <p><strong><?php print $key;?>: </strong><?php print $value;?></p>
      <?php endforeach;
    }
      ?>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>