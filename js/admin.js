var contributorsObject = [];
var subjectHeadingsObject = [];

$(document).ready(function (e){

});

function insertContributor(type,value,parent){
    var newElem = "<input type=\"hidden\" name=\""+type.toLowerCase() + "[]\" value=\""+ value+"\">";
    console.log(newElem);
    $(parent).after(newElem);
};


$("#btn-add-contributor").click(function(e){

    e.preventDefault();

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
    insertContributor(cType,cName,$(this).parent());
    $("#contributor_insert").val("");

});

$("#btn-add-heading").click(function(e){

    e.preventDefault();

});
$("#btn-insert-heading").click(function(e){

    e.preventDefault();
    console.log("insert heading");
    var cName = $("#heading_insert").val();

    contributor = ["Subject Heading",cName];
    subjectHeadingsObject.push(contributor);
    $.post(
        "headings-div",
        {data:subjectHeadingsObject},
        function( data ) {
            $("#subject-headings-list").replaceWith( data );
        },
        "html"
    );
    $("#headingsModal").modal('hide');
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
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$("#btn-add-note").click(function(e){

    e.preventDefault();
    console.log("add note");
    var formGroup = $(this).parent().prev().clone();
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$("#btn-add-text").click(function(e){

    e.preventDefault();
    console.log("add text");
    var formGroup = $(this).parent().prev().clone();
    formGroup.find("textarea").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$("#btn-add-language").click(function(e){

    e.preventDefault();
    console.log("add language");
    var formGroup = $(this).parent().prev().clone();
    formGroup.find("textarea").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$("#btn-add-publisher").click(function(e){
    e.preventDefault();
    console.log("add publisher");
    var formGroup = $(this).parent().prev().clone();
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$("#btn-add-pub-loc").click(function(e){
    e.preventDefault();
    console.log("add publisher location");
    var formGroup = $(this).parent().prev().clone();
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
});

$("#btn-add-heading").click(function(e){

    e.preventDefault();
    console.log("add language");
    var formGroup = $(this).parent().prev().clone();
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
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