/****
javascript functions for search page
***/
/* 
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
*/
/** Global variables*/

var carouselIndex = 0;

/**
 *
 * @param {HTML DOM Event} e: The event happening.
 */
$("#addRow").click(function (e) {
	var row = $(".search-row:first").clone();


	//alert (row);

	//row.removeAttr("id");

	//alert (row);

	row.insertBefore($(this).parent());
	//row.show();
	$(".boolean-selector").show();
	$(".boolean-selector:first").hide();
	$(".form-control.close").show();


  /*var section = $(this).parentsUntil("section").parent();
  var group   = section.find("select[name='role[]']").last().parent().parent().clone();
  var newID   = increaseID(group, "select");

  group.find("select").prop("id", newID).find("> option:selected").removeAttr("selected").parent().find("> option:first-child").prop("selected", "true");
  group.find("label").attr("for", newID);

  $(group).insertBefore($(this).parent().parent());
  group.show();

  group = section.find("input[name='role_value[]']").last().parent().parent().clone();
  newID = increaseID(group, "input");

  group.find("input").prop("id", newID).val("");
  group.find("label").attr("for", newID);

  $(group).insertBefore($(this).parent().parent());
  group.show();

  section.find(".close.hide").removeClass("hide");*/

  e.target.blur();
});


$("#home-search").on("click", ".close", function (e) {
	//alert("close");
	$(this).parent().parent().remove();

	if ($(".close").length == 1){
		$(".close:first").hide();
	}

	$(".boolean-selector:first").hide();

  e.target.blur();
});



$(document).ready(function () {

	//alert (currentQuery);
	$(".boolean-selector").show();
	$(".boolean-selector:first").hide();
	$(".form-control.close").show();
	if ($(".close").length == 1){
		$(".close:first").hide();
	}

	try {
    minYear;
		maxYear;
		rMinYear;
		rMaxYear;
} catch(err) {
    // caught the reference error
    // code here will execute **only** if variable was never declared
		minYear=0;
		maxYear=2000;
		rMinYear=0;
		rMaxYear=2000;
	}
	//this iterates through the disableArray
	//defined in search-results.php
	//each function in the array hides a 'more' button
	//if there is no more information to display
	if(typeof disableArray != 'undefined' && disableArray != null){
      for(var i in disableArray){
	    disableArray[i]();
	  }
  }

	$( function() {
    $( "#slider-range" ).slider({
      range: true,
      min: minYear,
      max: maxYear,
      values: [ rMinYear, rMaxYear ],
      slide: function( event, ui ) {
        $( "#amount" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
      },
	  change: function (event, ui) {
			var newMin = ui.values[0];
			var newMax = ui.values[1];

			currentQuery = currentQuery+'&fq[]=['+newMin +'+TO+'+ newMax+']&fq_field[]=years&'+'rMinYear='+newMin+'&rMaxYear='+newMax;
		  console.log (currentQuery);
			window.location = currentQuery;
	  }
    });
    $( "#amount" ).val($( "#slider-range" ).slider( "values", 0 ) +
      " - " + $( "#slider-range" ).slider( "values", 1 ) );
  } );


});

$(".accordion-toggle").click(function (e) {
	$(this).toggleClass("accordion-opened");
});

$('#carouselModal').on('shown.bs.modal', function() {
	//alert(carouselIndex);
    $("#musicCarousel").carousel(carouselIndex);
});
 

window.addEventListener("awesomplete-selectcomplete", function(e){
  // User made a selection from dropdown. 
  // This is fired before the selection is applied
  var name = $("#nameInput").val();
  window.location = "index?op%5B0%5D=AND&q%5B0%5D=%2A&f%5B0%5D=all&form_submitted=&start=0&fq[]=%22"+name+"%22&fq_field[]="+browseFacet;
  //alert(e.text);
}, false);