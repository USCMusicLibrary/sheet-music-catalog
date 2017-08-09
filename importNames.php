<?php 
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
require_once "functions.php";



//function importExcelTabFile(){
	global $mysqli;
	$file = NULL;
	ini_set("auto_detect_line_endings", true);
	try {
	$file = new SplFileObject("smdb_names_9_29_2016.tsv");
	}
	catch (Exception $error){
		echo '<div class="jumbotron"><h1 class="text-danger">Unable to open uploaded file. Please try again.</h1><p>'.$error->getMessage().'</p></div>';
		return;
	}

	$counter=0;

	$counter2=0;

    $namesString = "<datalist id=\"names-list\">\n";

	while ($line= $file->fgets()) {

		if ($counter++ == 0) {
			continue; //discard first line because it only contains headers
		}
		//echo $line.'<br>';
		//$line= utf8_encode($line);
		//echo $line.'<br>';
    	$line2 =  preg_replace('/\\t"/',"\t",$line);
		//echo $line2.'<br>';
		$line3 =  preg_replace('/"\\t/',"\t",$line2);
		//echo $line3.'<br>';
		$line4 =  preg_replace('/""/','"',$line3);
		//echo $line4.'<br>';

		$fields = explode("\t",$line4);
        $namesString = $namesString.'<option value="'.$fields[0].'">'. htmlspecialchars( $fields[0]).'</option>'."\n";
    }

    $namesString = $namesString."</datalist>";

file_put_contents("data/namesList.php",$namesString);

//}