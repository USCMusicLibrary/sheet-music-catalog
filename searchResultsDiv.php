<?php 

/**
 * @file searchResultsDiv.php
* returns a formatted <div></div> element for display of search results
*/

require_once('functions.php');


if (!isset($_GET['q'])): //if there is no query parameter
?>
<div>Empty div</div><!-- return empty div-->
<?php endif; //!isset($_GET['q']?>
<div>
<?php
$query = array('q' => $_GET['q']);

$results = getResultsFromSolr($query);

$numDocs = $results['numFound'];

print '<h3>Found '.$numDocs.' objects</h3>';

if ($numDocs != 0):
  foreach ($results['docs'] as $doc) :
    ?>	
    <div class="search-result">
    <?php foreach ($doc as $key => $value):?>
      <p><strong class="bold"><?php print $key;?>: </strong> <?php print $value;?></p>
    <?php endforeach;?>
    </div>
  <?php
  endforeach; //foreach $results['docs'] as $doc
endif;
?>
</div>