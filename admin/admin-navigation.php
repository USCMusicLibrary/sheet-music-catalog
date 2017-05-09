<?php
/* this file contains navigation elements for the admin pages */
?>
<nav class="navbar navbar-inverse">
      <div class="container">
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?php print $ROOTURL;?>admin/index">Start</a></li>
            <li><a href="<?php print $ROOTURL;?>admin/all">All</a></li>
            <?php if (isLoggedIn()):?><li><a href="logout">Log out</a></li><?php endif;?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>