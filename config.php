<?php

/*
 config file for global values
*/

global $solrCoreName;
$solrCoreName = "sheet-music-catalog";

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
"publisher_location" => array("field_title" => "Publisher location",
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
)
);

global $briefDisplayFields;
foreach ($solrFieldNames as $name =>$info){
	if ($info['display']=='brief') $briefDisplayFields[] = $name;
}


global $facetFields;
$facetFields = array(
		"composer_facet" => "Composer",
		"publisher_facet" => "Publisher",
		"publisher_location_facet" => "Publisher Location",
		"subject_heading_facet" => "LC Subject Headings",
		"language" => "Language"
		//"date"
);

global $searchFields;
$searchFields = array(
			'title',
'alternative_title',
'publisher',
'publisher_location',
//'year',
'language',
'text_t',
'notes',
'donor',
'subject_heading',
'call_number',
'series',
'larger_work'
	);

global $advancedSearchFields;
$advancedSearchFields = array (
	"all" => "Search all fields",
	"title" => "Title (title and alternative title fields)",
	"contributor" => "Contributors (authors, editors, etc.)",
	"publisher" => "Publisher",
	"larger" => "From Larger Work",
	"collection" => "Collection",
	"Donor" => "Donor",
	"call" => "Call Number",
	"text" => "Text",
	"notes" => "Notes",
	"subject" => "LC Subject Headings"
);

global $siteTitle;
$siteTitle = "";
