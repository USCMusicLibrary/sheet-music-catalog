<?php

require "header.php";


require "functions.php";

?>
<div class="container-fluid">
	<div class="row">
	</div>
<?php

if (isset($_GET['form_submitted'])):
//form submitted, display advanced search page with populated search fields
//and also display search results


//check that $_GET query is valid
if (!isset($_GET['f']) || !isset($_GET['q']) || !isset($_GET['op']) || sizeof($_GET['f']) != sizeof($_GET['q']) || sizeof($_GET['f']) != sizeof($_GET['op'])):
	$qArray = NULL;
	require 'advanced-search-box.php';

print '<div class="row text-center"><h1 class="text-danger">Invalid search query</h1></div>';
else:
/* convert search results into query array
 $queryArray = array($field,$boolean,$query)
*/

$queryArray = array();
$counter=0;

foreach ($_GET['q'] as $query){
	$queryArray[] = array($_GET['f'][$counter],$_GET['op'][$counter],$query);
	$counter++;
}



require 'advanced-search-box.php';
//debug
/*print '<pre>';
print_r($queryArray);
print '</pre>';*/
//end debug



//debug
//var_dump($searchResponse);
?>

<?php 
require 'search-results.php';

endif;//check $_GET query

else: //form not submitted - display advanced search page only
$queryArray = NULL;
require 'advanced-search-box.php';

endif; //if (isset($_GET['form_submitted'])):

?></div> <!-- container-fluid -->
<?php 

require "footer.php";

//require "layout/scripts.php";

?>