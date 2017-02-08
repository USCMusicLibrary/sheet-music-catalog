<?php

/*
 config file for global values
*/

global $solrCoreName;
$solrCoreName = "sheet-music-catalog";

global $ROOTURL;
$ROOTURL = "http://localhost/catalog/";
//$ROOTURL = "http://129.252.210.237/catalog/";

global $solrUrl;
$solrUrl = 'http://localhost:8983/solr/'.$solrCoreName.'/';

global $solrResultsHighlightTag;
$solrResultsHighlightTag = "mark";//bootstrap highlight <mark></mark>

global $lastError;
$lastError = '';


global $solrFieldNames;
$solrFieldNames = array(
"url" => array("field_title" => "ID",
	"display" => "full"
),
"id" => array("field_title" => "ID",
	"display" => "full"
),
"title" => array("field_title" => "Title",
	"display" => "full"
),
"alternative_title" => array("field_title" => "Alternate title",
	"display" => "full"
),
"composer" => array("field_title" => "Composer",
	"display" => "brief"
),
"lyricist" => array("field_title" => "Lyricist",
	"display" => "full"
),
"arranger" => array("field_title" => "Arranger",
	"display" => "full"
),
"publisher" => array("field_title" => "Publisher",
	"display" => "brief"
),
"illustrator" => array("field_title" => "Illustrator",
	"display" => "full"
),
"photographer" => array("field_title" => "Photographer",
                      "display" => "full"
),
"editor" => array("field_title" => "Editor",
                      "display" => "full"
),
"publisher_location" => array("field_title" => "Publisher location",
	"display" => "full"
),
"years_text" => array("field_title" => "Date",
	"display" => "full"
),
"years" => array("field_title" => "Date",
	"display" => "full"
),
"language" => array("field_title" => "Language",
	"display" => "full"
),
"notes" => array("field_title" => "Note",
	"display" => "full"
),
"donor" => array("field_title" => "Donor",
	"display" => "full"
),
"subject_heading" => array("field_title" => "LC subject heading",
	"display" => "full"
),
"call_number" => array("field_title" => "Call number",
	"display" => "brief"
),
"series" => array("field_title" => "Series",
	"display" => "full"
),
"larger_work" => array("field_title" => "Larger work",
	"display" => "full"
),
"text_t" => array("field_title" => "Text",
	"display" => "full"
),
"collection_source" => array("field_title" => "Collection Source",
	"display" => "full"
),
"has_image" => array("field_title" => "Online score",
	"display" => "full"
)
);

global $briefDisplayFields;
foreach ($solrFieldNames as $name =>$info){
	if ($info['display']=='brief') $briefDisplayFields[] = $name;
}


global $facetFields;
$facetFields = array(
		"composer_facet" => "Composer",
		"lyricist_facet" => "Lyricist",
		"arranger_facet" => "Arranger",
		"illustrator_facet"=> "Illustrator",
		"photographer_facet" => "Photographer",
                "editor_facet"  => "Editor",
		"publisher_facet" => "Publisher",
		"publisher_location_facet" => "Publisher Location",
		"subject_heading_facet" => "LC Subject Headings",
		"language" => "Language",
		"has_image" => "Availability"
		//"date"
);

global $searchFields;
$searchFields = array(
		'title',
'alternative_title',
'publisher',
'publisher_location',
'composer',
'lyricist',
'arranger',
'illustrator',
'photographer',
'editor',
'years_text',
'language',
'text_t',
'notes',
'donor',
'subject_heading',
'call_number',
'series',
'larger_work',
'collection_source'
);

global $advancedSearchFields;
$advancedSearchFields = array (
	"all" => "Search all fields",
	"title" => "Title (title and alternative title fields)",
	"contributor" => "Contributors (authors, editors, etc.)",
	"publisher" => "Publisher",
	"larger_work" => "From Larger Work",
	"collection_source" => "Collection",
	"donor" => "Donor",
	"call_number" => "Call Number",
	"text_t" => "Text",
	"notes" => "Notes",
	"subject_heading" => "LC Subject Headings"
);

global $siteTitle;
$siteTitle = "";

global $contribtypes;
//Supported MARC relator terms
$contribtypes = array(
    'composer' => 0, 
    'lyricist' => 1, 
    'arranger' => 2,
    'illustrator' => 3, 
    'editor' => 4, 
    'photographer' =>5, 
    'other_contributor' => 6
    );

global $other_heading_types;
//Non-lcnaf controlled vocabularies for which we are recording URIs:
$other_heading_types = array('subject_heading');
