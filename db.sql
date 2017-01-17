/* main table for records*/
CREATE TABLE records (
  id int(11) NOT NULL AUTO_INCREMENT,
  mid int(11),
  title text,
  publisher varchar(255),
  call_number varchar(255),
  series varchar(255),
  larger_work varchar(255),
  collection_source varchar(255),
  donor varchar(255),
  scanning_technician varchar(255),
  media_cataloguer varchar(255),
  reviewer varchar(255),
  PRIMARY KEY (id)
);

/* 
 * additional tables
 */
CREATE TABLE alternative_titles (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  alternative_title text,
  PRIMARY KEY (id)
);

CREATE TABLE years (
  record_id int(11),
  start_year varchar(10),
  end_year varchar(10)
);

CREATE TABLE notes (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11),
  note text,
  PRIMARY KEY (id)
);

CREATE TABLE contributors (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  contributor_id int(11) NOT NULL,
  role_id varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE roles (
  id int(11) NOT NULL AUTO_INCREMENT,
  role varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE tbl_name (
  name varchar(255),
  uri varchar(2000),,
  nameUpdate varchar(100),
  localID int(10) UNSIGNED NOT NULL
)

CREATE TABLE publisher_locations (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  publisher_location varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE texts (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  text_t text,
  PRIMARY KEY (id)
);

CREATE TABLE languages (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  language varchar(255),
  PRIMARY KEY (id)
);

CREATE TABLE subject_headings (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  subject_heading text,
  PRIMARY KEY (id)
);

CREATE TABLE hidden_subject_headings (
  id int(11) NOT NULL AUTO_INCREMENT,
  record_id int(11) NOT NULL,
  hidden_subject_heading text,
  PRIMARY KEY (id)
);
