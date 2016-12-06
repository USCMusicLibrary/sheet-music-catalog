$(document).ready(function (e){

});

$("#btn-add-contributor").click(function(e){
    e.preventDefault();
    console.log("add contributor");
    var formGroup = $(this).parent().parent().find(".contributor-form-group.collapse").clone();
    formGroup.insertBefore($(this).parent());
    formGroup.show('slow', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});