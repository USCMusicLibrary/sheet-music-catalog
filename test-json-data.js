[
  {
    "id" :  "",
    "archive" :  "",
    "contributing_institution" :  "",
    "url" :  "",
    "title" :  "",
    "type_content" :  "",
    "type_digital" :  "",
    "role" :  "",
    "geolocation_human" :  "",
    "file_format" :  "",
    "alternative_title" :  "",
    "thumbnail_url" :  "",
    "description" :  "",
  },
  {}
]

/*
<field name="id" type="string" indexed="true" stored="true" required="true" multiValued="false" /> 
   
   
   <!-- 
   Fields for Digital US South Indexing Project
   -->
   
   <!-- Required Fields -->
   <!-- Archive -->
   <field name="archive" type="text_general" indexed="true" stored="true" required="true" multiValued="false" />
   
   <!-- Contributing Institution -->   
   <field name="contributing_institution" type="text_general" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- URL -->
   <field name="url" type="string" indexed="true" stored="true" required="true" multiValued="false" />
   
   <!-- Title -->
   <field name="title" type="text_en_splitting" indexed="true" stored="true" required="true" multiValued="false" />
   
   
   <!-- Type of Content -->
   <field name="type_content" type="string" indexed="true" stored="true" required="true" multiValued="false" />
   
   <!-- Type of Digital artifact -->
   <field name="type_digital" type="string" indexed="true" stored="true" required="true" multiValued="false" />
   
   <!-- Roles Dynamic field
   This field is used for all "role" fields
   Examples:
   role_aut (author)
   role_dsr (designer)
   full list at http://www.loc.gov/marc/relators/relaterm.html
   -->
   <dynamicField name="role_*" type="string" indexed="true" stored="true" multiValued="true"/>
   <!-- The following field is used for querying all role fields -->
   <field name="role_ALL"  type="string" indexed="true"  stored="true" multiValued="true"/>
   <copyField source="role_*"    dest="role_ALL"/>
   
   <!-- Date of original artifact 
   These fields are used for date searching.
   To indicate a date range specify the beginning and end dates.
   To indicate a single date, use the same value for both.
   -->
   <!-- Date range begin -->
   <field name="year_begin" type="int" indexed="true" stored="true" multiValued="false"/>
   <field name="month_begin" type="int" indexed="true" stored="true" multiValued="false"/>
   <field name="day_begin" type="int" indexed="true" stored="true" multiValued="false"/>
   <!-- Date range end -->
   <field name="year_end" type="int" indexed="true" stored="true" multiValued="false"/>
   <field name="month_end" type="int" indexed="true" stored="true" multiValued="false"/>
   <field name="day_end" type="int" indexed="true" stored="true" multiValued="false"/>
   <!-- years field - used for date range searching 
   Includes all the years >= year_begin && <= year_end
   -->
   <field name="years" type="tint" indexed="true" stored="false" multiValued="true"/>

   <!--Date of digital surrogate -->
   <field name="year_digital" type="int" indexed="true" stored="true" multiValued="false"/>
   <field name="month_digital" type="int" indexed="true" stored="true" multiValued="false"/>
   <field name="day_digital" type="int" indexed="true" stored="true" multiValued="false"/>
   
   <!-- Geographic Location - Human -->
   <field name="geolocation_human" type="text_general" indexed="true" stored="true" required="true" multiValued="true" />
   
   <!-- File Format -->
   <field name="file_format" type="string" indexed="true" stored="true" required="true" multiValued="false" />
   
   
   <!-- Optional Fields -->
   
   <!-- Alternative Title(s) -->
   <!-- TODO: finalize text field type decisions -->
   <field name="alternative_title" type="text_en_splitting" indexed="true" stored="true" required="false" multiValued="true" />
   
   <!-- Thumbnail URL -->
   <field name="thumbnail_url" type="string" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- Description -->
   <field name="description" type="text_en_splitting" indexed="true" stored="true" required="true" multiValued="false" />
   
   <!-- Full Text -->
   <field name="full_text" type="text_en" indexed="true" stored="false" required="false" multiValued="false" />
   
   <!-- Type of Physical Artifact -->
   <field name="type_physical" type="text_en" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- Date of Original Artifact - Human readable -->
   <field name="date_original" type="text_en" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- Date of Digital Surrogate - Human readable -->
   <field name="date_digital" type="text_en" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- Geographic Location - Machine -->
   <field name="geolocation_machine" type="location" indexed="true" stored="true" required="false" multiValued="true" />
   
   <!-- Source/Shelfmark -->
   <field name="shelfmark" type="string" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- LC Subject Headings -->
   <field name="subject_heading" type="text_en_splitting" indexed="true" stored="true" required="false" multiValued="true" />
   
   <!-- Extent/Size/Duration -->
   <field name="extent" type="string" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- Copyright holder -->
   <field name="copyright_holder" type="string" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- Use Permissions -->
   <field name="use_permissions" type="string" indexed="true" stored="true" required="false" multiValued="false" />
   
   <!-- Language -->
   <field name="language" type="string" indexed="true" stored="true" required="false" multiValued="true" />
   
   <!-- Notes -->
   <field name="notes" type="text_en" indexed="true" stored="true" required="false" multiValued="false" />
   
*/