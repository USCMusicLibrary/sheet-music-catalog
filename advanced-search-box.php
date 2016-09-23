<div class="row">
		<div class="col-xs-12 text-center">
			<p><?php print $siteTitle;?></p>
		</div>
	<div class="col-xs-6 col-xs-offset-3">
		<div class="row center-block">
			<form class="form-horizontal" id="home-search" name="home-search" method="GET" action="index">

			<?php if($queryArray)://populate search terms in advanced search box
			foreach ($queryArray as $item):
			?>
	        <div class="row form-group search-row">
				<div class="col-xs-1 nopadding">
					<select class="form-control boolean-selector" name="op[]">
						<option value="AND"<?php if($item[1]=='AND') print ' selected=""';?>>AND</option>
						<option value="OR"<?php if($item[1]=='OR') print ' selected=""';?>>OR</option>
						<option value="NOT"<?php if($item[1]=='NOT') print ' selected=""';?>>NOT</option>
					</select>
				</div>
				<div class="col-xs-6 nopadding">
					<input type="text" name="q[]" class="form-control" placeholder="Search sheet music database" value="<?php print $item[2];?>">
				</div>
				<div class="col-xs-4 nopadding">
					<select class="form-control" name="f[]">
						<?php foreach ($advancedSearchFields as $key => $value):?>
						<option value="<?php print $key;?>"<?php if ($item[0]==$key) print ' selected=""';?>><?php print $value;?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="col-xs-1 nopadding">
					<button type="button" class="form-control close">x</button>
				</div>
				<div class="col-xs-12"></div>
			</div>
			<?php
			endforeach;
			else:?>
			<div class="row form-group search-row">
				<div class="col-xs-1 nopadding">
					<select class="form-control boolean-selector" name="op[]">
						<option value="AND" selected="">AND</option>
						<option value="OR">OR</option>
						<option value="NOT">NOT</option>
					</select>
				</div>
				<div class="col-xs-6 nopadding">
					<input type="text" name="q[]" class="form-control" placeholder="Search sheet music database">
				</div>
				<div class="col-xs-4 nopadding">
					<select class="form-control" name="f[]">
						<?php foreach ($advancedSearchFields as $key => $value):?>
						<option value="<?php print $key;?>"><?php print $value;?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="col-xs-1 nopadding">
					<button type="button" class="form-control close">x</button>
				</div>
				<div class="col-xs-12"></div>
			</div>
			<?php endif;?>


			<!--<div class="row form-group">
					<button type="button" class="btn btn-default" id="addRow">Add another search term</button>
			</div>-->

			<div class="row">
				<div class="col-xs-8">
					<!--<input type="checkbox" value="<?php //print isset($_GET['full-text-search']) ? $_GET['full-text-search']: 'false' ;?>" name="full-text-search" id="full-text-search">
					<label for="full-text-search" class="control-label">Search full text</label><br>-->
				</div>
				<div class="col-xs-4">
					<input type="submit" class="btn btn-primary" value="Search">
					<input type="hidden" name="form_submitted">
					<input type="hidden" name="start" value="0">
				</div>
			</div>
			</form>
		</div>
	</div>
</div><!-- row-->
	<br>
	<hr>
