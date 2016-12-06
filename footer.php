


  </div> <!-- /container-fluid -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
    <script src="<?php print $ROOTURL;?>js/jquery.ui.touch-punch.min.js"></script>


    <script src="<?php print $ROOTURL;?>bootstrap/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>-->
    <script src="<?php print $ROOTURL;?>js/search.js"></script>
    <script src="<?php print $ROOTURL;?>js/admin.js"></script>
    <script src="<?php print $ROOTURL;?>js/awesomplete.js" async></script>

    <script>
var defaultText = 'Click me and enter some text';

function endEdit(e) {
    var input = $(e.target),
        label = input && input.prev();

    label.text(input.val() === '' ? defaultText : input.val());
    input.hide();
    label.show();
}

$('.clickedit').hide()
.focusout(endEdit)
.keyup(function (e) {
    if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
        endEdit(e);
        return false;
    } else {
        return true;
    }
})
.prev().click(function () {
    //alert($(this).html());
    $(this).hide();
    $(this).next().show().focus().val($(this).html());
});


</script>

  </body>
</html>
