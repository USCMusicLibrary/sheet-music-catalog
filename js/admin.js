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

$("#btn-add-alt-title").click(function(e){

    e.preventDefault();
    console.log("add alt title");
    var formGroup = $(this).parent().prev().clone();
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('slow', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$('#daterange').daterangepicker({
    "locale": {
        "format": "YYYY",
        "separator": " - ",
        "applyLabel": "Apply",
        "cancelLabel": "Cancel",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Custom",
        "weekLabel": "W",
        "daysOfWeek": [
            "Su",
            "Mo",
            "Tu",
            "We",
            "Th",
            "Fr",
            "Sa"
        ],
        "monthNames": [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ],
        "firstDay": 1
    },
    "startDate": "02/23/2017",
    "endDate": "03/01/2017"
}, function(start, end, label) {
  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
});