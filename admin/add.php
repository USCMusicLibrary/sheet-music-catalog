<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
//add record page

session_start();
if (!$_SESSION['logged-in']){
    header("Location: login");
    die();
}


require "../header.php";


require_once "../functions.php";

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h2>Submission form</h2>
        <form class="form-horizontal" action="submit" method="POST" id="recordForm" name="recordForm">
<input type="hidden" name="cataloguer_id" value="<?php print $_SESSION['user_id']?>">
<input type="hidden" name="id" value="0">


        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Title</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="alt-title" class="control-label">Alternative Title</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <input type="text" class="form-control" name="alt-title[]" id="alt-title">
          </div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-alt-title" class="btn btn-danger">Add another alternative title</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="contributor_type" class="control-label">Contributor(s)</label>
          </div>
          <div class="col-xs-10" id="contributors-list"></div>

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
                      <div class="col-xs-12">
                        <a href="names-list" target="_blank">View list of names</a>
                      </div>
                      <div class="clearfix"></div>
                    </div>

                  </div>
                  <div class="modal-footer" style="margin-top:3em;">
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
          <div class="col-xs-10 col-xs-offset-2">
            <input type="text" class="form-control" name="publisher[]" id="pub">
          </div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-publisher" class="btn btn-danger">Add another publisher</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="publisher_location" class="control-label">Publisher Location</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <input type="text" class="form-control" name="publisher_location[]" id="pub_loc">
          </div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-pub-loc" class="btn btn-danger">Add another publisher location</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="year" class="control-label">Date</label>
          </div>
          <div class="col-xs-10">
            <!--<input type="text" class="form-control" name="year" id="daterange">-->
            <label for="year_start">Start Year</label><input type="text" class="form-control" name="year_start" id="year_start" required="">
            <label for="year_end">End Year</label><input type="text" class="form-control" name="year_end" id="year_end">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="Notes" class="control-label">Notes</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <input type="text" class="form-control" name="note[]" id="note">
          </div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-note" class="btn btn-danger">Add another note</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="text-t" class="control-label">Text</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <textarea name="text-t[]"></textarea>
          </div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-text" class="btn btn-danger">Add another text</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="Notes" class="control-label">Languages</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <input type="text" class="form-control" name="language[]" id="language" value="">
          </div>
          <div class="col-xs-10 col-xs-offset-2"><button id="btn-add-language" class="btn btn-danger">Add another language</button></div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="subject_heading" class="control-label">Subject Heading(s)</label>
          </div>
          <div class="col-xs-10" id="subject-headings-list"></div>

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
                      <div class="col-xs-12">
                        <a href="subject-headings" target="_blank">View list of subject headings</a>
                      </div>
                      <div class="clearfix"></div>
                    </div> 

                  </div>
                  <div class="modal-footer" style="margin-top:3em;">
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
                          <option>Sheet music</option>
                          <option>Sheet music large</option>
                          <option>CSAM</option>
                        </select>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="series" class="control-label">Series</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="series" id="series">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="larger-work" class="control-label">Larger Work</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="larger-work" id="larger-work">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="collection" class="control-label">Collection Source</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="collection" id="collection">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="donor" class="control-label">Donor</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="donor" id="donor">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="scanning-tech" class="control-label">Scanning technician</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="scanning-tech" id="scanning-tech">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-2">
            <label for="msg" class="control-label">Messages for supervisor</label>
          </div>
          <div class="col-xs-10 col-xs-offset-2">
            <textarea name="msg"></textarea>
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