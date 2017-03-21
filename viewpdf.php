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
    $imageString = $imageString." ".$image;
}

$output;
$execString = "convert ".$imageString." tmppdf.pdf";
//print $execString;
//die();
exec($execString,$output);

$file = file_get_contents($fileContents);

header("Content-Type: application/pdf");

print $fileContents;






function printError(){
    print "error?";
}