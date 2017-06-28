//var contributorsObject = [];
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
    contributorsObject = contributor;
    //console.log(contributorsObject);
    //return;
    var html = "<div><span>"+cType+": <b><input type=\"text\" value=\""+cName+"\" readonly name=\""+cType.toLowerCase()+"[]\"></b></span><button class=\"btn btn-default btn-sm btn-rm-contributor\">x</button></div>";

    $("#contributorModal").modal('hide');
    //insertContributor(cType,cName,$(this).parent());
    $("#contributor_insert").val("");

    $("#contributors-list").append(html);

});

$("#btn-add-heading").click(function(e){

    e.preventDefault();

});
$("#btn-insert-heading").click(function(e){

    e.preventDefault();
    console.log("insert heading");
    var cName = $("#heading_insert").val();

    //contributor = ["Subject Heading",cName];
    //subjectHeadingsObject.push(contributor);

    var html = "<div><span>Subject heading: <b><input type=\"text\" value=\""+cName+"\" readonly name=\"subject_heading[]\"></b></span><button class=\"btn btn-default btn-sm btn-rm-contributor\">x</button></div>";

    $("#headingsModal").modal('hide');
    $("#heading_insert").val("");

    $("#subject-headings-list").append(html);
});

$(document).on("click",".btn-rm-contributor",function(e){
    e.preventDefault();
    $(this).parent().remove();
});
$(document).on("click",".btn-rm-alt-title",function(e){
    e.preventDefault();
    $(this).parent().remove();
    altTitleCounter--;
});
$(document).on("click",".btn-rm-note",function(e){
    e.preventDefault();
    $(this).parent().remove();
    noteCounter--;
});
$(document).on("click",".btn-rm-text",function(e){
    e.preventDefault();
    $(this).parent().remove();
    textCounter--;
});
$(document).on("click",".btn-rm-pub",function(e){
    e.preventDefault();
    $(this).parent().remove();
    pubCounter--;
});
$(document).on("click",".btn-rm-publoc",function(e){
    e.preventDefault();
    $(this).parent().remove();
    publocCounter--;
});
$(document).on("click",".btn-rm-lang",function(e){
    e.preventDefault();
    $(this).parent().remove();
    langCounter--;
});

var altTitleCounter = 1;
$("#btn-add-alt-title").click(function(e){

    e.preventDefault();
    console.log("add alt title");
    var formGroup = $(this).parent().prev().clone();
    if (altTitleCounter ==1)$("<button class=\"btn btn-default btn-sm btn-rm-alt-title\">x</button>").insertAfter(formGroup.find("input"));
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
    altTitleCounter++;
});

var noteCounter = 1;
$("#btn-add-note").click(function(e){

    e.preventDefault();
    console.log("add note");
    var formGroup = $(this).parent().prev().clone();
    if (noteCounter ==1)$("<button class=\"btn btn-default btn-sm btn-rm-note\">x</button>").insertAfter(formGroup.find("input"));
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
    noteCounter++;
});

var textCounter = 1;
$("#btn-add-text").click(function(e){

    e.preventDefault();
    console.log("add text");
    var formGroup = $(this).parent().prev().clone();
    if (textCounter ==1)$("<button class=\"btn btn-default btn-sm btn-rm-text\">x</button>").insertAfter(formGroup.find("textarea"));
    formGroup.find("textarea").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
    textCounter++;
});

var langCounter = 1;
$("#btn-add-language").click(function(e){

    e.preventDefault();
    console.log("add language");
    var formGroup = $(this).parent().prev().clone();
    if (langCounter ==1)$("<button class=\"btn btn-default btn-sm btn-rm-lang\">x</button>").insertAfter(formGroup.find("input"));
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
    langCounter++;
});

var pubCounter = 1;
$("#btn-add-publisher").click(function(e){
    e.preventDefault();
    console.log("add publisher");
    var formGroup = $(this).parent().prev().clone();
    if (pubCounter ==1)$("<button class=\"btn btn-default btn-sm btn-rm-note\">x</button>").insertAfter(formGroup.find("input"));
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
    pubCounter++;
});

var publocCounter = 1;

$("#btn-add-pub-loc").click(function(e){
    e.preventDefault();
    console.log("add publisher location");
    var formGroup = $(this).parent().prev().clone();
    if (publocCounter ==1)$("<button class=\"btn btn-default btn-sm btn-rm-publoc\">x</button>").insertAfter(formGroup.find("input"));
    formGroup.find("input").val("");
    formGroup.toggleClass("collapse");
    formGroup.insertBefore($(this).parent());
    formGroup.show('fast', function() {
            console.log("show");
        });
    formGroup.toggleClass("collapse");
    publocCounter++;
});

$("#btn-add-heading").click(function(e){

    e.preventDefault();
    console.log("add heading");
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


//functions for editing records
$(".clickedit")