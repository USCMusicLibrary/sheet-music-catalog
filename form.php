<?php

require "header.php";


require "functions.php";

?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-8 col-xs-offset-2">
        <h2>Submission form</h2>
        <form class="form-horizontal">
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
            <label for="title" class="control-label">Alternative Title</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Contributor</label>
          </div>
          <div class="col-xs-5">
            <select class="form-control" id="type_of_content" name="type_of_content" required="">
              <option>Composer</option>
              <option>Lyricist</option>
              <option>Arranger</option>
              <option>Editor</option>
              <option>Illustrator</option>
              <option>Photographer</option>
              <option>Other</option>
                    <?php //printOptions(array("Text","Image")); ?>
            </select>
          </div>
          <div class="col-xs-5">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Publisher</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Publisher Location</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Copyright Year</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Text</label>
          </div>
          <div class="col-xs-10">
            <textarea></textarea>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Language</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Call Number</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Series</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Larger Work</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Collection Source</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-2">
            <label for="title" class="control-label">Donor</label>
          </div>
          <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" required="">
          </div>
        </div>

      </form>

      </div>
  </div>
</div> <!-- container-fluid -->
<?php

require "footer.php";

//require "layout/scripts.php";

?>
