<?php
/* this file contains navigation elements for the admin pages */
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="$ROOTURL/admin/index">Start</a></li>
            <li><a href="$ROOTURL/admin/all">All</a></li>
            <?php if (isLoggedIn()):?><li><a href="logout">Log out</a></li><?php endif;?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>