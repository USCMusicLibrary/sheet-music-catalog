<?php

require "header.php";?>
<!--<pre>-->
<?php


require "functions.php";

global $solrUrl;
global $solrResultsHighlightTag;

if (!isset($_GET['id'])){
	header ('Location: index');
	die();
}

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
      <div class="col-xs-12 col-md-8 col-md-offset-2">
        <h2><?php print $result['title'];?></h2>
      </div>
      <div class="col-xs-12 col-md-8 col-md-offset-2">
				<table class="item-display-table">

      <?php foreach ($solrFieldNames as $field => $v):
			if ($field=="url"||$field=="id"||$field=="years") continue;
	    if (!array_key_exists($field,$result)) continue;

			//check if blank
			if (is_array($result[$field])){
				$emptyVar = array_filter($result[$field]);//gotta love php 5.3
				if (empty($emptyVar)) continue;
			}
			else if (trim($result[$field])==""){
				continue;
			}
	  ?>
      <tr>
        <th><?php
		  print $solrFieldNames[$field]['field_title'];
		  if (is_array($result[$field]) && count($result[$field])>1){
		    print 's';
		  }
		  ?>:</th><td>
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
		  if ($field=="has_image" && $result[$field]=="Print only"){
				print "&nbsp;&nbsp;<a href=\"http://library.sc.edu/p/Music/About\">Contact us for access</a>";
			}
			?>
			
		</td>
			</tr>
      <?php endforeach;?>
		</table>
      </div>

		<div class="col-xs-12 col-md-8 col-md-offset-2">
			<hr>
			<?php
			  $imageList = getImagesForId($_GET['id']);
				//print_r($imageList);
				$counter=0;
				foreach ($imageList as $image):?>
				  <div class="col-xs-6 col-md-3 thumbnail-div">
						<a class="btn btn-default" data-toggle="modal" data-target="#carouselModal" onclick="javascript:carouselIndex=<?php print $counter++;?>;"><img class="img-responsive" src="<?php print $image; ?>"></a>
					</div>
					<?php if (($counter)%4 == 0):?>
						<div class="clearfix"></div>
					<?php endif;?>
				<?php
				endforeach;
			?>

	  </div>
  </div>
</div> <!-- container-fluid -->

<!-- modal for carousel-->
<div id="carouselModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Score view</h4>
      </div>
      <div class="modal-body">
				<div id="musicCarousel" class="carousel slide" data-interval="false">
  <!-- Indicators -->
  <ol class="carousel-indicators">
	<?php
	$counter=0;
	foreach ($imageList as $image):?>
    <li data-target="#musicCarousel" data-slide-to="<?php print $counter;?>"<?php if ($counter++==0) print ' class="active"';?>></li>
	<?php endforeach; ?>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
		<?php $counter=0;
		foreach ($imageList as $image):?>
    <div class="item<?php if ($counter++==0) print ' active';?>">
      <img src="<?php print $image;?>" alt="">
    </div>
		<?php endforeach; ?>
  </div>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#musicCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#musicCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<?php

require "footer.php";

//require "layout/scripts.php";

?>
