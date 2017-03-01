<?php
//index for admin part
require "../header.php";


require "../functions.php";

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h2>Submission form</h2>
        <form class="form-horizontal" action="submit" method="POST">
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
                        <select class="form-control" name="contributor_type_insert" id="contributor_type_insert"required="">
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
            <label for="publisher" class="control-label">Publisher</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="publisher" id="publisher" required="">
          </div>
        </div>

        <!--<div class="form-group">
          <div class="col-xs-2">
            <label for="publisher_location" class="control-label">Publisher Location</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="publisher_location" id="publisher_location" required="">
          </div>
        </div>-->

        <div class="form-group">
          <div class="col-xs-2">
            <label for="year" class="control-label">Copyright Year</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="year" id="year" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="text-t" class="control-label">Text</label>
          </div>
          <div class="col-xs-10">
            <textarea name="text-t"></textarea>
          </div>
        </div>

        <!--<div class="form-group">
          <div class="col-xs-2">
            <label for="language" class="control-label">Language</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="language" id="language" required="">
          </div>
        </div>-->

        <div class="form-group">
          <div class="col-xs-2">
            <label for="call-number" class="control-label">Call Number</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="call-number" id="number" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="series" class="control-label">Series</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="series" id="series" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="larger-work" class="control-label">Larger Work</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="larger-work" id="larger-work" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="collection" class="control-label">Collection Source</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="collection" id="collection" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="donor" class="control-label">Donor</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="donor" id="donor" required="">
          </div>
        </div>

        <!--<div class="form-group">
          <div class="col-xs-2">
            <label for="subject_heading" class="control-label">Subject heading</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="subject_heading[]" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="notes" class="control-label">Notes</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="notes" id="notes" required="">
          </div>
        </div>-->

        <div class="form-group">
          <div class="col-xs-2">
            <label for="scanning-tech" class="control-label">Scanning technician</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="scanning-tech" id="scanning-tech" required="">
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