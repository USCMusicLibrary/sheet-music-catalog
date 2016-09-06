<?php

require "header.php";?>
<!--<pre>-->
<?php


require "functions.php";

global $solrUrl;
global $solrResultsHighlightTag;


$queryString = 'id:'.$_GET['id'];
	$queryString = $solrUrl
		.'select?q='.$queryString.'&start=0&rows=1&wt=json';

	$ch = curl_init();
	curl_setopt_array($ch, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $queryString,
	));
	
	$jsonResponse = curl_exec($ch);
	if (curl_error($ch)){
		throw new Exception('Unable to connect to search engine.');
	}
	//$jsonResponse = file_get_contents($queryString);
	
	//print $queryString;

	if ($jsonResponse === false) return false;

	$responseArray = json_decode($jsonResponse,true);

	$searchResults = $responseArray/*["response"]*/;
	$result = $searchResults['response']['docs'][0];
	//print_r( $result);


?>
<!--</pre>-->
<div class="container-fluid">
	<div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h2><?php print $result['title'];?></h2>
      </div>
      <div class="col-xs-8 col-xs-offset-2">
      <?php foreach ($solrFieldNames as $field => $v):
	    if (!array_key_exists($field,$result)) continue;
	  ?>
      
        <p>
          <strong><?php 
		  print $solrFieldNames[$field]['field_title'];
		  if (is_array($result[$field]) && count($result[$field])>1){
		    print '(s)';
		  }
		  ?> :</strong>
		  <?php
		  $value = $result[$field];
		  if (is_array($value)){
		    foreach ($value as $val){
		  	  print $val.'<br>';
		    }
		  }
		  else{
		    print $value;
		  }
		  ?>
        </p>
      <?php endforeach;?>
      </div>
	</div>
</div> <!-- container-fluid -->
<?php 

require "footer.php";

//require "layout/scripts.php";

?>