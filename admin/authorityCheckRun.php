<?php
//authority check run
session_start();
require_once "../functions.php";

if (!isLoggedIn()){
    header("Location: login");
    die();
}

require "../header.php";

require "admin-navigation.php";

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
    <?php
$execString = getcwd()."/runAuthorityCheck.sh";

print $execString; 

//die();
shell_exec($execString);

$output1 = file_get_contents("output.txt");
$output2 = file_get_contents("output2.txt");
print '<pre>'.$output1."\n".$output2.'</pre>';
    ?>
    </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>