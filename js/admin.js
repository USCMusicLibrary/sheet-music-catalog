var contributorsObject = [];

$(document).ready(function (e){

});

$("#btn-add-contributor").click(function(e){

    e.preventDefault();
    return;
    console.log("add contributor");
    var formGroup = $(this).parent().parent().find(".contributor-form-group.collapse").clone();
    formGroup.insertBefore($(this).parent());
    formGroup.show('slow', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$("#btn-insert-contributor").click(function(e){

    e.preventDefault();
    console.log("insert contributor");
    var cName = $("#contributor_insert").val();
    var cType = $("#contributor_type_insert").val();
    contributor = [cType,cName];
    contributorsObject.push(contributor);
    //console.log(contributorsObject);
    //return;
    $.post(
        "contributors-div",
        {data:contributorsObject},
        function( data ) {
            $("#contributors-list").replaceWith( data );
        },
        "html"
    );
    $("#contributorModal").modal('hide');
});

$(document).on("click",".btn-rm-contributor",function(e){
    e.preventDefault();
});