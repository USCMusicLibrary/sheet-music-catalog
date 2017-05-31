<?php 

require_once "functions.php";

$id;
if (!isset($_GET['id'])){
    //printError();
    //die();
}
else {
    $id=$_GET['id'];
}
//print "isset";
if (!idHasImage($id)){
    //printError();
    //die();
}

$images = getImagesForId($id);

$imageString = "";
foreach ($images as $image){
    $imageString = $imageString." ".getcwd()."/".$image;
}

//die();
$output;
//$execString="cd 2>&1";
$execString = "convert ".$imageString." ".getcwd()."/tmppdf/tmppdf.pdf";

$execString = "#/bin/bash\n".$execString."\necho \"DONE!\"";
file_put_contents("tmppdf/pdf.sh", $execString);

exec("tmppdf/pdf.sh",$output);
//print_r( $output);
//print getcwd();
//die();
//$file = file_get_contents($fileContents);

header("Content-Type: application/pdf");

readfile("tmppdf/tmppdf.pdf");






function printError(){
    print "error?";
}