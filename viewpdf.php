<?php 

require "functions.php";

$id;
if (!isset($_GET['id'])){
    printError();
    die();
}
else {
    $id=$_GET['id'];
}
//print "isset";
if (!idHasImage($id)){
    printError();
    die();
}

$images = getImagesForId($id);

$imageString = "";
foreach ($images as $image){
    $imageString = $imageString." ".getcwd()."\\".$image;
}

$output;
//$execString="cd 2>&1";
$execString = "\"C:\Program Files\ImageMagick-7.0.5-Q16\magick.exe\" ".$imageString." tmppdf.pdf 2>&1";

exec($execString,$output);
print_r( $output);
//print getcwd();
die();
//$file = file_get_contents($fileContents);

header("Content-Type: application/pdf");

readfile("tmppdf.pdf");






function printError(){
    print "error?";
}