<?php
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
/* this file contains navigation elements for the admin pages */
?>
<nav class="navbar navbar-inverse">
      <div class="container">
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?php print $ROOTURL;?>admin/add">Add records</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/account">Account (<?php print $_SESSION['username'];?>)</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/all">View own submissions</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/pending">Pending</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/approved">Approved</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Headings<span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="names-list">Names</a></li>
                  <li><a href="subject-headings">Subjects</a></li>
                </ul>
              </li>
            <li><a href="<?php print $ROOTURL;?>admin/users">Users</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/export">Export</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/authorityCheck">Authority check report</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/authorityCheckRun">Run authority check</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>