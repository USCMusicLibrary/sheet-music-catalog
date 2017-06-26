<?php
//add record page

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";

require "admin-navigation.php";

require_once "../functions.php";

$statement = $mysqli->prepare("SELECT id,title,call_number,series,larger_work,collection_source,donor,scanning_technician,media_cataloguer_id,reviewer_id, admin_notes, date_created FROM records WHERE id=? LIMIT 1");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($id, $title, $call_number, $series, $larger_work, 	$collection_source, $donor, $scanning_technician, $media_cataloguer, $reviewer, $admin_notes,$date_created);
$statement->fetch();

$statement = $mysqli->prepare("SELECT contributor_id,role_id FROM contributors WHERE record_id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($contributorId, $roleId);

$contributors = array();
while ($statement->fetch()){
  $statement2 = $mysqli->prepare("SELECT name FROM names WHERE id=? LIMIT 1");
  $statement2->bind_param("i",$contributorId);
  $statement2->execute();
  $statement2->store_result();
  $statement2->bind_result($cName);
  if ($statement2->fetch()){
    $contributors[] = array(array_search($roleId,$contribtypes),$cName);
  }
}

$statement = $mysqli->prepare("SELECT subject_id FROM has_subject WHERE record_id=?");
$statement->bind_param("i",$_GET['id']);
$statement->execute();
$statement->store_result();
$statement->bind_result($subjectId);

$headings = array();
while ($statement->fetch()){
  $statement2 = $mysqli->prepare("SELECT subject_heading FROM subject_headings WHERE id=? LIMIT 1");
  $statement2->bind_param("i",$subjectId);
  $statement2->execute();
  $statement2->store_result();
  $statement2->bind_result($cName);
  if ($statement2->fetch()){
    $headings[] = $cName;
  }
}


//var_dump($admin_notes);

$fields = array(
  'alternative_title'=> ['alternative_titles','alternative_title'],
  'notes'=>['notes','note'],
  'text_t'=>['texts','text_t'],
  'publisher_location'=>['publisher_locations','publisher_location'],
  'publisher'=>['publishers','publisher'],
  'language'=>['languages','language']
);

$displayArray = array();

foreach($fields as $field=>$values){
  //var_dump($values);
  $query = "SELECT $values[1] FROM $values[0] WHERE record_id=?";
  $statement = $mysqli->prepare($query);
  $statement->bind_param("i",$id);
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($value);
  while ($statement->fetch()){
    if (trim($value)=="") continue;
    $displayArray[$field][] = $value; 
  }
}
var_dump($displayArray);
?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h2>Submission form</h2>
        <form class="form-horizontal" action="submit" method="POST" id="recordForm" name="recordForm">
<input type="hidden" name="cataloguer_id" value="<?php print $media_cataloguer;?>">
<input type="hidden" name="id" value="0">
<input type="hidden" name="editRecord" value="">
<input type="hidden" name="date_created" value="<?php print $date_created;?>">


        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Title</label>
          </div>
          <div class="col-xs-10">
            <input type="text" value="<?php print $title;?>" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="alt-title" class="control-label">Alternative Title</label>
          </div>
            <?php
              $fieldName = 'alternative_title'; 
              if (array_key_exists($fieldName,$displayArray)):
                foreach ($displayArray[$fieldName] as $value):?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" value="<?php print $value;?>" class="form-control" name="alt-title[]" id="alt-title">
                </div>
              <?php
                endforeach;
              else:?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" class="form-control" name="alt-title[]" id="alt-title">
                </div>
              <?php
              endif;?>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-alt-title" class="btn btn-danger">Add another alternative title</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="contributor_type" class="control-label">Contributor(s)</label>
          </div>
          <div class="col-xs-10" id="contributors-list">
            <?php foreach ($contributors as $contributor):
              $type = $contributor[0];
              $name = $contributor[1];?>
              <div><span><?php print $type;?>: <b><input type="text" value="<?php print $name;?>" readonly name="<?php print $type;?>[]"></b></span><button class="btn btn-default btn-sm btn-rm-contributor">x</button></div>
            <?php endforeach;?>
          </div>

          <!-- Modal -->
            <div id="contributorModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
              <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add a contributor</h4>
                  </div>
                  <div class="modal-body">

                    <div class="col-xs-12 contributor-form-group">
                      <div class="col-xs-5">
                        <select class="form-control" name="contributor_type_insert" id="contributor_type_insert">
                          <option>Composer</option>
                          <option>Lyricist</option>
                          <option>Arranger</option>
                          <option>Editor</option>
                          <option>Illustrator</option>
                          <option>Photographer</option>
                          <option>Other</option>
                        </select>
                      </div>
                      <div class="col-xs-5">
                        <input class="form-control awesomplete"  list="names-list" name="contributor_insert" id="contributor_insert">
                        <?php require ("../data/namesList.php"); ?>
                      </div>
                      <div class="col-xs-2">
                        <button id="btn-insert-contributor" class="btn btn-danger btn-sm">Add to record</button>
                      </div>
                    </div>

                  </div>
                  <div class="modal-footer" style="margin-top:2em;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

          <div class="col-xs-10 col-xs-offset-2"></div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-contributor" class="btn btn-danger" data-toggle="modal" data-target="#contributorModal">Add a contributor</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="publisher" class="control-label">Publisher(s)</label>
          </div>
             <?php
              $fieldName = 'publisher'; 
              if (array_key_exists($fieldName,$displayArray)):
                foreach ($displayArray[$fieldName] as $value):?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" value="<?php print $value;?>" class="form-control" name="publisher[]" id="pub">
                </div>
              <?php
                endforeach;
              else:?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" class="form-control" name="publisher[]" id="pub">
                </div>
              <?php
              endif;?>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-publisher" class="btn btn-danger">Add another publisher</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="publisher_location" class="control-label">Publisher Location</label>
          </div>
            <?php
              $fieldName = 'publisher_location'; 
              if (array_key_exists($fieldName,$displayArray)):
                foreach ($displayArray[$fieldName] as $value):?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" value="<?php print $value;?>" class="form-control" name="publisher_location[]" id="pub_loc">
                </div>
              <?php
                endforeach;
              else:?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" class="form-control" name="publisher_location[]" id="pub_loc">
                </div>
              <?php
              endif;?>
            <!--<input type="text" class="form-control" name="publisher_location[]" id="pub_loc">-->
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-pub-loc" class="btn btn-danger">Add another publisher location</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="year" class="control-label">Date</label>
          </div>
          <div class="col-xs-10">
            <!--<input type="text" class="form-control" name="year" id="daterange">-->
            <label for="year_start">Start Year</label><input type="text" class="form-control" name="year_start" id="date_start" required="">
            <label for="year_end">End Year</label><input type="text" class="form-control" name="year_end" id="date_end">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="Notes" class="control-label">Notes</label>
          </div>
          
            <?php
              $fieldName = 'notes'; 
              if (array_key_exists($fieldName,$displayArray)):
                foreach ($displayArray[$fieldName] as $value):?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" value="<?php print $value;?>" class="form-control" name="note[]" id="note">
                </div>
              <?php
                endforeach;
              else:?>
              <div class="col-xs-10 col-xs-offset-2">
                <input type="text" class="form-control" name="note[]" id="note">
              </div>
              <?php
              endif;?>
            <!--<input type="text" class="form-control" name="note[]" id="note">-->
          
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-note" class="btn btn-danger">Add another note</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="text-t" class="control-label">Text</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <?php
              $fieldName = 'text_t'; 
              if (array_key_exists($fieldName,$displayArray)):
                foreach ($displayArray[$fieldName] as $value):?>
                <div class="col-xs-10 col-xs-offset-2">
                <textarea name="text-t[]"><?php print $value;?></textarea>
                </div>
              <?php
                endforeach;
              else:?>
                <div class="col-xs-10 col-xs-offset-2">
                <textarea name="text-t[]"></textarea>
                </div>
              <?php
              endif;?>
            
          </div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-text" class="btn btn-danger">Add another text</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="Notes" class="control-label">Languages</label>
          </div>
          <?php
              $fieldName = 'language'; 
              if (array_key_exists($fieldName,$displayArray)):
                foreach ($displayArray[$fieldName] as $value):?>
                <div class="col-xs-10 col-xs-offset-2">
                <input type="text" value="<?php print $value;?>" class="form-control" name="language[]" id="language">
                </div>
              <?php
                endforeach;
              else:?>
              <div class="col-xs-10 col-xs-offset-2">
                <input type="text" class="form-control" name="language[]" id="language">
              </div>
              <?php
              endif;?>
          <!--<div class="col-xs-10 col-xs-offset-2">
            <input type="text" class="form-control" name="language[]" id="language" value="">
          </div>-->
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-language" class="btn btn-danger">Add another language</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="subject_heading" class="control-label">Subject Heading(s)</label>
          </div>
          <div class="col-xs-10" id="subject-headings-list">
            <?php foreach ($headings as $heading):?>
              <div><span>Subject heading: <b><input type="text" value="<?php print $heading?>" readonly name="subject_heading[]"></b></span><button class="btn btn-default btn-sm btn-rm-contributor">x</button></div>
            <?php endforeach;?>
          </div>

          <!-- Modal -->
            <div id="headingsModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
              <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add a subject heading</h4>
                  </div>
                  <div class="modal-body">

                    <div class="col-xs-12 contributor-form-group">
                      <div class="col-xs-10">
                        <input class="form-control awesomplete"  list="headings-list" name="heading_insert" id="heading_insert">
                        <?php require ("../data/subjectHeadingsList.php"); ?>
                      </div>
                      <div class="col-xs-2">
                        <button id="btn-insert-heading" class="btn btn-danger btn-sm">Add to record</button>
                      </div>
                    </div> 

                  </div>
                  <div class="modal-footer" style="margin-top:2em;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

          <div class="col-xs-10 col-xs-offset-2"></div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-heading" class="btn btn-danger" data-toggle="modal" data-target="#headingsModal">Add a subject heading</button></div>
        </div>


        <div class="form-group">
          <div class="col-xs-2">
            <label for="call-number" class="control-label">Call Number</label>
          </div>
          <div class="col-xs-10">
            <select class="form-control" name="call_number" id="call_number">
                          <option <?php if ($call_number=="Sheet music") print "selected";?>>Sheet music</option>
                          <option <?php if ($call_number=="Sheet music large") print "selected";?>>Sheet music large</option>
                          <option <?php if ($call_number=="CSAM") print "selected";?>>CSAM</option>
                        </select>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="series" class="control-label">Series</label>
          </div>
          <div class="col-xs-10">
            <input type="text" value="<?php print $series;?>" class="form-control" name="series" id="series">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="larger-work" class="control-label">Larger Work</label>
          </div>
          <div class="col-xs-10">
            <input type="text" value="<?php print $larger_work;?>" class="form-control" name="larger-work" id="larger-work">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="collection" class="control-label">Collection Source</label>
          </div>
          <div class="col-xs-10">
            <input type="text" value="<?php print $collection_source ;?>" class="form-control" name="collection" id="collection">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="donor" class="control-label">Donor</label>
          </div>
          <div class="col-xs-10">
            <input type="text" value="<?php print $donor;?>" class="form-control" name="donor" id="donor">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="scanning-tech" class="control-label">Scanning technician</label>
          </div>
          <div class="col-xs-10">
            <input type="text" value="<?php print $scanning_technician;?>" class="form-control" name="scanning-tech" id="scanning-tech">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-2">
            <label for="msg" class="control-label">Messages for supervisor</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <textarea name="msg"><?php print $admin_notes;?></textarea>
          </div>
        </div>

        

        <div class="form-group">
          <div class="col-xs-2">
            
          </div>
          <div class="col-xs-10">
            <input type="submit" class="form-control btn-success">
          </div>
        </div>

      </form>

      </div>
  </div>
</div> <!-- container-fluid -->
<?php 

require "../footer.php";

//require "layout/scripts.php";

?>