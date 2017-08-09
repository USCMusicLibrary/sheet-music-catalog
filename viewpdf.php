<?php 
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
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
$counter = 1;
foreach ($images as $image){
    $newPath = getcwd()."/tmppdf/img/".$counter++.".jpg";
    //print $newPath;
    copy($image,$newPath);
}

//print $imageString;
//die();
$output;
//$execString="cd 2>&1";
$execString = getcwd()."/tmppdf/pdf.sh";

print $execString;

//die();
shell_exec($execString);
//print_r( $output);
//print getcwd();
//die();
//$file = file_get_contents($fileContents);

header("Content-Type: application/pdf");

readfile("tmppdf/tmppdf.pdf");






function printError(){
    print "error?";
}