<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
session_start();
require "header.php";


require_once "functions.php";

?>
<div class="container-fluid">
	<div class="row">
	</div>
<?php



$queryArray = array(
	array('all','AND','*')
);
$counter=0;

require 'browse-by.php';


?></div> <!-- container-fluid -->
<?php 

require "footer.php";

//require "layout/scripts.php";

?>